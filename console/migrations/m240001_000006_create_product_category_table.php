<?php

use yii\db\Migration;

/**
 * Creates the `product_category` table.
 */
class m240001_000006_create_product_category_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('product_category', [
            'id'          => $this->primaryKey()->unsigned(),
            'name'        => $this->string(100)->notNull()->unique(),
            'description' => $this->text()->null()->defaultValue(null),
            'created_at'  => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'  => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'  => $this->timestamp()->null()->defaultValue(null),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('product_category');
    }
}
