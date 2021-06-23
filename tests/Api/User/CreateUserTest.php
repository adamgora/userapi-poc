<?php
declare(strict_types=1);

namespace App\Tests\Api\User;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CreateUserTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testItCreatesUser(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $this->getBaseProps()
        ]);
        self::assertResponseStatusCodeSame(201);
    }

    /**
     * @dataProvider requiredPropsProvider
     */
    public function testItDoesNotCreateUserWithoutRequiredProperties(string $property): void
    {
        $props = $this->getBaseProps();

        unset($props[$property]);

        $client = self::createClient();
        $client->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $props
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testItDoesNotCreateUserWithWrongEmailAddress(): void
    {
        $props = $this->getBaseProps();

        $props['email'] = 'foo';

        $client = self::createClient();
        $client->request('POST', '/api/users', [
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
            ['password'],
            ['email']
        ];
    }

    private function getBaseProps(): array
    {
        return [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password'
        ];
    }
}