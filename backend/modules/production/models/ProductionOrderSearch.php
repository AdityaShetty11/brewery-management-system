<?php

namespace backend\modules\production\models;

use common\models\ProductionOrder;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductionOrderSearch extends ProductionOrder
{
    public function rules(): array
    {
        return [
            [['id', 'product_id'], 'integer'],
            [['reference', 'status'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = ProductionOrder::find()->with(['product', 'batches']);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['production_order.id'         => $this->id])
              ->andFilterWhere(['production_order.product_id' => $this->product_id])
              ->andFilterWhere(['production_order.status'     => $this->status])
              ->andFilterWhere(['like', 'production_order.reference', $this->reference]);

        return $dataProvider;
    }
}
