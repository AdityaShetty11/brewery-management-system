<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * OrderItem ActiveRecord model.
 *
 * @property int    $id
 * @property int    $order_id
 * @property int    $product_id
 * @property int    $quantity
 * @property float  $unit_price   price snapshot at order time
 * @property float  $subtotal
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class OrderItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'order_item';
    }

    public function rules(): array
    {
        return [
            [['order_id', 'product_id', 'quantity', 'unit_price'], 'required'],
            [['order_id', 'product_id', 'quantity'], 'integer'],
            [['quantity'], 'integer', 'min' => 1],
            [['unit_price', 'subtotal'], 'number', 'min' => 0],
            [['product_id'], 'exist', 'skipOnError' => true,
                'targetClass'     => Product::class,
                'targetAttribute' => ['product_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'order_id'   => 'Order',
            'product_id' => 'Product',
            'quantity'   => 'Qty',
            'unit_price' => 'Unit Price',
            'subtotal'   => 'Subtotal',
        ];
    }

    // Computed fields — call before save

    public function beforeSave($insert): bool
    {
        // Snapshot current product price if not already set
        if (empty($this->unit_price) && $this->product_id) {
            $this->unit_price = $this->product->unit_price ?? 0;
        }

        $this->subtotal = round($this->unit_price * $this->quantity, 2);

        return parent::beforeSave($insert);
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['order_item.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Relationships

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    // Helpers

    public function getFormattedSubtotal(): string
    {
        return '$' . number_format((float) $this->subtotal, 2);
    }
}
