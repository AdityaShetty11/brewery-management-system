<?php

use yii\db\Migration;

/**
 * Creates the `customer_contact` table.
 * Represents a named individual at a customer company.
 * Optionally linked to a portal user account.
 */
class m240001_000004_create_customer_contact_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('customer_contact', [
            'id'         => $this->primaryKey()->unsigned(),
            'company_id' => $this->integer()->unsigned()->notNull(),
            'user_id'    => $this->integer()->unsigned()->null()->defaultValue(null),
            'first_name' => $this->string(75)->notNull(),
            'last_name'  => $this->string(75)->notNull(),
            'email'      => $this->string(100)->null()->defaultValue(null),
            'phone'      => $this->string(30)->null()->defaultValue(null),
            'role'       => $this->string(100)->null()->defaultValue(null),
            'notes'      => $this->text()->null()->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey(
            'fk_contact_company',
            'customer_contact', 'company_id',
            'customer_company', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_contact_user',
            'customer_contact', 'user_id',
            'user', 'id',
            'SET NULL', 'CASCADE'
        );

        $this->createIndex('idx_contact_company', 'customer_contact', 'company_id');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_contact_company', 'customer_contact');
        $this->dropForeignKey('fk_contact_user',    'customer_contact');
        $this->dropTable('customer_contact');
    }
}
