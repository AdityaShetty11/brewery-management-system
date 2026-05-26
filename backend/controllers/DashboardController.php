<?php

namespace backend\controllers;

use common\models\Batch;
use common\models\Order;
use common\models\RawMaterial;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Controller;

class DashboardController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $db           = Yii::$app->db;
        $monthStart   = date('Y-m-01 00:00:00');
        $sixMonthsAgo = date('Y-m-01', strtotime('-5 months'));

        $ordersThisMonth = (int) Order::find()
            ->andWhere(['>=', 'created_at', $monthStart])
            ->andWhere(['!=', 'status', Order::STATUS_CANCELLED])
            ->count();

        $revenueThisMonth = (float) (Order::find()
            ->andWhere(['>=', 'created_at', $monthStart])
            ->andWhere(['!=', 'status', Order::STATUS_CANCELLED])
            ->sum('total_amount') ?? 0);

        $activeBatches = (int) Batch::find()
            ->andWhere(['!=', 'status', Batch::STATUS_COMPLETED])
            ->count();

        $lowStockCount = (int) RawMaterial::find()
            ->andWhere(new Expression('stock_qty <= reorder_level'))
            ->count();

        $revenueRows = $db->createCommand(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
                    COALESCE(SUM(total_amount), 0) AS revenue
             FROM `order`
             WHERE deleted_at IS NULL AND status != 'cancelled' AND created_at >= :from
             GROUP BY month ORDER BY month",
            [':from' => $sixMonthsAgo]
        )->queryAll();

        $topProducts = $db->createCommand(
            "SELECT p.name, COALESCE(SUM(oi.subtotal), 0) AS revenue
             FROM order_item oi
             JOIN product p ON p.id = oi.product_id
             JOIN `order` o ON o.id = oi.order_id
             WHERE o.deleted_at IS NULL AND oi.deleted_at IS NULL AND o.status != 'cancelled'
             GROUP BY p.id, p.name ORDER BY revenue DESC LIMIT 5"
        )->queryAll();

        $batchStatuses = $db->createCommand(
            "SELECT status, COUNT(*) AS cnt FROM batch WHERE deleted_at IS NULL GROUP BY status"
        )->queryAll();

        $orderStatuses = $db->createCommand(
            "SELECT status, COUNT(*) AS cnt FROM `order` WHERE deleted_at IS NULL GROUP BY status"
        )->queryAll();

        $recentOrders = Order::find()
            ->with('customer')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6)
            ->all();

        $recentBatches = Batch::find()
            ->with(['productionOrder.product'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6)
            ->all();

        return $this->render('index', [
            'ordersThisMonth'   => $ordersThisMonth,
            'revenueThisMonth'  => $revenueThisMonth,
            'activeBatches'     => $activeBatches,
            'lowStockCount'     => $lowStockCount,
            'revenueLabels'     => array_column($revenueRows, 'month'),
            'revenueValues'     => array_map('floatval', array_column($revenueRows, 'revenue')),
            'topProductLabels'  => array_column($topProducts, 'name'),
            'topProductValues'  => array_map('floatval', array_column($topProducts, 'revenue')),
            'batchStatusLabels' => array_column($batchStatuses, 'status'),
            'batchStatusCounts' => array_map('intval', array_column($batchStatuses, 'cnt')),
            'orderStatusLabels' => array_column($orderStatuses, 'status'),
            'orderStatusCounts' => array_map('intval', array_column($orderStatuses, 'cnt')),
            'recentOrders'      => $recentOrders,
            'recentBatches'     => $recentBatches,
        ]);
    }
}
