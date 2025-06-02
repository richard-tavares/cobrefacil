<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public function actionIndex()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => 'ok', 'message' => 'Cobrefacil API is running ✅'];
    }
}
