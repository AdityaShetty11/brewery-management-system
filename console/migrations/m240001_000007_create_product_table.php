<?php

use yii\db\Migration;

/**
 * Creates the `product` table.
 *
 * packaging_type ENUM mirrors real brewery SKU formats:
 *   keg | can | bottle | other
 *
 * stock_qty is the live finished-goods count.
 * It is updated automatically when:
 *   - a batch completes   (+qty via StockTransaction)
 *   - an order delivers   (-qty via StockTransaction)
 *   - a manual adjustment (±qty via StockTransaction)
 */
class m240001_000007_create_product_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('product', [
            'id'             => $this->primaryKey()->unsigned(),
            'category_id'    => $this->integer()->unsigned()->notNull(),
            'sku'            => $this->string(50)->notNull()->unique(),
            'name'           => $this->string(150)->notNull(),
            'description'    => $this->text()->null()->defaultValue(null),
            'packaging_type' => "ENUM('keg','can','bottle','other') NOT NULL DEFAULT 'bottle'",
            'unit_price'     => $this->decimal(10, 2)->notNull()->defaultValue(0.00),
            'stock_qty'      => $this->integer()->notNull()->defaultValue(0),
            'is_active'      => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_at'     => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'     => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'     => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey(
            'fk_product_category',
            'product', 'category_id',
            'product_category', 'id',
            'RESTRICT', 'CASCADE'
        );

        $this->createIndex('idx_product_sku',      'product', 'sku');
        $this->createIndex('idx_product_category', 'product', 'category_id');
        $this->createIndex('idx_product_active',   'product', 'is_active');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_product_category', 'product');
        $this->dropTable('product');
    }
}
