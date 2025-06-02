<?php

class ExpenseCest
{
    private string $token;

    public function _before(ApiTester $tester)
    {
        $response = $tester->sendPOST('/user/login', [
            'email' => 'john.doe@example.com',
            'password' => '123456',
        ]);

        $this->token = $tester->grabDataFromResponseByJsonPath('$.token')[0];
        $tester->amBearerAuthenticated($this->token);
    }

    public function createExpense(ApiTester $tester)
    {
        $tester->sendPOST('/expense', [
            'description' => 'Padaria do Bill',
            'category' => 'alimentação',
            'amount' => 47.60,
            'date' => date('Y-m-d'),
        ]);
        $tester->seeResponseCodeIs(201);
        $tester->seeResponseContainsJson([
            'message' => 'Despesa cadastrada com sucesso.',
        ]);
    }

    public function listExpenses(ApiTester $tester)
    {
        $tester->sendGET('/expense');
        $tester->seeResponseCodeIs(200);
        $tester->seeResponseIsJson();
    }
}
