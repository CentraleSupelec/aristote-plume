<?php

namespace App\Tests\Application\Controller;

use App\Entity\PlumeUser;
use App\Tests\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ArticleControllerTest extends BaseWebTestCase
{
    public function testCreateArticlePage(): void
    {
        $plumeUser = (new PlumeUser())->setEnabled(true)->setEmail('john.doe@gmail.com');
        $this->entityManager->persist($plumeUser);
        $this->entityManager->flush();

        $this->client->loginUser($plumeUser);
        $crawler = $this->client->request(Request::METHOD_GET, '/app/create');
        $this->assertResponseIsSuccessful();
        $this->assertEquals('Rédigez votre article avec Plume', $crawler->filterXPath('//*[@id="main"]/div[2]/div/div/h1')->text());
    }
}
