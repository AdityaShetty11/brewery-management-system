<?php

use yii\db\Migration;

/**
 * Creates the `customer_company` table.
 * Represents a B2B customer organisation in the CRM.
 */
class m240001_000003_create_customer_company_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('customer_company', [
            'id'         => $this->primaryKey()->unsigned(),
            'name'       => $this->string(150)->notNull(),
            'industry'   => $this->string(100)->null()->defaultValue(null),
            'phone'      => $this->string(30)->null()->defaultValue(null),
            'email'      => $this->string(100)->null()->defaultValue(null),
            'address'    => $this->text()->null()->defaultValue(null),
            'city'       => $this->string(100)->null()->defaultValue(null),
            'country'    => $this->string(100)->null()->defaultValue(null),
            'notes'      => $this->text()->null()->defaultValue(null),
            'created_by' => $this->integer()->unsigned()->null()->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null()->defaultValue(null),
        ]);

        $this->addForeignKey(
            'fk_company_created_by',
            'customer_company', 'created_by',
            'user', 'id',
            'SET NULL', 'CASCADE'
        );

        $this->createIndex('idx_company_name', 'customer_company', 'name');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_company_created_by', 'customer_company');
        $this->dropTable('customer_company');
    }
}
