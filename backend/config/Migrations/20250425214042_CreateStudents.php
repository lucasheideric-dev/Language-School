<?php

use Migrations\AbstractMigration;

class CreateStudents extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('students');

        $table->addColumn('cpf', 'string', ['limit' => 14])
            ->addColumn('first_name', 'string', ['limit' => 100])
            ->addColumn('last_name', 'string', ['limit' => 100])
            ->addColumn('birth_date', 'date', ['null' => true])
            ->addColumn('postal_code', 'string', ['limit' => 9])
            ->addColumn('street', 'string', ['limit' => 255])
            ->addColumn('house_number', 'string', ['limit' => 10])
            ->addColumn('neighborhood', 'string', ['limit' => 100])
            ->addColumn('state', 'string', ['limit' => 2])
            ->addColumn('city', 'string', ['limit' => 100])
            ->addColumn('phone', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('whatsapp', 'string', ['limit' => 20])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => true])
            ->addPrimaryKey('id')
            ->addIndex(['cpf'], ['unique' => true])
            ->create();
    }
}
