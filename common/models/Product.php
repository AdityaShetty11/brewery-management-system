<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * Product ActiveRecord model.
 *
 * @property int         $id
 * @property int         $category_id
 * @property string      $sku
 * @property string      $name
 * @property string|null $description
 * @property string      $packaging_type   keg|can|bottle|other
 * @property float       $unit_price
 * @property int         $stock_qty
 * @property int         $is_active
 * @property string|null $image
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class Product extends ActiveRecord
{
    /** Holds the uploaded file instance — not persisted to DB directly. */
    public $imageFile = null;
    // Packaging type constants — mirrors the ENUM in the DB
    const PACK_KEG    = 'keg';
    const PACK_CAN    = 'can';
    const PACK_BOTTLE = 'bottle';
    const PACK_OTHER  = 'other';

    // Low stock threshold for dashboard alerts
    const LOW_STOCK_THRESHOLD = 10;

    public static function tableName(): string
    {
        return 'product';
    }

    public function rules(): array
    {
        return [
            [['category_id', 'sku', 'name', 'packaging_type', 'unit_price'], 'required'],

            [['category_id'], 'integer'],
            [['unit_price'], 'number', 'min' => 0],
            [['stock_qty'], 'integer', 'min' => 0],
            [['is_active'], 'boolean'],

            [['sku'], 'string', 'max' => 50],
            [['sku'], 'unique', 'message' => 'This SKU is already in use.'],
            [['name'], 'string', 'max' => 150],
            [['description'], 'string'],

            [['packaging_type'], 'in', 'range' => [
                self::PACK_KEG, self::PACK_CAN, self::PACK_BOTTLE, self::PACK_OTHER,
            ]],

            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass'     => ProductCategory::class,
                'targetAttribute' => ['category_id' => 'id'],
            ],

            [['imageFile'], 'image', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, gif, webp',
                'maxSize'    => 2 * 1024 * 1024,
                'tooBig'     => 'Image must be smaller than 2 MB.',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'category_id'    => 'Category',
            'sku'            => 'SKU',
            'name'           => 'Product Name',
            'description'    => 'Description',
            'packaging_type' => 'Packaging',
            'unit_price'     => 'Unit Price ($)',
            'stock_qty'      => 'Stock Quantity',
            'is_active'      => 'Active',
            'image'          => 'Image',
            'imageFile'      => 'Product Image',
            'created_at'     => 'Created',
            'updated_at'     => 'Last Updated',
        ];
    }

    // Soft delete — global scope

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['product.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Relationships

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(ProductCategory::class, ['id' => 'category_id']);
    }

    public function getOrderItems(): ActiveQuery
    {
        return $this->hasMany(OrderItem::class, ['product_id' => 'id']);
    }

    // Static helpers

    public static function packagingLabels(): array
    {
        return [
            self::PACK_KEG    => 'Keg',
            self::PACK_CAN    => 'Can',
            self::PACK_BOTTLE => 'Bottle',
            self::PACK_OTHER  => 'Other',
        ];
    }

    public static function packagingIcons(): array
    {
        return [
            self::PACK_KEG    => 'bi-droplet-half',
            self::PACK_CAN    => 'bi-cup',
            self::PACK_BOTTLE => 'bi-cup-hot',
            self::PACK_OTHER  => 'bi-box',
        ];
    }

    // Instance helpers

    public function getPackagingLabel(): string
    {
        return self::packagingLabels()[$this->packaging_type] ?? $this->packaging_type;
    }

    public function getPackagingIcon(): string
    {
        return self::packagingIcons()[$this->packaging_type] ?? 'bi-box';
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= self::LOW_STOCK_THRESHOLD;
    }

    public function getFormattedPrice(): string
    {
        return '$' . number_format((float) $this->unit_price, 2);
    }

    /**
     * Adjust stock and write a StockTransaction record.
     * Use +qty for in, -qty for out.
     */
    public function adjustStock(int $qty, string $transactionType, string $refType = 'manual', ?int $refId = null, ?string $notes = null): bool
    {
        $this->stock_qty += $qty;

        if ($this->stock_qty < 0) {
            $this->stock_qty = 0;
        }

        if (!$this->save(false)) {
            return false;
        }

        $tx                   = new StockTransaction();
        $tx->item_type        = 'finished_good';
        $tx->item_id          = $this->id;
        $tx->transaction_type = $transactionType;
        $tx->quantity         = abs($qty);
        $tx->reference_type   = $refType;
        $tx->reference_id     = $refId;
        $tx->notes            = $notes;
        $tx->created_by       = \Yii::$app->user->isGuest ? null : \Yii::$app->user->id;

        return $tx->save();
    }
}
