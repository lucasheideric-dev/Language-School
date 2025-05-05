<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'],
        'is_active' => ['type' => 'boolean', 'null' => false, 'default' => true],
        'created' => ['type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
        'name' => ['type' => 'string', 'null' => false],
        'last_name' => ['type' => 'string', 'null' => false],
        'email' => ['type' => 'string', 'null' => false],
        'password' => ['type' => 'string', 'null' => false],
        'role_id' => ['type' => 'integer', 'null' => true],
    ];

    public $records = [
        [
            'id' => 1,
            'is_active' => true,
            'created' => '2025-04-16 00:00:00',
            'name' => 'Teste',
            'last_name' => 'Tester',
            'email' => 'teste@gmail.com',
            'password' => '$2y$10$phh7xbfTwsGsnsetR.4pjuamrBQNFc1Vy3X38qqTNGqVC8SoFKNau',
            'role_id' => 1,
        ],
    ];
}
