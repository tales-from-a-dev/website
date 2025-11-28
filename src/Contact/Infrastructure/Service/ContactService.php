<?php

declare(strict_types=1);

namespace App\Contact\Infrastructure\Service;

use App\Contact\Domain\Service\ContactServiceInterface;
use App\Contact\Infrastructure\Notification\ContactNotification;
use App\Contact\Ui\Form\Data\ContactDto;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ContactService implements ContactServiceInterface
{
    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
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

        $this->notifier->send($notification, $recipient);
    }
}
