<?php

use Migrations\AbstractMigration;

class CreateTeachers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('teachers');

        $table->addColumn('cpf', 'string', ['limit' => 14])
            ->addColumn('first_name', 'string', ['limit' => 100])
            ->addColumn('last_name', 'string', ['limit' => 100])
            ->addColumn('birth_date', 'date', ['null' => true])
            ->addColumn('specialty', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('status', 'boolean', ['default' => true])
            ->addPrimaryKey('id')
            ->addIndex(['cpf'], ['unique' => true])
            ->create();
    }
}
