<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "expense".
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property string $category
 * @property float $amount
 * @property string $date
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property User $user
 */
class Expense extends ActiveRecord
{
    public static function tableName()
    {
        return 'expense';
    }

    public function rules()
    {
        return [
            [['user_id', 'description', 'category', 'amount', 'date'], 'required', 'message' => 'O campo {attribute} é obrigatório.'],
            ['user_id', 'integer', 'message' => 'O campo {attribute} deve ser um número inteiro.'],
            ['description', 'string', 'max' => 255, 'tooLong' => 'A descrição não pode ultrapassar 255 caracteres.'],
            ['category', 'in', 'range' => ['alimentação', 'transporte', 'lazer'], 'message' => 'A categoria deve ser alimentação, transporte ou lazer.'],
            ['amount', 'number', 'min' => 0, 'tooSmall' => 'O valor deve ser igual ou maior que zero.', 'message' => 'O valor informado não é válido.'],
            ['date', 'date', 'format' => 'php:Y-m-d', 'message' => 'A data deve estar no formato YYYY-MM-DD.'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Usuário',
            'description' => 'Descrição',
            'category' => 'Categoria',
            'amount' => 'Valor',
            'date' => 'Data da Despesa',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function find()
    {
        return parent::find()->andWhere(['deleted_at' => null]);
    }

    public function delete()
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false, ['deleted_at']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $now = date('Y-m-d H:i:s');
            $this->updated_at = $now;
            if ($this->isNewRecord) {
                $this->created_at = $now;
            }
            return true;
        }
        return false;
    }
}
