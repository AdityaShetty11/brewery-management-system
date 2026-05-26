<?php

use yii\db\Migration;

/**
 * Creates the `raw_material` table.
 *
 * stock_qty is the live quantity on hand.
 * reorder_level triggers the low-stock dashboard alert.
 */
class m240001_000011_create_raw_material_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('raw_material', [
            'id'            => $this->primaryKey()->unsigned(),
            'name'          => $this->string(150)->notNull(),
            'unit'          => $this->string(30)->notNull(),
            'stock_qty'     => $this->decimal(12, 3)->notNull()->defaultValue(0),
            'reorder_level' => $this->decimal(12, 3)->notNull()->defaultValue(0),
            'description'   => $this->text()->null()->defaultValue(null),
            'created_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at'    => $this->timestamp()->null()->defaultValue(null),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('raw_material');
    }
}
