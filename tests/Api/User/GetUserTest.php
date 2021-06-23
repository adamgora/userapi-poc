<?php
declare(strict_types=1);

namespace App\Tests\Api\User;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class GetUserTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testItFetchesUserCollection(): void
    {
        $client = self::createClient();

        $this->createUser('john@example.com');
        $this->createUser('jane@example.com');

        $response = $client->request('GET', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        self::assertResponseStatusCodeSame(200);

        $collection = json_decode($response->getContent(), true)['hydra:member'];
        self::assertCount(2, $collection);

    }

    public function testItFetchesUserById(): void
    {
        $client = self::createClient();

        $this->createUser('john@example.com');

        $response = $client->request('GET', '/api/users/1', [
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        self::assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);

        self::assertSame('john@example.com', $responseData['email']);

    }

    private function createUser(string $email)
    {
        $user = new User();
        $user->setFirstName('Foo');
        $user->setLastName('Smith');
        $user->setEmail($email);
        $user->setPassword('secret');

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($user);
        $entityManager->flush();
    }
}