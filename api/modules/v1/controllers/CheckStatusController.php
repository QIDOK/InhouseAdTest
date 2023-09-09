<?php

namespace api\modules\v1\controllers;

use http\Header;
use Yii;
use yii\rest\ActiveController;
use yii\helpers\Json;

class CheckStatusController extends ActiveController
{
    public $modelClass = "common\models\UrlStatuses";

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ]
        ];
        return $behaviors;
    }

    public function getHttpCode($url): string
    {
        try {
            file_get_contents($url, null, stream_context_create(['http' => ['method' => "GET"]]));
            return intval(substr($http_response_header[0], 9, 3));
        } catch (\Exception $e) {
            return 403;
        }
    }

    public function actionIndex()
    {
        $url = Yii::$app->request->post('url');
        if(!$url) return false;
        $url = json_decode($url);
        $codes = [];
        foreach ($url as $element) {
            if (strtotime(file_get_contents('temp/lastQuery.json')) + 5 >= time()) {
                $codes[] = [
                    "url" => $element,
                    "code" => 0
                ];
                continue;
            }


            $model = $this->modelClass::findOne(['hash_string' => md5($element)]);
            if($model) {
                if (strtotime($model->updated_at) + 600 < time()) {
                    $model->status_code = $this->getHttpCode($element);
                    $model->updated_at = date('Y-m-d H:i:s', time());
                }

                $model->query_count += 1;
            } else {
                $model = new $this->modelClass([
                    'hash_string' => md5($element),
                    'url' => $element,
                    'status_code' => $this->getHttpCode($element)
                ]);
            }

            $codes[] = [
                "url" => $element,
                "code" => $model->status_code
            ];
            $model->save();
        }

        if (strtotime(file_get_contents('temp/lastQuery.json')) + 5 < time()) {
            file_put_contents('temp/lastQuery.json', date('Y-m-d H:i:s', time()));
        }

        return $codes;
    }
}
