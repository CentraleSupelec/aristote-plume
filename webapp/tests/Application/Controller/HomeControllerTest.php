<?php

namespace App\Tests\Application\Controller;

use App\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Request;

class HomeControllerTest extends BaseWebTestCase
{
    public function testPublicHomePage(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/');
        $this->assertResponseIsSuccessful();
        $this->assertEquals('Plume', $crawler->filterXPath('//*[@id="main"]/div[2]/div[2]/div/h1')->text());
    }
}
