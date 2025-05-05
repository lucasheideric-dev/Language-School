<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('public.users');
        $this->setPrimaryKey('id');
        $this->setDisplayField('email');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('first_name', 'Primeiro nome é obrigatório')
            ->maxLength('first_name', 100, 'O primeiro nome não pode exceder 100 caracteres');

        $validator
            ->notEmptyString('last_name', 'Sobrenome é obrigatório')
            ->maxLength('last_name', 100, 'O sobrenome não pode exceder 100 caracteres');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email', 'Email é obrigatório')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Este email já está em uso.'
            ]);

        return $validator;
    }
}
