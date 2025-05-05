<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LessonsFixture extends TestFixture
{

    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'student_id' => 1,
                'teacher_id' => 1,
                'lesson_date' => 1745618662,
                'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'created_at' => 1745618662,
                'updated_at' => 1745618662,
            ],
        ];
        parent::init();
    }
}
