<?php

use yii\db\Migration;

/**
 * Creates the `user_role` table — a denormalised, queryable snapshot of
 * user→role assignments that mirrors `auth_assignment`.
 *
 * This table is kept in sync automatically by the application whenever
 * a role is assigned or revoked through RoleController / UserController.
 *
 * Having a dedicated table makes reporting queries simpler and allows
 * foreign-key relationships without depending on the RBAC schema.
 */
class m240001_000015_create_user_role_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('user_role', [
            'id'         => $this->primaryKey()->unsigned(),
            'user_id'    => $this->integer()->unsigned()->notNull(),
            'role_name'  => $this->string(64)->notNull(),
            'assigned_by'=> $this->integer()->unsigned()->null()->defaultValue(null)
                            ->comment('Admin user ID who made the assignment'),
            'assigned_at'=> $this->timestamp()->notNull()
                            ->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk_user_role_user',
            'user_role', 'user_id',
            'user', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->createIndex('idx_user_role_user',   'user_role', 'user_id');
        $this->createIndex('idx_user_role_role',   'user_role', 'role_name');
        $this->createIndex('uq_user_role',         'user_role', ['user_id', 'role_name'], true);

        // Back-fill from existing auth_assignment rows
        $this->execute("
            INSERT IGNORE INTO user_role (user_id, role_name)
            SELECT CAST(user_id AS UNSIGNED), item_name
            FROM auth_assignment
        ");

        echo "    > user_role table created and seeded from auth_assignment.\n";
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_user_role_user', 'user_role');
        $this->dropTable('user_role');
    }
}
