<?php

use yii\db\Migration;

/**
 * Creates the `user` table.
 *
 * Status codes:
 *   0  = deleted
 *   9  = inactive (email not verified)
 *  10  = active
 */
class m240001_000001_create_user_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('user', [
            'id'                    => $this->primaryKey()->unsigned(),
            'username'              => $this->string(50)->notNull()->unique(),
            'email'                 => $this->string(100)->notNull()->unique(),
            'password_hash'         => $this->string(255)->notNull(),
            'auth_key'              => $this->string(100)->notNull(),
            'verification_token'    => $this->string(100)->null()->defaultValue(null),
            'password_reset_token'  => $this->string(100)->null()->defaultValue(null),
            'status'                => $this->tinyInteger()->notNull()->defaultValue(9),
            'created_at'            => $this->integer()->notNull(),
            'updated_at'            => $this->integer()->notNull(),
            'deleted_at'            => $this->integer()->null()->defaultValue(null),
        ]);

        $this->createIndex('idx_user_email',  'user', 'email');
        $this->createIndex('idx_user_status', 'user', 'status');
    }

    public function safeDown(): void
    {
        $this->dropTable('user');
    }
}
