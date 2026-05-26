<?php

namespace backend\modules\production\models;

use common\models\Batch;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BatchSearch extends Batch
{
    public function rules(): array
    {
        return [
            [['id', 'production_order_id', 'brew_master_id'], 'integer'],
            [['batch_number', 'status'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params, ?int $productionOrderId = null): ActiveDataProvider
    {
        $query = Batch::find()->with(['productionOrder.product', 'brewMaster']);

        if ($productionOrderId !== null) {
            $query->andWhere(['batch.production_order_id' => $productionOrderId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['batch.id'                  => $this->id])
              ->andFilterWhere(['batch.production_order_id' => $this->production_order_id])
              ->andFilterWhere(['batch.status'              => $this->status])
              ->andFilterWhere(['batch.brew_master_id'      => $this->brew_master_id])
              ->andFilterWhere(['like', 'batch.batch_number', $this->batch_number]);

        return $dataProvider;
    }
}
