<?php

use yii\db\Migration;

/**
 * Creates the `order_item` table.
 *
 * unit_price is snapshotted at order time — product price changes
 * must never retroactively alter historical order values.
 */
class m240001_000010_create_order_item_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('order_item', [
            'id'         => $this->primaryKey()->unsigned(),
            'order_id'   => $this->integer()->unsigned()->notNull(),
            'product_id' => $this->integer()->unsigned()->notNull(),
            'quantity'   => $this->integer()->notNull()->defaultValue(1),
            'unit_price' => $this->decimal(10, 2)->notNull(),
            'subtotal'   => $this->decimal(12, 2)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey('fk_order_item_order',   'order_item', 'order_id',   'order',   'id', 'CASCADE',  'CASCADE');
        $this->addForeignKey('fk_order_item_product', 'order_item', 'product_id', 'product', 'id', 'RESTRICT', 'CASCADE');

        $this->createIndex('idx_order_item_order', 'order_item', 'order_id');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_order_item_order',   'order_item');
        $this->dropForeignKey('fk_order_item_product', 'order_item');
        $this->dropTable('order_item');
    }
}
