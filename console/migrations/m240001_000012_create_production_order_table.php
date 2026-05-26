<?php

use yii\db\Migration;

/**
 * Creates the `production_order` table.
 *
 * A production order is the authorisation to manufacture a quantity
 * of a specific product. It may be linked to a sales order (order_id)
 * or raised independently (e.g. for stock replenishment).
 *
 * Workflow: planned → in_progress → completed | cancelled
 */
class m240001_000012_create_production_order_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('production_order', [
            'id'           => $this->primaryKey()->unsigned(),
            'reference'    => $this->string(30)->notNull()->unique(),
            'order_id'     => $this->integer()->unsigned()->null()->defaultValue(null),
            'product_id'   => $this->integer()->unsigned()->notNull(),
            'planned_qty'  => $this->integer()->notNull(),
            'status'       => "ENUM('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned'",
            'planned_date' => $this->date()->null()->defaultValue(null),
            'completed_at' => $this->timestamp()->null()->defaultValue(null),
            'notes'        => $this->text()->null()->defaultValue(null),
            'created_by'   => $this->integer()->unsigned()->null()->defaultValue(null),
            'created_at'   => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'   => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'   => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey('fk_prod_order_order',      'production_order', 'order_id',   'order',   'id', 'SET NULL',  'CASCADE');
        $this->addForeignKey('fk_prod_order_product',    'production_order', 'product_id', 'product', 'id', 'RESTRICT',  'CASCADE');
        $this->addForeignKey('fk_prod_order_created_by', 'production_order', 'created_by', 'user',    'id', 'SET NULL',  'CASCADE');

        $this->createIndex('idx_prod_order_reference', 'production_order', 'reference');
        $this->createIndex('idx_prod_order_status',    'production_order', 'status');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_prod_order_order',      'production_order');
        $this->dropForeignKey('fk_prod_order_product',    'production_order');
        $this->dropForeignKey('fk_prod_order_created_by', 'production_order');
        $this->dropTable('production_order');
    }
}
