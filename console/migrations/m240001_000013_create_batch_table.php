<?php

use yii\db\Migration;

/**
 * Creates the `batch` table.
 *
 * A production order can have one or more batches.
 * Each batch tracks the physical brewing run.
 *
 * Workflow: planned → brewing → fermenting → packaging → completed
 *
 * actual_yield is filled in when the batch completes and determines
 * how many units are added to finished goods stock.
 */
class m240001_000013_create_batch_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('batch', [
            'id'                  => $this->primaryKey()->unsigned(),
            'production_order_id' => $this->integer()->unsigned()->notNull(),
            'batch_number'        => $this->string(30)->notNull()->unique(),
            'status'              => "ENUM('planned','brewing','fermenting','packaging','completed') NOT NULL DEFAULT 'planned'",
            'batch_size'          => $this->decimal(10, 2)->notNull()->comment('litres'),
            'actual_yield'        => $this->integer()->null()->defaultValue(null)->comment('finished units produced'),
            'brew_date'           => $this->date()->null()->defaultValue(null),
            'completion_date'     => $this->date()->null()->defaultValue(null),
            'notes'               => $this->text()->null()->defaultValue(null),
            'brew_master_id'      => $this->integer()->unsigned()->null()->defaultValue(null),
            'created_at'          => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'          => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'          => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey('fk_batch_prod_order',  'batch', 'production_order_id', 'production_order', 'id', 'CASCADE',  'CASCADE');
        $this->addForeignKey('fk_batch_brew_master', 'batch', 'brew_master_id',       'user',             'id', 'SET NULL', 'CASCADE');

        $this->createIndex('idx_batch_status',    'batch', 'status');
        $this->createIndex('idx_batch_prod_order','batch', 'production_order_id');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_batch_prod_order',  'batch');
        $this->dropForeignKey('fk_batch_brew_master', 'batch');
        $this->dropTable('batch');
    }
}
