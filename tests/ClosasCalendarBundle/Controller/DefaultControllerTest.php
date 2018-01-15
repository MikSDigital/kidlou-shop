<?php

namespace Closas\CalendarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }

    public function test2Index() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/doc');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }

}
