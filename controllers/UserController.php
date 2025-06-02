<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\services\UserService;
use app\exceptions\ValidationException;
use yii\web\HttpException;

class UserController extends Controller
{
    public $enableCsrfValidation = false;
    private UserService $userService;

    public function __construct($id, $module, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actionRegister(): array
    {
        try {
            $body = Yii::$app->request->bodyParams;
            $response = $this->userService->register($body);

            Yii::$app->response->statusCode = 201;
            return $response;
        } catch (ValidationException $e) {
            Yii::$app->response->statusCode = 400;
            return json_decode($e->getMessage(), true);
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionLogin(): array
    {
        try {
            $body = Yii::$app->request->bodyParams;
            return $this->userService->login($body);
        } catch (ValidationException $e) {
            Yii::$app->response->statusCode = 400;
            return json_decode($e->getMessage(), true);
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['message' => $e->getMessage()];
        }
    }
}
