<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class StudentsFixture extends TestFixture
{
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 1,
                'cpf' => 'Lorem ipsum ',
                'first_name' => 'Lorem ipsum dolor sit amet',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'birth_date' => '2025-04-25',
                'zip_code' => 'Lorem i',
                'street' => 'Lorem ipsum dolor sit amet',
                'house_number' => 'Lorem ip',
                'neighborhood' => 'Lorem ipsum dolor sit amet',
                'state' => 'Lo',
                'city' => 'Lorem ipsum dolor sit amet',
                'phone' => 'Lorem ipsum dolor ',
                'whatsapp' => 'Lorem ipsum dolor ',
                'email' => 'Lorem ipsum dolor sit amet',
                'created_at' => 1745618647,
                'updated_at' => 1745618647,
            ],
        ];
        parent::init();
    }
}
