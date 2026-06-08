<?php

namespace backend\modules\order\controllers;

use backend\modules\order\models\OrderSearch;

use common\models\Order;
use common\models\OrderItem;
use common\models\Product;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Backend order management.
 *
 * Staff view all orders, advance statuses, and add notes.
 * Requires `manageOrders` permission.
 */
class OrderController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['manageOrders']],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'transition'  => ['post'],
                    'delete'      => ['post'],
                    'remove-item' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel  = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $order    = $this->findModel($id);
        $products = Product::find()
            ->andWhere(['is_active' => 1])
            ->orderBy('name')
            ->all();

        return $this->render('view', [
            'model'    => $order,
            'products' => $products,
        ]);
    }

    public function actionTransition(int $id): \yii\web\Response
    {
        $order     = $this->findModel($id);
        $newStatus = Yii::$app->request->post('status', '');

        if (!$order->transitionTo($newStatus)) {
            Yii::$app->session->setFlash('error',
                implode(' ', $order->getFirstErrors())
            );
        } else {
            Yii::$app->session->setFlash('success',
                "Order {$order->order_number} moved to \"{$order->getStatusLabel()}\"."
            );
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAddItem(int $id): \yii\web\Response
    {
        $order = $this->findModel($id);

        if (!$order->isEditable()) {
            Yii::$app->session->setFlash('error', 'Items can only be added to draft orders.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $productId = (int) Yii::$app->request->post('product_id', 0);
        $quantity  = (int) Yii::$app->request->post('quantity', 1);

        $product = Product::findOne($productId);

        if (!$product) {
            Yii::$app->session->setFlash('error', 'Product not found.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // If same product already in order, increment quantity
        $existing = OrderItem::find()
            ->where(['order_id' => $order->id, 'product_id' => $productId])
            ->one();

        if ($existing) {
            $existing->quantity += $quantity;
            $existing->save();
        } else {
            $item             = new OrderItem();
            $item->order_id   = $order->id;
            $item->product_id = $productId;
            $item->quantity   = $quantity;
            $item->unit_price = $product->unit_price;
            $item->save();
        }

        $order->recalculateTotal();
        Yii::$app->session->setFlash('success', "Item added.");

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionRemoveItem(int $itemId): \yii\web\Response
    {
        $item = OrderItem::findOne($itemId);

        if (!$item) {
            throw new NotFoundHttpException('Item not found.');
        }

        $orderId = $item->order_id;
        $order   = $this->findModel($orderId);

        if (!$order->isEditable()) {
            Yii::$app->session->setFlash('error', 'Cannot remove items from a confirmed order.');
            return $this->redirect(['view', 'id' => $orderId]);
        }

        $item->softDelete();
        $order->recalculateTotal();
        Yii::$app->session->setFlash('success', 'Item removed.');

        return $this->redirect(['view', 'id' => $orderId]);
    }

    public function actionUpdateNotes(int $id): \yii\web\Response
    {
        $order        = $this->findModel($id);
        $order->notes = Yii::$app->request->post('notes', '');
        $order->save(false);

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $order = $this->findModel($id);

        if (!in_array($order->status, [Order::STATUS_DRAFT, Order::STATUS_CANCELLED], true)) {
            Yii::$app->session->setFlash('error', 'Only draft or cancelled orders can be deleted.');
            return $this->redirect(['index']);
        }

        $order->softDelete();
        Yii::$app->session->setFlash('success', "Order {$order->order_number} deleted.");

        return $this->redirect(['index']);
    }

    private function findModel(int $id): Order
    {
        $model = Order::find()->with(['items.product', 'customer'])->andWhere(['order.id' => $id])->one();

        if ($model === null) {
            throw new NotFoundHttpException('Order not found.');
        }

        return $model;
    }
}
