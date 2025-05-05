<?php

use Migrations\AbstractMigration;

class CreateGuardianForms extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('guardian_forms');
        $table->addColumn('lesson_id', 'integer')
            ->addColumn('signed', 'boolean', ['default' => false])
            ->addColumn('pdf_path', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addPrimaryKey('id')
            ->addForeignKey('lesson_id', 'lessons', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
