<?php

namespace backend\modules\report\controllers;

use common\models\Batch;
use common\models\Order;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class ReportController extends Controller
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

    // Sales Report

    public function actionSales(): string
    {
        $req      = Yii::$app->request;
        $dateFrom = $req->get('date_from', date('Y-m-01', strtotime('-2 months')));
        $dateTo   = $req->get('date_to',   date('Y-m-d'));
        $status   = $req->get('status',    '');

        $baseQuery = Order::find()
            ->with(['customer', 'company'])
            ->andWhere(['>=', 'order.created_at', $dateFrom . ' 00:00:00'])
            ->andWhere(['<=', 'order.created_at', $dateTo   . ' 23:59:59']);

        if ($status !== '') {
            $baseQuery->andWhere(['order.status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => clone $baseQuery,
            'sort'       => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        // Summary stats
        $statsQuery    = clone $baseQuery;
        $totalOrders   = (int)   $statsQuery->count();
        $totalRevenue  = (float) ($statsQuery->andWhere(['!=', 'order.status', Order::STATUS_CANCELLED])->sum('total_amount') ?? 0);
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return $this->render('sales', [
            'dataProvider' => $dataProvider,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'status'       => $status,
            'totalOrders'  => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'avgOrderValue'=> $avgOrderValue,
        ]);
    }

    public function actionExportSales(): Response
    {
        $req      = Yii::$app->request;
        $dateFrom = $req->get('date_from', date('Y-m-01', strtotime('-2 months')));
        $dateTo   = $req->get('date_to',   date('Y-m-d'));
        $status   = $req->get('status',    '');

        $query = Order::find()
            ->with(['customer', 'company'])
            ->andWhere(['>=', 'order.created_at', $dateFrom . ' 00:00:00'])
            ->andWhere(['<=', 'order.created_at', $dateTo   . ' 23:59:59']);

        if ($status !== '') {
            $query->andWhere(['order.status' => $status]);
        }

        $orders = $query->orderBy(['created_at' => SORT_DESC])->all();

        $lines   = [];
        $lines[] = implode(',', ['Order #', 'Date', 'Customer', 'Company', 'Status', 'Total (USD)']);

        foreach ($orders as $o) {
            $lines[] = implode(',', [
                $o->order_number,
                substr($o->created_at, 0, 10),
                '"' . ($o->customer->username ?? '') . '"',
                '"' . ($o->company->name ?? '') . '"',
                $o->status,
                number_format((float) $o->total_amount, 2),
            ]);
        }

        $csv = implode("\n", $lines);

        return Yii::$app->response->sendContentAsFile(
            $csv,
            "sales-report-{$dateFrom}-to-{$dateTo}.csv",
            ['mimeType' => 'text/csv']
        );
    }

    // Inventory Report

    public function actionInventory(): string
    {
        $db            = Yii::$app->db;
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $showLowOnly   = (bool) Yii::$app->request->get('low_only', false);

        $having = $showLowOnly ? 'HAVING rm.stock_qty <= rm.reorder_level' : '';

        $rows = $db->createCommand(
            "SELECT rm.id, rm.name, rm.unit, rm.stock_qty, rm.reorder_level,
                    COALESCE(SUM(CASE WHEN st.transaction_type = 'in'  THEN st.quantity ELSE 0 END), 0) AS total_in,
                    COALESCE(SUM(CASE WHEN st.transaction_type = 'out' THEN st.quantity ELSE 0 END), 0) AS total_out
             FROM raw_material rm
             LEFT JOIN stock_transaction st
                    ON st.item_id = rm.id
                   AND st.item_type = 'raw_material'
                   AND st.deleted_at IS NULL
                   AND st.created_at >= :from
             WHERE rm.deleted_at IS NULL
             GROUP BY rm.id, rm.name, rm.unit, rm.stock_qty, rm.reorder_level
             {$having}
             ORDER BY rm.stock_qty ASC",
            [':from' => $thirtyDaysAgo]
        )->queryAll();

        $totalMaterials = count($rows);
        $lowStockCount  = count(array_filter($rows, fn($r) => $r['stock_qty'] <= $r['reorder_level']));
        $totalStockIn   = array_sum(array_column($rows, 'total_in'));
        $totalStockOut  = array_sum(array_column($rows, 'total_out'));

        return $this->render('inventory', [
            'rows'           => $rows,
            'showLowOnly'    => $showLowOnly,
            'totalMaterials' => $totalMaterials,
            'lowStockCount'  => $lowStockCount,
            'totalStockIn'   => $totalStockIn,
            'totalStockOut'  => $totalStockOut,
        ]);
    }

    // Production Report

    public function actionProduction(): string
    {
        $req      = Yii::$app->request;
        $dateFrom = $req->get('date_from', date('Y-m-01', strtotime('-3 months')));
        $dateTo   = $req->get('date_to',   date('Y-m-d'));
        $status   = $req->get('status',    '');

        $baseQuery = Batch::find()
            ->andWhere(['>=', 'batch.created_at', $dateFrom . ' 00:00:00'])
            ->andWhere(['<=', 'batch.created_at', $dateTo   . ' 23:59:59']);

        if ($status !== '') {
            $baseQuery->andWhere(['batch.status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => (clone $baseQuery)->with(['productionOrder.product', 'brewMaster']),
            'sort'       => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $totalBatches   = (int)   $baseQuery->count();
        $completedCount = (int)   (clone $baseQuery)->andWhere(['batch.status' => Batch::STATUS_COMPLETED])->count();
        $totalYield     = (float) ((clone $baseQuery)->andWhere(['batch.status' => Batch::STATUS_COMPLETED])->sum('actual_yield') ?? 0);

        return $this->render('production', [
            'dataProvider'   => $dataProvider,
            'dateFrom'       => $dateFrom,
            'dateTo'         => $dateTo,
            'status'         => $status,
            'totalBatches'   => $totalBatches,
            'completedCount' => $completedCount,
            'totalYield'     => (int) $totalYield,
        ]);
    }
}
