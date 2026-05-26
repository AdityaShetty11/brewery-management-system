<?php

namespace backend\modules\order\models;

use common\models\Order;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderSearch extends Order
{
    public ?string $customerUsername = null;

    public function rules(): array
    {
        return [
            [['id', 'customer_id'], 'integer'],
            [['order_number', 'status', 'customerUsername'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params, ?int $customerId = null): ActiveDataProvider
    {
        $query = Order::find()
            ->with(['customer', 'items'])
            ->joinWith('customer c');

        if ($customerId !== null) {
            $query->andWhere(['order.customer_id' => $customerId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $dataProvider->sort->attributes['customerUsername'] = [
            'asc'  => ['c.username' => SORT_ASC],
            'desc' => ['c.username' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['order.id'          => $this->id])
              ->andFilterWhere(['order.customer_id'  => $this->customer_id])
              ->andFilterWhere(['order.status'       => $this->status])
              ->andFilterWhere(['like', 'order.order_number', $this->order_number])
              ->andFilterWhere(['like', 'c.username', $this->customerUsername]);

        return $dataProvider;
    }
}
