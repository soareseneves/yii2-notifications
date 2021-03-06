<?php

use yii\db\Migration;

/**
 * Class m010101_100001_init_notifications
 */
class m010101_100001_init_notifications extends Migration
{
    /**
     * Create table `notifications`
     */
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // notifications
        $this->createTable('{{%local_notifications}}', [
            'id' => $this->primaryKey(),
            'class' => $this->string(64)->notNull(),
            'title' => $this->string(60),
            'body' => $this->string(255)->notNull(),
            'icon_class' => $this->string(60)->notNull()->defaultValue('fa fa-flag text-sw'),
            'click_action' => $this->string(255)->notNull(),
            'seen' => $this->boolean()->notNull()->defaultValue(false),
            'read' => $this->boolean()->notNull()->defaultValue(false),
            'user_id' => $this->integer(11)->unsigned()->notNull(),
            'users_notification_id' => $this->integer(11)->unsigned()->notNull(),
            'created_at' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->createIndex('index_2', '{{%local_notifications}}', ['user_id']);
        $this->createIndex('index_3', '{{%local_notifications}}', ['created_at']);
        $this->createIndex('index_4', '{{%local_notifications}}', ['seen']);

    }

    /**
     * Drop table `notifications`
     */
    public function down()
    {
        $this->dropTable('{{%local_notifications}}');
    }
}
