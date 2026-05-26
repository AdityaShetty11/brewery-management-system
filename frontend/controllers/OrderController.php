<?php

namespace frontend\controllers;

use common\models\CustomerCompany;
use common\models\Order;
use common\models\OrderItem;
use common\models\Product;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Customer-facing order actions.
 * All actions require login (`@` role).
 */
class OrderController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'add-item'    => ['post'],
                    'remove-item' => ['post'],
                    'submit'      => ['post'],
                    'cancel'      => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $orders = Order::find()
            ->with('items')
            ->where(['customer_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', ['orders' => $orders]);
    }

    public function actionCreate(): \yii\web\Response|string
    {
        $products  = Product::find()->andWhere(['is_active' => 1])->orderBy('name')->all();
        $companies = CustomerCompany::find()->select(['name', 'id'])->indexBy('id')->column();

        if (Yii::$app->request->isPost) {
            $post      = Yii::$app->request->post();
            $companyId = !empty($post['company_id']) ? (int) $post['company_id'] : null;

            $order              = new Order();
            $order->customer_id = Yii::$app->user->id;
            $order->company_id  = $companyId;
            $order->status      = Order::STATUS_DRAFT;
            $order->notes       = $post['notes'] ?? null;

            if (!$order->save()) {
                Yii::$app->session->setFlash('error', 'Could not create order. Please try again.');
                return $this->render('create', ['products' => $products, 'companies' => $companies]);
            }

            // Add all submitted line items
            $productIds = $post['product_id'] ?? [];
            $quantities = $post['quantity']   ?? [];
            $saved      = false;

            foreach ($productIds as $i => $productId) {
                $qty     = (int) ($quantities[$i] ?? 1);
                $product = Product::findOne((int) $productId);

                if (!$product || $qty < 1) {
                    continue;
                }

                $item             = new OrderItem();
                $item->order_id   = $order->id;
                $item->product_id = $product->id;
                $item->quantity   = $qty;
                $item->unit_price = $product->unit_price;
                $item->save();
                $saved = true;
            }

            if (!$saved) {
                $order->softDelete();
                Yii::$app->session->setFlash('error', 'Please add at least one product to your order.');
                return $this->render('create', ['products' => $products, 'companies' => $companies]);
            }

            $order->recalculateTotal();
            Yii::$app->session->setFlash('success', "Order {$order->order_number} created.");
            return $this->redirect(['view', 'id' => $order->id]);
        }

        return $this->render('create', ['products' => $products, 'companies' => $companies]);
    }

    public function actionView(int $id): string
    {
        $order = $this->findOwnOrder($id);

        return $this->render('view', ['model' => $order]);
    }

    public function actionSubmit(int $id): \yii\web\Response
    {
        $order = $this->findOwnOrder($id);

        if (!$order->isEditable()) {
            Yii::$app->session->setFlash('error', 'Only draft orders can be submitted.');
            return $this->redirect(['view', 'id' => $id]);
        }

        if (empty($order->items)) {
            Yii::$app->session->setFlash('error', 'Your order has no items. Please add products first.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Draft → Confirmed (staff can see it now)
        $order->transitionTo(Order::STATUS_CONFIRMED);
        Yii::$app->session->setFlash('success', "Order {$order->order_number} submitted! Our team will review it shortly.");

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionCancel(int $id): \yii\web\Response
    {
        $order = $this->findOwnOrder($id);

        if (!$order->canTransitionTo(Order::STATUS_CANCELLED)) {
            Yii::$app->session->setFlash('error', 'This order can no longer be cancelled.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $order->transitionTo(Order::STATUS_CANCELLED);
        Yii::$app->session->setFlash('success', "Order {$order->order_number} has been cancelled.");

        return $this->redirect(['index']);
    }

    /**
     * Finds an order that belongs to the current user only.
     * Throws ForbiddenHttpException if the order belongs to someone else.
     */
    private function findOwnOrder(int $id): Order
    {
        $order = Order::find()
            ->with(['items.product'])
            ->andWhere(['order.id' => $id])
            ->one();

        if ($order === null) {
            throw new NotFoundHttpException('Order not found.');
        }

        if ($order->customer_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to view this order.');
        }

        return $order;
    }
}
