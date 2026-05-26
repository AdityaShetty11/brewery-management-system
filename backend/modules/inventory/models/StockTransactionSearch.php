<?php

namespace backend\modules\inventory\models;

use common\models\StockTransaction;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StockTransactionSearch extends StockTransaction
{
    public function rules(): array
    {
        return [
            [['id', 'item_id', 'reference_id', 'created_by'], 'integer'],
            [['item_type', 'transaction_type', 'reference_type'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params, ?string $itemType = null, ?int $itemId = null): ActiveDataProvider
    {
        $query = StockTransaction::find()->with(['createdBy', 'rawMaterial', 'product']);

        if ($itemType !== null) {
            $query->andWhere(['item_type' => $itemType]);
        }

        if ($itemId !== null) {
            $query->andWhere(['item_id' => $itemId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['stock_transaction.item_type'        => $this->item_type])
              ->andFilterWhere(['stock_transaction.transaction_type'  => $this->transaction_type])
              ->andFilterWhere(['stock_transaction.reference_type'    => $this->reference_type])
              ->andFilterWhere(['stock_transaction.item_id'           => $this->item_id])
              ->andFilterWhere(['stock_transaction.created_by'        => $this->created_by]);

        return $dataProvider;
    }
}
