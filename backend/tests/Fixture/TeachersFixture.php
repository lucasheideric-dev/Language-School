<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TeachersFixture extends TestFixture
{
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'cpf' => '123.456.789-10',
                'first_name' => 'João',
                'last_name' => 'Silva',
                'birth_date' => '1985-05-10',
                'specialty' => 'Matemática',
                'status' => true,
                'created_at' => 1745618654,
                'updated_at' => 1745618654,
            ],
        ];
        parent::init();
    }
}
