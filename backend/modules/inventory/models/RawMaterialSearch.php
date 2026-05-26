<?php

namespace backend\modules\inventory\models;

use common\models\RawMaterial;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RawMaterialSearch extends RawMaterial
{
    public ?string $lowStock = null;

    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'unit', 'lowStock'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = RawMaterial::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['name' => SORT_ASC]],
            'pagination' => ['pageSize' => 25],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Special filter: show only low-stock items
        if ($this->lowStock === '1') {
            $query->andWhere('raw_material.stock_qty <= raw_material.reorder_level');
        }

        $query->andFilterWhere(['raw_material.id'   => $this->id])
              ->andFilterWhere(['like', 'raw_material.name', $this->name])
              ->andFilterWhere(['like', 'raw_material.unit', $this->unit]);

        return $dataProvider;
    }
}
