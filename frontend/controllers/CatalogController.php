<?php

namespace frontend\controllers;

use common\models\Product;
use common\models\ProductCategory;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Public product catalog — accessible to all visitors, no login required.
 */
class CatalogController extends Controller
{
    /**
     * Catalog index with optional category and packaging filters.
     */
    public function actionIndex(): string
    {
        $categoryId    = (int) \Yii::$app->request->get('category_id', 0);
        $packagingType = \Yii::$app->request->get('packaging_type', '');

        $query = Product::find()
            ->with('category')
            ->andWhere(['is_active' => 1]);

        if ($categoryId > 0) {
            $query->andWhere(['category_id' => $categoryId]);
        }

        if (!empty($packagingType)) {
            $query->andWhere(['packaging_type' => $packagingType]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['name' => SORT_ASC]],
            'pagination' => ['pageSize' => 12],
        ]);

        $categories = ProductCategory::find()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'categories'    => $categories,
            'categoryId'    => $categoryId,
            'packagingType' => $packagingType,
        ]);
    }

    /**
     * Public product detail page.
     */
    public function actionView(int $id): string
    {
        $product = Product::find()
            ->with('category')
            ->andWhere(['id' => $id, 'is_active' => 1])
            ->one();

        if ($product === null) {
            throw new NotFoundHttpException('Product not found.');
        }

        return $this->render('view', ['model' => $product]);
    }
}
