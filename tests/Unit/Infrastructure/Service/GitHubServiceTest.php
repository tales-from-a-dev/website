<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Service;

use App\Infrastructure\Service\GitHubService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\CacheInterface;

final class GitHubServiceTest extends TestCase
{
    public function testItCanFetchPinnedRepositories(): void
    {
        $mockResponseJson = file_get_contents(__DIR__.'/../../../Fixtures/Api/github/pinned_response.json');
        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $mockHttpClient = new MockHttpClient($mockResponse, 'https://api.github.com')
            ->withOptions([
                'headers' => [
                    'Accept: application/vnd.github.v4+json',
                    'Authorization: Bearer github_authorization_token',
                    'Content-Type: application/json',
                ],
            ])
        ;

        $expectedResponseData = json_decode($mockResponseJson, true, 512, \JSON_THROW_ON_ERROR);

        // Mocked cache
        $mockCache = $this->createMock(CacheInterface::class);
        $mockCache
            ->expects($this->once())
            ->method('get')
            ->willReturn($expectedResponseData['data']['user']['pinnedItems']['nodes']);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger
            ->expects($this->never())
            ->method('error')
        ;

        $githubService = new GitHubService($mockCache, $mockHttpClient, $mockLogger, 'ker0x');

        $pinnedRepositories = $githubService->getPinnedRepositories();

        self::assertCount(6, $pinnedRepositories);
        self::assertArrayHasKey('id', $pinnedRepositories[0]);
        self::assertArrayHasKey('name', $pinnedRepositories[0]);
        self::assertArrayHasKey('description', $pinnedRepositories[0]);
        self::assertArrayHasKey('forkCount', $pinnedRepositories[0]);
        self::assertArrayHasKey('stargazerCount', $pinnedRepositories[0]);
        self::assertArrayHasKey('url', $pinnedRepositories[0]);
        self::assertArrayHasKey('languages', $pinnedRepositories[0]);
    }
}
