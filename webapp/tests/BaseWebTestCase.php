<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseWebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient(['debug' => false, 'environment' => 'test']);
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        $this->client->getCookieJar()->clear();
        parent::tearDown();
    }
}
