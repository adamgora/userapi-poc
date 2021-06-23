<?php
declare(strict_types=1);

namespace App\Tests\Api\User;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UpdateUserTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testItUpdatesUser(): void
    {
        $client = self::createClient();
        $this->createUser();
        $client->request('PUT', '/api/users/1', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $this->getBaseUpdateProps()
        ]);
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @dataProvider requiredPropsProvider
     */
    public function testItDoesNotCreateUserWithoutRequiredProperties(string $property): void
    {
        $props = $this->getBaseUpdateProps();
        $props[$property] = '';

        $client = self::createClient();
        $this->createUser();

        $client->request('PUT', '/api/users/1', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $props
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testItDoesNotUpdateUserWithWrongEmailAddress(): void
    {
        $props = $this->getBaseUpdateProps();
        $props['email'] = 'foo';

        $client = self::createClient();
        $this->createUser();

        $client->request('PUT', '/api/users/1', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $props
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function requiredPropsProvider(): array
    {
        return [
            ['firstName'],
            ['lastName'],
            ['email']
        ];
    }

    private function getBaseUpdateProps(): array
    {
        return [
            'firstName' => 'Mark',
            'lastName' => 'Smith',
            'email' => 'marksmith@example.com',
            'password' => 'supersecret'
        ];
    }

    private function createUser()
    {
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('john@example.com');
        $user->setPassword('secret');

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($user);
        $entityManager->flush();
    }
}