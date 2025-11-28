<?php

declare(strict_types=1);

namespace App\Contact\Infrastructure\State\Processor;

use App\Contact\Domain\Service\ContactServiceInterface;
use App\Contact\Ui\Form\Data\ContactDto;
use App\Shared\Domain\State\ProcessorInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<ContactDto|null, bool>
 */
final readonly class SendContactProcessor implements ProcessorInterface
{
    public function __construct(
        private ContactServiceInterface $contactService,
        private LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, array $context = []): bool
    {
        Assert::isInstanceOf($data, ContactDto::class);
        Assert::notNull($data->fullName);
        Assert::notNull($data->email);
        Assert::notNull($data->content);

        $success = true;

        try {
            $this->contactService->notify($data);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            $success = false;
        } finally {
            return $success;
        }
    }
}
