<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Scheduler;

use App\Analytics\Application\Message\ProcessDailyTrafficMessage;
use Symfony\Component\Console\Messenger\RunCommandMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
final readonly class TaskProvider implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule(): Schedule
    {
        return new Schedule()
            ->stateful($this->cache) // ensure missed tasks are executed
            ->processOnlyLastMissedRun(true) // ensure only last missed task is run
            ->with(
                RecurringMessage::every(
                    frequency: '1 day',
                    message: new RunCommandMessage('db-tools:backup --quiet'),
                    from: '02:07',
                ),
                RecurringMessage::every(
                    frequency: '1 day',
                    message: new ProcessDailyTrafficMessage(),
                    from: '01:13',
                )
            )
        ;
    }
}
