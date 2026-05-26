<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * BatchIngredient ActiveRecord model.
 *
 * Links a batch to the raw materials it will consume.
 * Quantities are deducted from raw_material.stock_qty when the
 * batch transitions to 'brewing'.
 *
 * @property int   $id
 * @property int   $batch_id
 * @property int   $raw_material_id
 * @property float $quantity
 */
class BatchIngredient extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'batch_ingredient';
    }

    public function rules(): array
    {
        return [
            [['batch_id', 'raw_material_id', 'quantity'], 'required'],
            [['batch_id', 'raw_material_id'], 'integer'],
            [['quantity'], 'number', 'min' => 0.001],
            [['raw_material_id'], 'exist', 'skipOnError' => true,
                'targetClass'     => RawMaterial::class,
                'targetAttribute' => ['raw_material_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'batch_id'        => 'Batch',
            'raw_material_id' => 'Raw Material',
            'quantity'        => 'Quantity',
        ];
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['batch_ingredient.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Relationships

    public function getBatch(): ActiveQuery
    {
        return $this->hasOne(Batch::class, ['id' => 'batch_id']);
    }

    public function getRawMaterial(): ActiveQuery
    {
        return $this->hasOne(RawMaterial::class, ['id' => 'raw_material_id']);
    }
}
