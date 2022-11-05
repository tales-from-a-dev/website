<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use App\Domain\Contact\Model\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContactManager
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly string $contactEmail,
    ) {
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send(Contact $contact): void
    {
        if ((null === $email = $contact->email) || (null === $name = $contact->name)) {
            return;
        }

        $from = new Address($email, $name);
        $to = new Address($this->contactEmail, 'Contact');

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($this->translator->trans(id: 'contact.subject', domain: 'email'))
            ->htmlTemplate('email/contact.html.twig')
            ->context([
                'contact' => $contact,
            ])
        ;

        $this->mailer->send($email);
    }
}
