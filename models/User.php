<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $password_hash
 *
 * @property Expense[] $expenses
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $plainPassword;

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            ['email', 'required', 'message' => 'O campo e-mail é obrigatório.'],
            ['email', 'email', 'message' => 'O e-mail informado não é válido.'],

            ['plainPassword', 'required', 'message' => 'A senha é obrigatória.'],
            ['plainPassword', 'string', 'min' => 6, 'tooShort' => 'A senha deve conter no mínimo 6 caracteres.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
            'plainPassword' => 'Senha',
        ];
    }

    public function getExpenses()
    {
        return $this->hasMany(Expense::class, ['user_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return '';
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function validatePassword($password)
    {
        return password_verify($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
    }
}
