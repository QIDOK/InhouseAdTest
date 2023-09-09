<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\UrlStatuses;

class CheckStatusController extends Controller {
    public function actionStatistics() {
        $statuses = UrlStatuses::find()->where("updated_at >= '" . date("Y-m-d h:i:s", time() - 3600 * 24) . "'")->andWhere("status_code != 200")->all();
        foreach ($statuses as $status) {
            $this->stdout("\n Код ответа страницы $status->url: $status->status_code");
        }
    }



    public function actionCronChecker(string $data) {
        $data = json_decode($data);
        for ($i = 0; $i < count($data->url); $i++) {
            $model = UrlStatuses::findOne(['hash_string' => md5($data->url[$i])]);

            if($model && !$model->enabled) {
                unset($data->url[$i]);
            }
        }
        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_URL, "api.testwork.local/api/v1/check-status");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );

        $response = json_decode(curl_exec($curl));
        $this->stdout($response);

        if(!empty($response)) {
            foreach ($response as $element) {
                if ($element->code != 200) {
                    $model = UrlStatuses::findOne(['hash_string' => md5($element->url)]);

                    $model->error_count += 1;

                    if ($model->error_count >= 5) {
                        $model->enabled = 0;
                    }

                    $model->save();
                }
            }
        }
    }
}
