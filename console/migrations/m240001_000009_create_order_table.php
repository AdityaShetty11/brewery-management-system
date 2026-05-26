<?php

use yii\db\Migration;

/**
 * Creates the `order` table.
 *
 * Workflow: draft → confirmed → in_production → delivered | cancelled
 *
 * order_number is a human-readable reference (ORD-2024-00001).
 * total_amount is denormalised and recalculated whenever items change.
 * company_id is optional — links a B2B order to a CRM company.
 */
class m240001_000009_create_order_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('order', [
            'id'           => $this->primaryKey()->unsigned(),
            'order_number' => $this->string(30)->notNull()->unique(),
            'customer_id'  => $this->integer()->unsigned()->notNull(),
            'company_id'   => $this->integer()->unsigned()->null()->defaultValue(null),
            'status'       => "ENUM('draft','confirmed','in_production','delivered','cancelled') NOT NULL DEFAULT 'draft'",
            'total_amount' => $this->decimal(12, 2)->notNull()->defaultValue(0.00),
            'notes'        => $this->text()->null()->defaultValue(null),
            'confirmed_at' => $this->timestamp()->null()->defaultValue(null),
            'delivered_at' => $this->timestamp()->null()->defaultValue(null),
            'created_at'   => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'   => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'   => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey('fk_order_customer', 'order', 'customer_id', 'user', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_order_company',  'order', 'company_id',  'customer_company', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx_order_customer',     'order', 'customer_id');
        $this->createIndex('idx_order_status',       'order', 'status');
        $this->createIndex('idx_order_number',       'order', 'order_number');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_order_customer', 'order');
        $this->dropForeignKey('fk_order_company',  'order');
        $this->dropTable('order');
    }
}
