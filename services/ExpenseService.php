<?php

namespace app\services;

use app\models\Expense;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use app\exceptions\ValidationException;

class ExpenseService
{
    public function listByUser(int $userId, array $filters = []): array
    {
        $query = Expense::find()->where(['user_id' => $userId]);

        if (!empty($filters['date'])) {
            $query->andWhere(['date' => $filters['date']]);
        }

        if (!empty($filters['category'])) {
            $query->andWhere(['category' => $filters['category']]);
        }

        return $query->orderBy(['date' => SORT_DESC])->all();
    }

    public function getById(int $id, int $userId): Expense
    {
        $expense = Expense::findOne(['id' => $id, 'user_id' => $userId]);

        if (!$expense) {
            throw new NotFoundHttpException('Despesa não encontrada.');
        }

        return $expense;
    }

    public function create(array $data, int $userId): Expense
    {
        $expense = new Expense();
        $expense->user_id = $userId;
        $expense->load($data, '');

        if (!$expense->validate()) {
            throw new ValidationException($expense->getErrors());
        }

        if (!$expense->save(false)) {
            throw new HttpException(400, json_encode([
                'message' => 'Erro ao salvar a despesa.'
            ], JSON_UNESCAPED_UNICODE));
        }

        return $expense;
    }

    public function update(int $id, array $data, int $userId): Expense
    {
        $expense = $this->getById($id, $userId);
        $expense->load($data, '');

        if (!$expense->validate()) {
            throw new ValidationException($expense->getErrors());
        }

        if (!$expense->save(false)) {
            throw new HttpException(400, json_encode([
                'message' => 'Erro ao atualizar a despesa.'
            ], JSON_UNESCAPED_UNICODE));
        }

        return $expense;
    }

    public function delete(int $id, int $userId): void
    {
        $expense = $this->getById($id, $userId);

        if (!$expense->delete()) {
            throw new HttpException(400, json_encode([
                'message' => 'Erro ao deletar a despesa.'
            ], JSON_UNESCAPED_UNICODE));
        }
    }
}
