<?php

namespace app\components;

use Yii;
use yii\filters\auth\AuthInterface;
use yii\web\UnauthorizedHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\models\User;

class JwtAuth implements AuthInterface
{
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader && preg_match('/^Bearer\s+(.*)$/', $authHeader, $matches)) {
            $token = $matches[1];
            try {
                $key = $_ENV['JWT_SECRET'] ?? 'secret_key';
                $decoded = JWT::decode($token, new Key($key, 'HS256'));

                $identity = User::findOne($decoded->uid ?? null);
                if ($identity) {
                    $user->login($identity);
                    return $identity;
                }
            } catch (\Throwable $e) {
                throw new UnauthorizedHttpException('Token inválido ou expirado.');
            }
        }

        return null;
    }

    public function challenge($response)
    {
        $response->getHeaders()->set('WWW-Authenticate', 'Bearer realm="api"');
    }

    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Requer Autenticação.');
    }
}
