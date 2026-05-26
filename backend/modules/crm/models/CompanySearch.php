<?php

namespace backend\modules\crm\models;

use common\models\CustomerCompany;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CompanySearch is the search model for GridView filtering on the company list.
 */
class CompanySearch extends CustomerCompany
{
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'industry', 'city', 'country', 'email', 'phone'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = CustomerCompany::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['name' => SORT_ASC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['like', 'name',     $this->name])
              ->andFilterWhere(['like', 'industry',  $this->industry])
              ->andFilterWhere(['like', 'city',      $this->city])
              ->andFilterWhere(['like', 'country',   $this->country])
              ->andFilterWhere(['like', 'email',     $this->email])
              ->andFilterWhere(['like', 'phone',     $this->phone]);

        return $dataProvider;
    }
}
