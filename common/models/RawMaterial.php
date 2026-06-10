<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * RawMaterial ActiveRecord model.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $unit          e.g. kg, litre, unit
 * @property float       $stock_qty
 * @property float       $reorder_level
 * @property string|null $description
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class RawMaterial extends ActiveRecord
{
    const LOW_STOCK_THRESHOLD_MULTIPLIER = 1.0; // alert when stock_qty <= reorder_level

    public static function tableName(): string
    {
        return 'raw_material';
    }

    public function rules(): array
    {
        return [
            [['name', 'unit'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['unit'], 'string', 'max' => 30],
            [['stock_qty', 'reorder_level'], 'number', 'min' => 0],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'            => 'ID',
            'name'          => 'Material Name',
            'unit'          => 'Unit',
            'stock_qty'     => 'Stock Qty',
            'reorder_level' => 'Reorder Level',
            'description'   => 'Description',
            'created_at'    => 'Created',
            'updated_at'    => 'Last Updated',
        ];
    }

    // Soft delete — global scope

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['raw_material.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Stock helpers

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->reorder_level;
    }

    public function getFormattedStock(): string
    {
        return number_format((float) $this->stock_qty, 2) . ' ' . $this->unit;
    }

    /**
     * Adjust stock and write a StockTransaction row.
     * Use positive qty for 'in', negative for 'out'.
     */
    public function adjustStock(float $qty, string $type, string $refType = 'manual', ?int $refId = null, ?string $notes = null): bool
    {
        $this->stock_qty += $qty;

        if ($this->stock_qty < 0) {
            $this->stock_qty = 0;
        }

        if (!$this->save(false)) {
            return false;
        }

        $tx                   = new StockTransaction();
        $tx->item_type        = StockTransaction::ITEM_RAW;
        $tx->item_id          = $this->id;
        $tx->transaction_type = $type;
        $tx->quantity         = abs($qty);
        $tx->reference_type   = $refType;
        $tx->reference_id     = $refId;
        $tx->notes            = $notes;
        $tx->created_by       = \Yii::$app->user->isGuest ? null : \Yii::$app->user->id;

        return $tx->save();
    }

    // Relationships

    public function getBatchIngredients(): ActiveQuery
    {
        return $this->hasMany(BatchIngredient::class, ['raw_material_id' => 'id']);
    }
}
