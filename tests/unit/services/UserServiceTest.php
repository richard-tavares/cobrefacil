<?php

namespace tests\unit\services;

use app\services\UserService;
use app\models\User;
use yii\base\Exception;
use yii\web\UnauthorizedHttpException;
use Yii;

class UserServiceTest extends \Codeception\Test\Unit
{
    private UserService $service;

    protected function _before()
    {
        $this->service = new UserService();
    }

    public function testRegisterWithInvalidEmail()
    {
        $this->expectException(\yii\web\HttpException::class);

        $this->service->register([
            'email' => '',
            'password' => '123456'
        ]);
    }

    public function testRegisterWithInvalidPassword()
    {
        $this->expectException(\yii\web\HttpException::class);

        $this->service->register([
            'email' => 'john.doe@example.com',
            'password' => ''
        ]);
    }

    public function testRegisterWithDuplicatedEmail()
    {
        $email = 'duplicated@example.com';

        $user = new User();
        $user->email = $email;
        $user->setPassword('123456');
        $user->save(false);

        $this->expectException(\yii\web\HttpException::class);

        $this->service->register([
            'email' => $email,
            'password' => '123456'
        ]);
    }

    public function testRegisterWithValidData()
    {
        $email = 'unit_user_' . time() . '@test.com';

        $result = $this->service->register([
            'email' => $email,
            'password' => '123456'
        ]);

        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('Usuário cadastrado com sucesso.', $result['message']);
        $this->assertEquals($email, $result['data']['email']);
    }

    public function testLoginWithInvalidData()
    {
        $this->expectException(\yii\web\HttpException::class);

        $this->service->login([
            'email' => '',
            'password' => ''
        ]);
    }

    public function testLoginWithNonexistentUser()
    {
        $this->expectException(UnauthorizedHttpException::class);

        $this->service->login([
            'email' => 'inexistente@example.com',
            'password' => 'senhaerrada'
        ]);
    }

    public function testLoginWithWrongPassword()
    {
        $email = 'senhaerrada@example.com';

        $user = new User();
        $user->email = $email;
        $user->setPassword('correta');
        $user->save(false);

        $this->expectException(UnauthorizedHttpException::class);

        $this->service->login([
            'email' => $email,
            'password' => 'errada'
        ]);
    }

    public function testLoginWithValidCredentials()
    {
        $email = 'login_user_' . time() . '@test.com';

        $user = new User();
        $user->email = $email;
        $user->setPassword('123456');
        $user->save(false);

        $result = $this->service->login([
            'email' => $email,
            'password' => '123456'
        ]);

        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('Login realizado com sucesso.', $result['message']);
        $this->assertArrayHasKey('token', $result);
        $this->assertNotEmpty($result['token']);
    }
}
