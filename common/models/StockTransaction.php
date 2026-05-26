<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * StockTransaction ActiveRecord model.
 *
 * Polymorphic — item_type is either 'raw_material' or 'finished_good'.
 * item_id references raw_material.id or product.id respectively.
 *
 * @property int         $id
 * @property string      $item_type        raw_material|finished_good
 * @property int         $item_id
 * @property string      $transaction_type in|out|adjustment
 * @property float       $quantity
 * @property string|null $reference_type   order|batch|manual
 * @property int|null    $reference_id
 * @property string|null $notes
 * @property int|null    $created_by
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class StockTransaction extends ActiveRecord
{
    const ITEM_RAW      = 'raw_material';
    const ITEM_FINISHED = 'finished_good';

    const TYPE_IN         = 'in';
    const TYPE_OUT        = 'out';
    const TYPE_ADJUSTMENT = 'adjustment';

    public static function tableName(): string
    {
        return 'stock_transaction';
    }

    public function rules(): array
    {
        return [
            [['item_type', 'item_id', 'transaction_type', 'quantity'], 'required'],
            [['item_id', 'reference_id', 'created_by'], 'integer'],
            [['quantity'], 'number', 'min' => 0],
            [['item_type'], 'in', 'range' => [self::ITEM_RAW, self::ITEM_FINISHED]],
            [['transaction_type'], 'in', 'range' => [self::TYPE_IN, self::TYPE_OUT, self::TYPE_ADJUSTMENT]],
            [['reference_type'], 'string', 'max' => 50],
            [['notes'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'               => 'ID',
            'item_type'        => 'Item Type',
            'item_id'          => 'Item',
            'transaction_type' => 'Transaction',
            'quantity'         => 'Quantity',
            'reference_type'   => 'Reference Type',
            'reference_id'     => 'Reference ID',
            'notes'            => 'Notes',
            'created_by'       => 'Created By',
            'created_at'       => 'Date',
        ];
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['stock_transaction.deleted_at' => null]);
    }

    // Relationships

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getRawMaterial(): ActiveQuery
    {
        return $this->hasOne(RawMaterial::class, ['id' => 'item_id']);
    }

    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'item_id']);
    }

    // Helpers

    public static function typeLabels(): array
    {
        return [
            self::TYPE_IN         => 'Stock In',
            self::TYPE_OUT        => 'Stock Out',
            self::TYPE_ADJUSTMENT => 'Adjustment',
        ];
    }

    public function getTypeLabel(): string
    {
        return self::typeLabels()[$this->transaction_type] ?? $this->transaction_type;
    }

    public static function itemTypeLabels(): array
    {
        return [
            self::ITEM_RAW      => 'Raw Material',
            self::ITEM_FINISHED => 'Finished Good',
        ];
    }
}
