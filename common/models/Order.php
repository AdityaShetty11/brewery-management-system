<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * Order ActiveRecord model.
 *
 * @property int         $id
 * @property string      $order_number
 * @property int         $customer_id
 * @property int|null    $company_id
 * @property string      $status
 * @property float       $total_amount
 * @property string|null $notes
 * @property string|null $confirmed_at
 * @property string|null $delivered_at
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class Order extends ActiveRecord
{
    // Status constants — mirror the DB ENUM exactly

    const STATUS_DRAFT         = 'draft';
    const STATUS_CONFIRMED     = 'confirmed';
    const STATUS_IN_PRODUCTION = 'in_production';
    const STATUS_DELIVERED     = 'delivered';
    const STATUS_CANCELLED     = 'cancelled';

    // Valid forward transitions for each status

    const TRANSITIONS = [
        self::STATUS_DRAFT         => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED     => [self::STATUS_IN_PRODUCTION, self::STATUS_CANCELLED],
        self::STATUS_IN_PRODUCTION => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
        self::STATUS_DELIVERED     => [],
        self::STATUS_CANCELLED     => [],
    ];

    public static function tableName(): string
    {
        return 'order';
    }

    public function rules(): array
    {
        return [
            [['customer_id'], 'required'],
            [['customer_id', 'company_id'], 'integer'],
            [['total_amount'], 'number', 'min' => 0],
            [['notes'], 'string'],
            [['status'], 'in', 'range' => self::allStatuses()],
            [['confirmed_at', 'delivered_at'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'           => 'ID',
            'order_number' => 'Order #',
            'customer_id'  => 'Customer',
            'company_id'   => 'Company',
            'status'       => 'Status',
            'total_amount' => 'Total',
            'notes'        => 'Notes',
            'confirmed_at' => 'Confirmed',
            'delivered_at' => 'Delivered',
            'created_at'   => 'Placed',
            'updated_at'   => 'Last Updated',
        ];
    }

    // Soft delete — global scope

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['order.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Order number generation — called before insert

    public function beforeSave($insert): bool
    {
        if ($insert && empty($this->order_number)) {
            $this->order_number = $this->generateOrderNumber();
        }
        return parent::beforeSave($insert);
    }

    private function generateOrderNumber(): string
    {
        $year  = date('Y');
        $last  = static::find()
            ->andWhere(['like', 'order_number', "ORD-{$year}-"])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->order_number);
            $seq   = ((int) end($parts)) + 1;
        }

        return sprintf('ORD-%s-%05d', $year, $seq);
    }

    // Status workflow

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? [], true);
    }

    /**
     * Transitions the order to a new status and handles side effects.
     * Returns false with an error message if the transition is not allowed.
     */
    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            $this->addError('status', "Cannot move from \"{$this->status}\" to \"{$newStatus}\".");
            return false;
        }

        $oldStatus   = $this->status;
        $this->status = $newStatus;

        if ($newStatus === self::STATUS_CONFIRMED) {
            $this->confirmed_at = date('Y-m-d H:i:s');
        }

        if ($newStatus === self::STATUS_DELIVERED) {
            $this->delivered_at = date('Y-m-d H:i:s');
            $this->deductStock();
        }

        if (!$this->save(false)) {
            return false;
        }

        AuditLog::record(
            'order.status_change',
            'Order',
            $this->id,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );

        return true;
    }

    /**
     * Deducts finished goods stock when an order is marked delivered.
     */
    private function deductStock(): void
    {
        foreach ($this->items as $item) {
            $item->product->adjustStock(
                -$item->quantity,
                'out',
                'order',
                $this->id,
                "Delivered with order {$this->order_number}"
            );
        }
    }

    // Total recalculation — call after any item add/remove

    public function recalculateTotal(): bool
    {
        $this->total_amount = (float) OrderItem::find()
            ->where(['order_id' => $this->id])
            ->andWhere(['deleted_at' => null])
            ->sum('subtotal') ?? 0;

        return $this->save(false);
    }

    // Relationships

    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    public function getCompany(): ActiveQuery
    {
        return $this->hasOne(CustomerCompany::class, ['id' => 'company_id']);
    }

    public function getItems(): ActiveQuery
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id'])
                    ->andWhere(['order_item.deleted_at' => null]);
    }

    // Helpers

    public static function allStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_CONFIRMED,
            self::STATUS_IN_PRODUCTION,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT         => 'Draft',
            self::STATUS_CONFIRMED     => 'Confirmed',
            self::STATUS_IN_PRODUCTION => 'In Production',
            self::STATUS_DELIVERED     => 'Delivered',
            self::STATUS_CANCELLED     => 'Cancelled',
        ];
    }

    public static function statusBadgeClass(): array
    {
        return [
            self::STATUS_DRAFT         => 'bg-secondary',
            self::STATUS_CONFIRMED     => 'bg-primary',
            self::STATUS_IN_PRODUCTION => 'bg-warning text-dark',
            self::STATUS_DELIVERED     => 'bg-success',
            self::STATUS_CANCELLED     => 'bg-danger',
        ];
    }

    public static function statusIcons(): array
    {
        return [
            self::STATUS_DRAFT         => 'bi-pencil-square',
            self::STATUS_CONFIRMED     => 'bi-check2-circle',
            self::STATUS_IN_PRODUCTION => 'bi-gear-fill',
            self::STATUS_DELIVERED     => 'bi-truck',
            self::STATUS_CANCELLED     => 'bi-x-circle',
        ];
    }

    public function getStatusIcon(): string
    {
        return self::statusIcons()[$this->status] ?? 'bi-circle';
    }

    public function getStatusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusBadge(): string
    {
        $class = self::statusBadgeClass()[$this->status] ?? 'bg-secondary';
        $icon  = self::statusIcons()[$this->status]      ?? 'bi-circle';
        return "<span class=\"badge {$class}\"><i class=\"bi {$icon} me-1\"></i>{$this->getStatusLabel()}</span>";
    }

    public function getFormattedTotal(): string
    {
        return '$' . number_format((float) $this->total_amount, 2);
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
}
