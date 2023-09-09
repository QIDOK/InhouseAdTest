<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "url_statuses".
 *
 * @property string $hash_string
 * @property string $url
 * @property int $status_code
 * @property int $query_count
 * @property int $enabled
 * @property int $error_count
 * @property string $created_at
 * @property string $updated_at
 */
class UrlStatuses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'url_statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hash_string', 'url', 'status_code'], 'required'],
            [['status_code', 'query_count', 'enabled', 'error_count'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['hash_string'], 'string', 'max' => 32],
            [['url'], 'string', 'max' => 256],
            [['hash_string'], 'unique'],
            [['url'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hash_string' => 'Hash String',
            'url' => 'Url',
            'status_code' => 'Status Code',
            'query_count' => 'Query Count',
            'enabled' => 'Enabled',
            'error_count' => 'Error Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
