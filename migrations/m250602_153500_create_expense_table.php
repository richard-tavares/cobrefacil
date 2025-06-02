<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expense}}`.
 */
class m250602_153500_create_expense_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%expense}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->string()->notNull(),
            'category' => "ENUM('alimentação', 'transporte', 'lazer') NOT NULL",
            'amount' => $this->decimal(10, 2)->notNull(),
            'date' => $this->date()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null()->append('ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null()
        ]);

        $this->addForeignKey(
            'fk-expense-user_id',
            '{{%expense}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-expense-user_id', '{{%expense}}');
        $this->dropTable('{{%expense}}');
    }
}
