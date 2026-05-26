<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * ProductCategory ActiveRecord model.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $description
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class ProductCategory extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'product_category';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique', 'message' => 'A category with this name already exists.'],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'name'        => 'Category Name',
            'description' => 'Description',
            'created_at'  => 'Created',
            'updated_at'  => 'Last Updated',
        ];
    }

    // Soft delete — global scope

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['product_category.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Relationships

    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['category_id' => 'id'])
                    ->andWhere(['product.deleted_at' => null]);
    }

    public function getActiveProductCount(): int
    {
        return (int) $this->getProducts()->andWhere(['is_active' => 1])->count();
    }
}
