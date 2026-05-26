<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * ProductionOrder ActiveRecord model.
 *
 * @property int         $id
 * @property string      $reference      e.g. PRD-2024-00001
 * @property int|null    $order_id       optional sales order link
 * @property int         $product_id
 * @property int         $planned_qty
 * @property string      $status         planned|in_progress|completed|cancelled
 * @property string|null $planned_date
 * @property string|null $completed_at
 * @property string|null $notes
 * @property int|null    $created_by
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class ProductionOrder extends ActiveRecord
{
    const STATUS_PLANNED     = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_CANCELLED   = 'cancelled';

    const TRANSITIONS = [
        self::STATUS_PLANNED     => [self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
        self::STATUS_IN_PROGRESS => [self::STATUS_COMPLETED,   self::STATUS_CANCELLED],
        self::STATUS_COMPLETED   => [],
        self::STATUS_CANCELLED   => [],
    ];

    public static function tableName(): string
    {
        return 'production_order';
    }

    public function rules(): array
    {
        return [
            [['product_id', 'planned_qty'], 'required'],
            [['product_id', 'planned_qty', 'order_id', 'created_by'], 'integer'],
            [['planned_qty'], 'integer', 'min' => 1],
            [['notes'], 'string'],
            [['planned_date'], 'safe'],
            [['status'], 'in', 'range' => [
                self::STATUS_PLANNED, self::STATUS_IN_PROGRESS,
                self::STATUS_COMPLETED, self::STATUS_CANCELLED,
            ]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'           => 'ID',
            'reference'    => 'Reference',
            'order_id'     => 'Sales Order',
            'product_id'   => 'Product',
            'planned_qty'  => 'Planned Qty',
            'status'       => 'Status',
            'planned_date' => 'Planned Date',
            'completed_at' => 'Completed',
            'notes'        => 'Notes',
            'created_by'   => 'Created By',
            'created_at'   => 'Created',
            'updated_at'   => 'Updated',
        ];
    }

    // Reference number generation

    public function beforeSave($insert): bool
    {
        if ($insert && empty($this->reference)) {
            $this->reference    = $this->generateReference();
            $this->created_by   = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }

    private function generateReference(): string
    {
        $year = date('Y');
        $last = static::find()
            ->andWhere(['like', 'reference', "PRD-{$year}-"])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $seq = $last ? ((int) substr($last->reference, -5)) + 1 : 1;
        return sprintf('PRD-%s-%05d', $year, $seq);
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['production_order.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Status workflow

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            $this->addError('status', "Cannot move from \"{$this->status}\" to \"{$newStatus}\".");
            return false;
        }

        $old          = $this->status;
        $this->status = $newStatus;

        if ($newStatus === self::STATUS_COMPLETED) {
            $this->completed_at = date('Y-m-d H:i:s');
        }

        if (!$this->save(false)) {
            return false;
        }

        AuditLog::record('production_order.status_change', 'ProductionOrder', $this->id,
            ['status' => $old], ['status' => $newStatus]);

        return true;
    }

    /**
     * Auto-complete the production order when all its batches are done.
     * Called from Batch::transitionTo('completed').
     */
    public function checkAutoComplete(): void
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            return;
        }

        $unfinished = Batch::find()
            ->where(['production_order_id' => $this->id])
            ->andWhere(['!=', 'status', Batch::STATUS_COMPLETED])
            ->count();

        if ($unfinished == 0) {
            $this->transitionTo(self::STATUS_COMPLETED);
        }
    }

    // Relationships

    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getSalesOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getCreatedByUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getBatches(): ActiveQuery
    {
        return $this->hasMany(Batch::class, ['production_order_id' => 'id'])
                    ->andWhere(['batch.deleted_at' => null])
                    ->orderBy(['batch.created_at' => SORT_ASC]);
    }

    // Helpers

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PLANNED     => 'Planned',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED   => 'Completed',
            self::STATUS_CANCELLED   => 'Cancelled',
        ];
    }

    public static function statusBadgeClass(): array
    {
        return [
            self::STATUS_PLANNED     => 'bg-secondary',
            self::STATUS_IN_PROGRESS => 'bg-warning text-dark',
            self::STATUS_COMPLETED   => 'bg-success',
            self::STATUS_CANCELLED   => 'bg-danger',
        ];
    }

    public function getStatusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusBadge(): string
    {
        $class = self::statusBadgeClass()[$this->status] ?? 'bg-secondary';
        return "<span class=\"badge {$class}\">{$this->getStatusLabel()}</span>";
    }
}
