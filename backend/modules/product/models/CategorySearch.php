<?php

namespace backend\modules\product\models;

use common\models\ProductCategory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CategorySearch extends ProductCategory
{
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'description'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = ProductCategory::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['name' => SORT_ASC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
