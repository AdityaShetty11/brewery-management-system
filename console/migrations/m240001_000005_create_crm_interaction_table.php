<?php

use yii\db\Migration;

/**
 * Creates the `crm_interaction` table.
 * Logs every touchpoint (call, email, meeting, note) with a customer company.
 */
class m240001_000005_create_crm_interaction_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('crm_interaction', [
            'id'             => $this->primaryKey()->unsigned(),
            'company_id'     => $this->integer()->unsigned()->notNull(),
            'contact_id'     => $this->integer()->unsigned()->null()->defaultValue(null),
            'staff_id'       => $this->integer()->unsigned()->notNull(),
            'type'           => "ENUM('call','email','meeting','note') NOT NULL DEFAULT 'note'",
            'summary'        => $this->text()->notNull(),
            'interaction_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_at'     => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'     => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'     => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey(
            'fk_interaction_company',
            'crm_interaction', 'company_id',
            'customer_company', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_interaction_contact',
            'crm_interaction', 'contact_id',
            'customer_contact', 'id',
            'SET NULL', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_interaction_staff',
            'crm_interaction', 'staff_id',
            'user', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->createIndex('idx_interaction_company', 'crm_interaction', 'company_id');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_interaction_company', 'crm_interaction');
        $this->dropForeignKey('fk_interaction_contact', 'crm_interaction');
        $this->dropForeignKey('fk_interaction_staff',   'crm_interaction');
        $this->dropTable('crm_interaction');
    }
}
