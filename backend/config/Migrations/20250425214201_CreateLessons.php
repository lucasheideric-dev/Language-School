<?php

use Migrations\AbstractMigration;

class CreateLessons extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons');
        $table->addColumn('student_id', 'integer')
            ->addColumn('teacher_id', 'integer')
            ->addColumn('lesson_date', 'timestamp')
            ->addColumn('content', 'string', ['limit' => 25])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('status', 'string', ['limit' => 25, 'null' => true])
            ->addPrimaryKey('id')
            ->addForeignKey('student_id', 'students', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('teacher_id', 'teachers', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
