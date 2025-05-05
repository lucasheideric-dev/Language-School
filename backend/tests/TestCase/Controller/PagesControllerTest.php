<?php

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class PagesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function testDisplay()
    {
        $this->get('/pages/display/home');
        $this->assertResponseOk();
        $this->assertResponseContains('home');

        $this->get('/pages/display/about');
        $this->assertResponseOk();
        $this->assertResponseContains('about');

        $this->get('/pages/display/../forbidden');
        $this->assertResponseCode(403);

        $this->get('/pages/display/nonexistent');
        $this->assertResponseCode(404);
    }
}
