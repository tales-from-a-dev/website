<?php

declare(strict_types=1);

namespace App\Analytics\Application\Message;

use App\Analytics\Domain\Entity\PageView;
use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use App\Shared\Domain\Logger\Parser\LogParserInterface;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Cache\CacheInterface;

use function Symfony\Component\String\s;

#[AsMessageHandler]
final readonly class ProcessDailyTrafficMessageHandler
{
    private const string FILE_PATTERN = '/^traffic-[\d]{4}-[\d]{2}-[\d]{2}.log$/';

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

    private const array IP_BLACKLIST = [];

    public function __construct(
        private LogParserInterface $logParser,
        private CacheInterface $analyticsCache,
        private PageViewRepositoryInterface $pageViewRepository,
        #[Autowire(value: '%kernel.logs_dir%')]
        private string $logsDir,
    ) {
    }

    /**
     * @return string[]
     */
    public function __invoke(ProcessDailyTrafficMessage $message): array
    {
        $cacheKey = 'last_traffic_parsed_files';
        $lastParsedFiles = $this->analyticsCache->get($cacheKey, static fn () => []);

        $finder = new Finder()
            ->in($this->logsDir)
            ->files()
            ->name(self::FILE_PATTERN)
        ;

        $parsedFiles = [];
        foreach ($finder as $file) {
            /** @var string[] $lastParsedFiles */
            if (\in_array($file->getFilename(), $lastParsedFiles, true)) {
                $parsedFiles[] = $file->getFilename();

                continue;
            }

            try {
                foreach ($this->logParser->parse($file->getRealPath()) as $logEntry) {
                    $level = s($logEntry->level)->lower();
                    if (!$level->equalsTo(LogLevel::INFO)) {
                        continue;
                    }

                    $method = s($logEntry->extra['http_method'] ?? '');
                    if ($method->isEmpty()) {
                        continue;
                    }

                    $url = s($logEntry->extra['url'] ?? '')->lower();
                    if ($url->isEmpty() || $url->endsWith(self::URL_BLACKLIST) || $url->startsWith(self::URL_BLACKLIST)) {
                        continue;
                    }

                    $userAgent = s($logEntry->extra['user_agent'] ?? '')->lower();
                    if ($userAgent->isEmpty() || $userAgent->containsAny(self::USER_AGENT_BLACKLIST)) {
                        continue;
                    }

                    $ip = s($logEntry->extra['ip'] ?? '');
                    if ($ip->isEmpty() || IpUtils::checkIp($ip->toString(), self::IP_BLACKLIST)) {
                        continue;
                    }

                    $server = s($logEntry->extra['server'] ?? '');
                    $referer = s($logEntry->extra['referrer'] ?? '');

                    $pageView = new PageView(
                        url: $url->toString(),
                        method: $method->toString(),
                        server: $server->toString(),
                        ip: IpUtils::anonymize($ip->toString()),
                        userAgent: $userAgent->toString(),
                        referer: $referer->isEmpty() ? null : $referer->toString(),
                        visitedAt: $logEntry->datetime,
                    );

                    $this->pageViewRepository->add($pageView);
                }

                $parsedFiles[] = $file->getFilename();
            } catch (\Throwable) {
            }
        }

        if ([] !== $parsedFiles) {
            $this->analyticsCache->delete($cacheKey);
            $this->analyticsCache->get($cacheKey, static fn () => $parsedFiles);
        }

        return $parsedFiles;
    }
}
