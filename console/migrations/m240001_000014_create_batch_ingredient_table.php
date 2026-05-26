<?php

use yii\db\Migration;

/**
 * Creates the `batch_ingredient` table.
 *
 * Records the raw materials consumed by a batch.
 * The brewmaster populates this before brewing starts.
 * On transition to "brewing", all quantities are deducted from
 * raw_material.stock_qty and StockTransaction rows are written.
 */
class m240001_000014_create_batch_ingredient_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('batch_ingredient', [
            'id'              => $this->primaryKey()->unsigned(),
            'batch_id'        => $this->integer()->unsigned()->notNull(),
            'raw_material_id' => $this->integer()->unsigned()->notNull(),
            'quantity'        => $this->decimal(12, 3)->notNull(),
            'created_at'      => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'      => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'      => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey('fk_ingredient_batch', 'batch_ingredient', 'batch_id',        'batch',        'id', 'CASCADE',  'CASCADE');
        $this->addForeignKey('fk_ingredient_raw',   'batch_ingredient', 'raw_material_id', 'raw_material', 'id', 'RESTRICT', 'CASCADE');

        $this->createIndex('idx_ingredient_batch', 'batch_ingredient', 'batch_id');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_ingredient_batch', 'batch_ingredient');
        $this->dropForeignKey('fk_ingredient_raw',   'batch_ingredient');
        $this->dropTable('batch_ingredient');
    }
}
