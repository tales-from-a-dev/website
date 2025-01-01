<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Dto\ContactDto;
use App\Domain\Service\ContactServiceInterface;
use App\Infrastructure\Notification\ContactNotification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ContactService implements ContactServiceInterface
{
    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private string $contactEmail,
        private string $contactPhone,
    ) {
    }

    public function notify(ContactDto $contact): void
    {
        $subject = $this->translator->trans(id: 'contact.subject', parameters: ['name' => $contact->fullName], domain: 'notification');

        $notification = new ContactNotification($contact, $subject)
            ->importance(Notification::IMPORTANCE_URGENT)
        ;

        $recipient = new Recipient($this->contactEmail, $this->contactPhone);

        try {
            $this->notifier->send($notification, $recipient);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage(), $exception->getTrace());

            throw $exception;
        }
    }
}
