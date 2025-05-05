<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TeachersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('teachers');
        $this->setDisplayField('first_name');
        $this->setPrimaryKey('id');

        $this->hasMany('Lessons', [
            'foreignKey' => 'teacher_id',
        ]);

        $this->addBehavior('Timestamp');
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['cpf'], 'CPF já cadastrado.'));
        $rules->add($rules->isUnique(['email'], 'E-mail já cadastrado.'));

        return $rules;
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
            ->date('birth_date')
            ->allowEmptyDate('birth_date');

        $validator
            ->maxLength('specialty', 255, 'A especialidade não pode exceder 255 caracteres')
            ->allowEmptyString('specialty');

        $validator
            ->boolean('status', 'Status deve ser um valor booleano')
            ->allowEmptyString('status');


        return $validator;
    }
}
