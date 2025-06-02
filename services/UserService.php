<?php

namespace app\services;

use Yii;
use app\models\User;
use Firebase\JWT\JWT;
use yii\web\HttpException;
use yii\web\UnauthorizedHttpException;
use app\exceptions\ValidationException;

class UserService
{
    public function register(array $data): array
    {
        $user = new User();
        $user->email = $data['email'] ?? null;
        $user->plainPassword = $data['password'] ?? null;

        if (!$user->validate()) {
            throw new ValidationException($user->getErrors());
        }

        if (User::find()->where(['email' => $user->email])->exists()) {
            throw new ValidationException([
                'email' => ['Este e-mail já está cadastrado.']
            ]);
        }

        $user->setPassword($user->plainPassword);

        if (!$user->save(false)) {
            throw new HttpException(500, json_encode([
                'message' => 'Erro ao salvar o usuário.'
            ], JSON_UNESCAPED_UNICODE));
        }

        return [
            'message' => 'Usuário cadastrado com sucesso.',
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ];
    }

    public function login(array $data): array
    {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $errors = [];

        if (empty($email)) {
            $errors['email'][] = 'Campo obrigatório.';
        }

        if (empty($password)) {
            $errors['password'][] = 'Campo obrigatório.';
        }

        if ($errors) {
            throw new ValidationException($errors, 'E-mail e senha são obrigatórios.');
        }

        $user = User::findOne(['email' => $email]);

        if (!$user || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('E-mail ou senha inválidos.');
        }

        $key = Yii::$app->params['jwtSecret'] ?? 'secret_key';

        $payload = [
            'iss' => 'http://localhost',
            'aud' => 'http://localhost',
            'iat' => time(),
            'exp' => time() + 3600,
            'uid' => $user->id,
            'email' => $user->email,
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        return [
            'message' => 'Login realizado com sucesso.',
            'token' => $jwt,
        ];
    }
}
