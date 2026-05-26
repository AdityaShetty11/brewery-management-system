<?php

use yii\db\Migration;

/**
 * Creates the `audit_log` table for tracking critical system actions.
 */
class m240001_000002_create_audit_log_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('audit_log', [
            'id'         => $this->primaryKey()->unsigned(),
            'user_id'    => $this->integer()->unsigned()->null()->defaultValue(null),
            'action'     => $this->string(100)->notNull(),
            'model'      => $this->string(100)->null()->defaultValue(null),
            'model_id'   => $this->integer()->unsigned()->null()->defaultValue(null),
            'old_value'  => $this->json()->null()->defaultValue(null),
            'new_value'  => $this->json()->null()->defaultValue(null),
            'ip_address' => $this->string(45)->null()->defaultValue(null),
            'user_agent' => $this->string(255)->null()->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey(
            'fk_audit_log_user',
            'audit_log', 'user_id',
            'user', 'id',
            'SET NULL', 'CASCADE'
        );

        $this->createIndex('idx_audit_action',   'audit_log', 'action');
        $this->createIndex('idx_audit_model',    'audit_log', ['model', 'model_id']);
        $this->createIndex('idx_audit_user',     'audit_log', 'user_id');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_audit_log_user', 'audit_log');
        $this->dropTable('audit_log');
    }
}
