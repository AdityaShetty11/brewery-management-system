<?php

use yii\db\Migration;

/**
 * Adds an `image` column to the `product` table to store the uploaded filename.
 */
class m240002_000001_add_image_to_product_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn(
            'product',
            'image',
            $this->string(255)->null()->defaultValue(null)->after('description')
        );
    }

    public function safeDown(): void
    {
        $this->dropColumn('product', 'image');
    }
}
