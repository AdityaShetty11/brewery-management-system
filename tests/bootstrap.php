<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV')   or define('YII_ENV', 'test');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@common',   __DIR__ . '/../common');
Yii::setAlias('@frontend', __DIR__ . '/../frontend');
Yii::setAlias('@backend',  __DIR__ . '/../backend');
Yii::setAlias('@console',  __DIR__ . '/../console');

new yii\console\Application([
    'id'       => 'unit-tests',
    'basePath' => dirname(__DIR__),
    'aliases'  => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn'   => 'sqlite::memory:',
        ],
    ],
]);

// Create minimal table schemas so ActiveRecord can resolve column names
// without a real MySQL connection. Only the columns used by tested models
// need to be present.
$db = Yii::$app->db;

$db->createCommand('CREATE TABLE IF NOT EXISTS "order" (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    order_number  TEXT,
    customer_id   INTEGER NOT NULL,
    company_id    INTEGER,
    status        TEXT    NOT NULL DEFAULT "draft",
    total_amount  REAL    NOT NULL DEFAULT 0,
    notes         TEXT,
    confirmed_at  TEXT,
    delivered_at  TEXT,
    created_at    TEXT    NOT NULL DEFAULT "",
    updated_at    TEXT    NOT NULL DEFAULT "",
    deleted_at    TEXT
)')->execute();

$db->createCommand('CREATE TABLE IF NOT EXISTS "order_item" (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id   INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity   INTEGER NOT NULL DEFAULT 1,
    unit_price REAL    NOT NULL DEFAULT 0,
    subtotal   REAL    NOT NULL DEFAULT 0,
    deleted_at TEXT
)')->execute();

$db->createCommand('CREATE TABLE IF NOT EXISTS "batch" (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    production_order_id INTEGER NOT NULL,
    batch_number        TEXT,
    status              TEXT    NOT NULL DEFAULT "planned",
    batch_size          REAL    NOT NULL DEFAULT 0,
    actual_yield        INTEGER,
    brew_date           TEXT,
    completion_date     TEXT,
    notes               TEXT,
    brew_master_id      INTEGER,
    created_at          TEXT    NOT NULL DEFAULT "",
    updated_at          TEXT    NOT NULL DEFAULT "",
    deleted_at          TEXT
)')->execute();

$db->createCommand('CREATE TABLE IF NOT EXISTS "user" (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    username              TEXT    NOT NULL,
    email                 TEXT    NOT NULL,
    password_hash         TEXT    NOT NULL DEFAULT "",
    auth_key              TEXT    NOT NULL DEFAULT "",
    verification_token    TEXT,
    password_reset_token  TEXT,
    status                INTEGER NOT NULL DEFAULT 9,
    created_at            INTEGER NOT NULL DEFAULT 0,
    updated_at            INTEGER NOT NULL DEFAULT 0,
    deleted_at            INTEGER
)')->execute();

$db->createCommand('CREATE TABLE IF NOT EXISTS "product_category" (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    name       TEXT    NOT NULL,
    created_at TEXT    NOT NULL DEFAULT "",
    updated_at TEXT    NOT NULL DEFAULT ""
)')->execute();

$db->createCommand('CREATE TABLE IF NOT EXISTS "product" (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id    INTEGER NOT NULL,
    sku            TEXT    NOT NULL,
    name           TEXT    NOT NULL,
    description    TEXT,
    packaging_type TEXT    NOT NULL DEFAULT "keg",
    unit_price     REAL    NOT NULL DEFAULT 0,
    stock_qty      INTEGER NOT NULL DEFAULT 0,
    is_active      INTEGER NOT NULL DEFAULT 1,
    created_at     TEXT    NOT NULL DEFAULT "",
    updated_at     TEXT    NOT NULL DEFAULT "",
    deleted_at     TEXT
)')->execute();

$db->createCommand('CREATE TABLE IF NOT EXISTS "raw_material" (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT    NOT NULL,
    unit          TEXT    NOT NULL,
    stock_qty     REAL    NOT NULL DEFAULT 0,
    reorder_level REAL    NOT NULL DEFAULT 0,
    description   TEXT,
    created_at    TEXT    NOT NULL DEFAULT "",
    updated_at    TEXT    NOT NULL DEFAULT "",
    deleted_at    TEXT
)')->execute();
