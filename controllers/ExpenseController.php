<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\web\UnauthorizedHttpException;
use app\services\ExpenseService;
use app\exceptions\ValidationException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ExpenseController extends Controller
{
    public $enableCsrfValidation = false;
    private ExpenseService $expenseService;

    public function __construct($id, $module, ExpenseService $expenseService, $config = [])
    {
        $this->expenseService = $expenseService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    private function getCurrentUserId(): int
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        if (!$authHeader || !preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
            throw new UnauthorizedHttpException('Token JWT não fornecido.');
        }

        try {
            $key = Yii::$app->params['jwtSecret'];
            $decoded = JWT::decode($matches[1], new Key($key, 'HS256'));

            if (empty($decoded->uid)) {
                throw new UnauthorizedHttpException('Token inválido.');
            }

            return $decoded->uid;
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Token JWT inválido.');
        }
    }

    public function actionIndex(): array
    {
        try {
            $userId = $this->getCurrentUserId();
            $filters = Yii::$app->request->get();
            return $this->expenseService->listByUser($userId, $filters);
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        try {
            $userId = $this->getCurrentUserId();
            return $this->expenseService->getById((int)$id, $userId);
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCreate(): array
    {
        try {
            $userId = $this->getCurrentUserId();
            $data = Yii::$app->request->bodyParams;

            $expense = $this->expenseService->create($data, $userId);
            Yii::$app->response->statusCode = 201;

            return [
                'message' => 'Despesa cadastrada com sucesso.',
                'data' => $expense
            ];
        } catch (ValidationException $e) {
            Yii::$app->response->statusCode = 400;
            return json_decode($e->getMessage(), true);
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate($id): array
    {
        try {
            $userId = $this->getCurrentUserId();
            $data = Yii::$app->request->bodyParams;

            $expense = $this->expenseService->update((int)$id, $data, $userId);

            return [
                'message' => 'Despesa atualizada com sucesso.',
                'data' => $expense
            ];
        } catch (ValidationException $e) {
            Yii::$app->response->statusCode = 400;
            return json_decode($e->getMessage(), true);
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete($id): ?array
    {
        try {
            $userId = $this->getCurrentUserId();
            $this->expenseService->delete((int)$id, $userId);
            Yii::$app->response->statusCode = 204;
            return null;
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['message' => $e->getMessage()];
        }
    }
}
