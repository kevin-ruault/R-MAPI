<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CharacterApiTest extends WebTestCase
{
    private function getJsonResponse(array $expectedStatusCodes = [200]): array
    {
        $status = self::getClient()->getResponse()->getStatusCode();
        self::assertContains($status, $expectedStatusCodes);

        $content = self::getClient()->getResponse()->getContent();
        self::assertNotFalse($content);

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function testListCharactersHasExpectedShape(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/character');

        $data = $this->getJsonResponse();

        self::assertArrayHasKey('info', $data);
        self::assertArrayHasKey('results', $data);

        self::assertArrayHasKey('count', $data['info']);
        self::assertArrayHasKey('pages', $data['info']);
        self::assertArrayHasKey('next', $data['info']);
        self::assertArrayHasKey('prev', $data['info']);

        self::assertIsArray($data['results']);
        self::assertLessThanOrEqual(20, count($data['results']));
    }

    public function testFilterByNameFindsRick(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/character?name=rick');

        $data = $this->getJsonResponse();

        self::assertNotEmpty($data['results']);

        $names = array_map(static fn (array $c) => $c['name'] ?? null, $data['results']);
        self::assertContains('Rick Sanchez', $names);
    }

    public function testFilterByGenderIsExactAndCaseInsensitive(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/character?gender=male');

        $data = $this->getJsonResponse();

        foreach ($data['results'] as $character) {
            self::assertSame('Male', $character['gender']);
        }
    }

    public function testCombinedFiltersWorkTogether(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/character?name=smith&gender=male');

        $data = $this->getJsonResponse();

        foreach ($data['results'] as $character) {
            self::assertStringContainsStringIgnoringCase('smith', $character['name']);
            self::assertSame('Male', $character['gender']);
        }
    }

    public function testGetCharacterByIdNotFoundReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/character/999999');

        self::assertResponseStatusCodeSame(404);
    }

    public function testPageOutOfRangeReturns404OrEmptyDependingOnYourContract(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/character?page=9999');

        self::assertResponseStatusCodeSame(404);
    }
}
