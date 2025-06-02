<?php

namespace unit\services;

use Codeception\Test\Unit;
use app\models\Expense;
use app\models\User;
use app\services\ExpenseService;
use app\exceptions\ValidationException;
use yii\web\NotFoundHttpException;
use Yii;

class ExpenseServiceTest extends Unit
{
    private ExpenseService $service;
    private int $userId;

    protected function _before()
    {
        $this->service = new ExpenseService();

        $user = new User();
        $user->email = 'john.doe@example.com';
        $user->password_hash = Yii::$app->security->generatePasswordHash('p@ssw0rd');
        $user->created_at = date('Y-m-d H:i:s');
        $user->save(false);

        $this->userId = $user->id;
    }

    protected function _after()
    {
        Expense::deleteAll(['user_id' => $this->userId]);
        User::deleteAll(['id' => $this->userId]);
    }

    public function testCreateExpenseWithInvalidData()
    {
        $this->expectException(ValidationException::class);
        $this->service->create([], $this->userId);
    }

    public function testCreateExpenseSuccessfully()
    {
        $data = [
            'description' => 'Teste Padaria',
            'category' => 'alimentação',
            'amount' => 19.99,
            'date' => date('Y-m-d'),
        ];

        $expense = $this->service->create($data, $this->userId);

        $this->assertInstanceOf(Expense::class, $expense);
        $this->assertEquals('Teste Padaria', $expense->description);
        $this->assertEquals('alimentação', $expense->category);
        $this->assertEquals(19.99, $expense->amount);
        $this->assertEquals($this->userId, $expense->user_id);
    }

    public function testUpdateExpenseSuccessfully()
    {
        $created = $this->service->create([
            'description' => 'Antigo',
            'category' => 'transporte',
            'amount' => 30,
            'date' => date('Y-m-d'),
        ], $this->userId);

        $updated = $this->service->update($created->id, [
            'description' => 'Atualizado',
            'category' => 'lazer',
            'amount' => 45.5,
            'date' => date('Y-m-d'),
        ], $this->userId);

        $this->assertEquals('Atualizado', $updated->description);
        $this->assertEquals('lazer', $updated->category);
        $this->assertEquals(45.5, $updated->amount);
    }

    public function testGetByIdWithWrongUser()
    {
        $created = $this->service->create([
            'description' => 'Acesso indevido',
            'category' => 'lazer',
            'amount' => 50,
            'date' => date('Y-m-d'),
        ], $this->userId);

        $this->expectException(NotFoundHttpException::class);
        $this->service->getById($created->id, $this->userId + 1);
    }

    public function testDeleteExpense()
    {
        $created = $this->service->create([
            'description' => 'Excluir',
            'category' => 'alimentação',
            'amount' => 10,
            'date' => date('Y-m-d'),
        ], $this->userId);

        $this->service->delete($created->id, $this->userId);

        $this->expectException(NotFoundHttpException::class);
        $this->service->getById($created->id, $this->userId);
    }
}
