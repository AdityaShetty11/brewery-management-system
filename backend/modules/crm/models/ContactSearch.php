<?php

namespace backend\modules\crm\models;

use common\models\CustomerContact;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContactSearch is the search model for GridView filtering on the contact list.
 */
class ContactSearch extends CustomerContact
{
    public function rules(): array
    {
        return [
            [['id', 'company_id'], 'integer'],
            [['first_name', 'last_name', 'email', 'phone', 'role'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params, ?int $companyId = null): ActiveDataProvider
    {
        $query = CustomerContact::find()->with('company');

        if ($companyId !== null) {
            $query->andWhere(['company_id' => $companyId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['last_name' => SORT_ASC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id'         => $this->id])
              ->andFilterWhere(['company_id'  => $this->company_id ?? $companyId])
              ->andFilterWhere(['like', 'first_name', $this->first_name])
              ->andFilterWhere(['like', 'last_name',  $this->last_name])
              ->andFilterWhere(['like', 'email',      $this->email])
              ->andFilterWhere(['like', 'phone',      $this->phone])
              ->andFilterWhere(['like', 'role',       $this->role]);

        return $dataProvider;
    }
}
