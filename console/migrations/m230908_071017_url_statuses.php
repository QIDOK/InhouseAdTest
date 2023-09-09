<?php

use yii\db\Migration;

/**
 * Class m230908_071017_url_statuses
 */
class m230908_071017_url_statuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("url_statuses", [
            'hash_string' => $this->string(32)->notNull()->unique(),
            'url' => $this->string(256)->notNull()->unique(),
            'status_code'   => $this->integer(3)->notNull(),
            'query_count'   => $this->integer()->defaultValue(1)->notNull(),
            'enabled'       => $this->boolean()->notNull()->defaultValue(true),
            'error_count'   => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'    => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        Yii::$app->runAction("gii/model", ["tableName"=>"url_statuses", "modelClass"=>"UrlStatuses", "ns"=>"\\common\\models"]);
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("url_statuses");
    }
}
