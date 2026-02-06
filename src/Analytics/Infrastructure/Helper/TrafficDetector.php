<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\Helper;

use App\Analytics\Domain\ValueObject\TrafficResult;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

use function Symfony\Component\String\s;

final class TrafficDetector
{
    private const array REQUIRED_HEADERS = [
        'User-Agent',
        'Accept',
        'Accept-Language',
        'Accept-Encoding',
    ];

    private const array URL_BLACKLIST = [
        // assets
        '.css',
        '.js',
        '.png',
        '.jpg',
        '.jpeg',

        // profiler
        '/_wdt',
        '/_profiler',

        // routes
        '/contact',
        '/settings',
        '/login',
        '/robots.txt',
    ];

    private const array DOMAINS_WHITELIST = [
        '.googlebot.com',
        '.google.com',
        '.search.msn.com', // Bing
    ];

    private const array USER_AGENT_WHITELIST = [
        'googlebot',
        'bingbot',
    ];

    private const array USER_AGENT_BLACKLIST = [
        'bot',
        'crawler',
        'spider',
        'scrapy',
        'curl',
        'wget',
        'httpclient',
        'python',
        'java',
        'go-http-client',
        'libwww',
    ];

    private const array IP_BLACKLIST = [
        '127.0.0.1',
    ];

    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    public function detect(Request $request): TrafficResult
    {
        if ($this->isClaimingCrawler($request) && $this->isVerifiedCrawler($request)) {
            return $this->getResult(10, $request);
        }

        $score = 0;

        $score += $this->urlScore($request);
        $score += $this->headerScore($request);
        $score += $this->userAgentScore($request);
        $score += $this->ipScore($request);
        $score += $this->browserScore($request);
        $score += $this->rateScore($request);

        return $this->getResult($score, $request);
    }

    private function isClaimingCrawler(Request $request): bool
    {
        $userAgent = s($request->headers->get('User-Agent', ''))->lower();

        return $userAgent->containsAny(self::USER_AGENT_WHITELIST);
    }

    private function isVerifiedCrawler(Request $request): bool
    {
        $ip = s($request->getClientIp() ?? '');

        if (false === $host = @gethostbyaddr($ip->toString())) {
            return false;
        }

        $host = s($host);

        if ($host->endsWith(self::DOMAINS_WHITELIST)) {
            $resolved = @gethostbyname($host->toString());

            return $resolved === $ip->toString();
        }

        return false;
    }

    private function urlScore(Request $request): int
    {
        $url = s($request->getPathInfo());

        if ($url->isEmpty() || $url->endsWith(self::URL_BLACKLIST) || $url->startsWith(self::URL_BLACKLIST)) {
            return 5;
        }

        return 0;
    }

    private function headerScore(Request $request): int
    {
        $missing = 0;

        foreach (self::REQUIRED_HEADERS as $header) {
            if (!$request->headers->has($header)) {
                ++$missing;
            }
        }

        return $missing >= 2 ? 2 : 0;
    }

    private function userAgentScore(Request $request): int
    {
        $userAgent = s($request->headers->get('User-Agent', ''))->lower();

        if ($userAgent->containsAny(self::USER_AGENT_BLACKLIST)) {
            return 3;
        }

        return 0;
    }

    private function ipScore(Request $request): int
    {
        $ip = s($request->getClientIp() ?? '');

        if ($ip->isEmpty() || $ip->equalsTo(self::IP_BLACKLIST)) {
            return 3;
        }

        return 0;
    }

    private function browserScore(Request $request): int
    {
        $score = 0;

        if ($request->isXmlHttpRequest()) {
            ++$score;
        }

        // No cookies at all
        if (0 === $request->cookies->count()) {
            ++$score;
        }

        // JS capability check
        if (!$request->cookies->has('js_enabled')) {
            $score += 2;
        }

        // HTML page without referer
        $accept = s($request->headers->get('Accept', ''));
        if (!$request->headers->has('Referer') && $accept->containsAny('text/html')) {
            ++$score;
        }

        return $score;
    }

    private function rateScore(Request $request): int
    {
        $ip = s($request->getClientIp() ?? '');
        $route = s($request->attributes->get('_route', ''));

        if ($ip->isEmpty() || $route->isEmpty()) {
            return 3;
        }

        $key = \sprintf('traffic_rate_%s_%s', md5($ip->toString()), md5($route->toString()));

        $data = $this->cache->get($key, static function (ItemInterface $item) {
            $item->expiresAfter(60);

            return [
                'count' => 0,
                'last' => microtime(true),
            ];
        });

        ++$data['count'];

        $this->cache->delete($key);
        $this->cache->get($key, static function (ItemInterface $item) use ($data) {
            $item->expiresAfter(60);

            return $data;
        });

        if ($data['count'] > 30) {
            return 3;
        }

        if ($data['count'] > 10) {
            return 1;
        }

        return 0;
    }

    private function getResult(int $score, Request $request): TrafficResult
    {
        return new TrafficResult(
            score: min($score, 10),
            path: $request->getPathInfo(),
            method: $request->getMethod(),
            ip: IpUtils::anonymize($request->getClientIp() ?? ''),
            userAgent: $request->headers->get('User-Agent', ''),
            referer: $request->headers->get('Referer', ''),
        );
    }
}
