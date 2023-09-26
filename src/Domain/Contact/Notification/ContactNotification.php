<?php

declare(strict_types=1);

namespace App\Domain\Contact\Notification;

use App\Domain\Contact\Model\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

final class ContactNotification extends Notification implements EmailNotificationInterface, SmsNotificationInterface
{
    public function __construct(
        private readonly Contact $contact,
        private readonly string $subject,
    ) {
        parent::__construct($this->subject);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        return new EmailMessage(
            (new TemplatedEmail())
                ->from(new Address(
                    address: 'noreply@talesfroma.dev',
                    name: 'Tales from a Dev'
                ))
                ->to(new Address(
                    address: $recipient->getEmail(),
                    name: 'Contact'
                ))
                ->subject($this->getSubject())
                ->htmlTemplate('email/contact.html.twig')
                ->context([
                    'contact' => $this->contact,
                ])
        );
    }

    public function asSmsMessage(SmsRecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        return new SmsMessage(
            $recipient->getPhone(),
            $this->getSubject(),
        );
    }
}
