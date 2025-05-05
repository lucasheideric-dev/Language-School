<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class GuardianFormsFixture extends TestFixture
{
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'lesson_id' => 1,
                'signed' => 1,
                'pdf_path' => 'Lorem ipsum dolor sit amet',
                'created_at' => 1745618669,
            ],
        ];
        parent::init();
    }
}
