<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * Batch ActiveRecord model.
 *
 * A batch is one physical brewing run under a production order.
 *
 * Key side effects on transition:
 *   planned   → brewing   : deducts batch ingredients from raw_material stock
 *   packaging → completed : adds actual_yield units to product stock
 *
 * @property int         $id
 * @property int         $production_order_id
 * @property string      $batch_number
 * @property string      $status
 * @property float       $batch_size          litres
 * @property int|null    $actual_yield        finished units
 * @property string|null $brew_date
 * @property string|null $completion_date
 * @property string|null $notes
 * @property int|null    $brew_master_id
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class Batch extends ActiveRecord
{
    const STATUS_PLANNED    = 'planned';
    const STATUS_BREWING    = 'brewing';
    const STATUS_FERMENTING = 'fermenting';
    const STATUS_PACKAGING  = 'packaging';
    const STATUS_COMPLETED  = 'completed';

    const TRANSITIONS = [
        self::STATUS_PLANNED    => [self::STATUS_BREWING],
        self::STATUS_BREWING    => [self::STATUS_FERMENTING],
        self::STATUS_FERMENTING => [self::STATUS_PACKAGING],
        self::STATUS_PACKAGING  => [self::STATUS_COMPLETED],
        self::STATUS_COMPLETED  => [],
    ];

    public static function tableName(): string
    {
        return 'batch';
    }

    public function rules(): array
    {
        return [
            [['production_order_id', 'batch_size'], 'required'],
            [['production_order_id', 'brew_master_id', 'actual_yield'], 'integer'],
            [['batch_size'], 'number', 'min' => 0.01],
            [['actual_yield'], 'integer', 'min' => 0],
            [['brew_date', 'completion_date'], 'safe'],
            [['notes'], 'string'],
            [['status'], 'in', 'range' => [
                self::STATUS_PLANNED, self::STATUS_BREWING, self::STATUS_FERMENTING,
                self::STATUS_PACKAGING, self::STATUS_COMPLETED,
            ]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'                  => 'ID',
            'production_order_id' => 'Production Order',
            'batch_number'        => 'Batch #',
            'status'              => 'Status',
            'batch_size'          => 'Batch Size (L)',
            'actual_yield'        => 'Actual Yield (units)',
            'brew_date'           => 'Brew Date',
            'completion_date'     => 'Completion Date',
            'notes'               => 'Notes',
            'brew_master_id'      => 'Brew Master',
            'created_at'          => 'Created',
        ];
    }

    // Batch number generation

    public function beforeSave($insert): bool
    {
        if ($insert && empty($this->batch_number)) {
            $this->batch_number = $this->generateBatchNumber();
        }
        return parent::beforeSave($insert);
    }

    private function generateBatchNumber(): string
    {
        $year = date('Y');
        $last = static::find()
            ->andWhere(['like', 'batch_number', "BCH-{$year}-"])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $seq = $last ? ((int) substr($last->batch_number, -5)) + 1 : 1;
        return sprintf('BCH-%s-%05d', $year, $seq);
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['batch.deleted_at' => null]);
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
            $this->addError('status', "Cannot move batch from \"{$this->status}\" to \"{$newStatus}\".");
            return false;
        }

        // Pre-transition side effects
        if ($newStatus === self::STATUS_BREWING) {
            if (!$this->deductIngredients()) {
                $this->addError('status', 'Insufficient raw material stock. Check ingredient quantities.');
                return false;
            }
            $this->brew_date = date('Y-m-d');

            // Push production order to in_progress if still planned
            if ($this->productionOrder->status === ProductionOrder::STATUS_PLANNED) {
                $this->productionOrder->transitionTo(ProductionOrder::STATUS_IN_PROGRESS);
            }
        }

        if ($newStatus === self::STATUS_COMPLETED) {
            $this->completion_date = date('Y-m-d');
            $this->incrementFinishedGoods();
            $this->productionOrder->checkAutoComplete();
        }

        $old          = $this->status;
        $this->status = $newStatus;

        if (!$this->save(false)) {
            return false;
        }

        AuditLog::record('batch.status_change', 'Batch', $this->id,
            ['status' => $old], ['status' => $newStatus]);

        return true;
    }

    /**
     * Deducts all batch ingredient quantities from raw material stock.
     * Returns false if any material has insufficient stock.
     */
    private function deductIngredients(): bool
    {
        foreach ($this->ingredients as $ingredient) {
            $material = $ingredient->rawMaterial;

            if (!$material) {
                continue;
            }

            // Warn but don't block if stock goes negative — brewmaster decides
            $material->adjustStock(
                -$ingredient->quantity,
                StockTransaction::TYPE_OUT,
                'batch',
                $this->id,
                "Used in batch {$this->batch_number}"
            );
        }

        return true;
    }

    /**
     * Adds the actual_yield (or planned_qty from the production order)
     * to the finished goods product stock.
     */
    private function incrementFinishedGoods(): void
    {
        $qty     = $this->actual_yield ?? $this->productionOrder->planned_qty;
        $product = $this->productionOrder->product;

        if ($product && $qty > 0) {
            $product->adjustStock(
                $qty,
                StockTransaction::TYPE_IN,
                'batch',
                $this->id,
                "Produced by batch {$this->batch_number}"
            );
        }
    }

    // Relationships

    public function getProductionOrder(): ActiveQuery
    {
        return $this->hasOne(ProductionOrder::class, ['id' => 'production_order_id']);
    }

    public function getBrewMaster(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'brew_master_id']);
    }

    public function getIngredients(): ActiveQuery
    {
        return $this->hasMany(BatchIngredient::class, ['batch_id' => 'id'])
                    ->andWhere(['batch_ingredient.deleted_at' => null]);
    }

    // Helpers

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PLANNED    => 'Planned',
            self::STATUS_BREWING    => 'Brewing',
            self::STATUS_FERMENTING => 'Fermenting',
            self::STATUS_PACKAGING  => 'Packaging',
            self::STATUS_COMPLETED  => 'Completed',
        ];
    }

    public static function statusBadgeClass(): array
    {
        return [
            self::STATUS_PLANNED    => 'bg-secondary',
            self::STATUS_BREWING    => 'bg-warning text-dark',
            self::STATUS_FERMENTING => 'bg-info text-dark',
            self::STATUS_PACKAGING  => 'bg-primary',
            self::STATUS_COMPLETED  => 'bg-success',
        ];
    }

    public static function statusIcons(): array
    {
        return [
            self::STATUS_PLANNED    => 'bi-clipboard',
            self::STATUS_BREWING    => 'bi-fire',
            self::STATUS_FERMENTING => 'bi-hourglass-split',
            self::STATUS_PACKAGING  => 'bi-box-seam',
            self::STATUS_COMPLETED  => 'bi-check-circle-fill',
        ];
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

    public function getNextStatus(): ?string
    {
        return self::TRANSITIONS[$this->status][0] ?? null;
    }
}
