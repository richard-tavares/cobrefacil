<?php

class UserCest
{
    public function registerUser(ApiTester $tester)
    {
        $tester->sendPOST('/user/register', [
            'email' => 'test@example.com',
            'password' => '123456',
        ]);
        $tester->seeResponseCodeIs(201);
        $tester->seeResponseContainsJson([
            'message' => 'Usuário cadastrado com sucesso.'
        ]);
    }

    public function loginUser(ApiTester $tester)
    {
        $tester->sendPOST('/user/login', [
            'email' => 'test@example.com',
            'password' => '123456',
        ]);
        $tester->seeResponseCodeIs(200);
        $tester->seeResponseContains('token');
    }
}
