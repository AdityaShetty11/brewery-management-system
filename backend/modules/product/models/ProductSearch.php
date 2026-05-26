<?php

namespace backend\modules\product\models;

use common\models\Product;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    public function rules(): array
    {
        return [
            [['id', 'category_id', 'is_active'], 'integer'],
            [['sku', 'name', 'packaging_type'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Product::find()->with('category');

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['name' => SORT_ASC]],
            'pagination' => ['pageSize' => 25],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id'             => $this->id])
              ->andFilterWhere(['category_id'    => $this->category_id])
              ->andFilterWhere(['packaging_type' => $this->packaging_type])
              ->andFilterWhere(['is_active'      => $this->is_active])
              ->andFilterWhere(['like', 'sku',  $this->sku])
              ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
