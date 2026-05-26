<?php

use yii\db\Migration;

/**
 * Creates the `stock_transaction` table.
 *
 * Polymorphic design: item_type + item_id point to either
 *   raw_material.id  (item_type = 'raw_material')
 *   product.id       (item_type = 'finished_good')
 *
 * This avoids two separate transaction tables and keeps inventory
 * history centralised for reporting.
 */
class m240001_000008_create_stock_transaction_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('stock_transaction', [
            'id'               => $this->primaryKey()->unsigned(),
            'item_type'        => "ENUM('raw_material','finished_good') NOT NULL",
            'item_id'          => $this->integer()->unsigned()->notNull(),
            'transaction_type' => "ENUM('in','out','adjustment') NOT NULL",
            'quantity'         => $this->decimal(12, 3)->notNull(),
            'reference_type'   => $this->string(50)->null()->defaultValue(null),
            'reference_id'     => $this->integer()->unsigned()->null()->defaultValue(null),
            'notes'            => $this->text()->null()->defaultValue(null),
            'created_by'       => $this->integer()->unsigned()->null()->defaultValue(null),
            'created_at'       => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'       => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'       => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey(
            'fk_stock_tx_user',
            'stock_transaction', 'created_by',
            'user', 'id',
            'SET NULL', 'CASCADE'
        );

        $this->createIndex('idx_stock_tx_item',      'stock_transaction', ['item_type', 'item_id']);
        $this->createIndex('idx_stock_tx_reference', 'stock_transaction', ['reference_type', 'reference_id']);
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_stock_tx_user', 'stock_transaction');
        $this->dropTable('stock_transaction');
    }
}
