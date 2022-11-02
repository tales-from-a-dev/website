<?php

declare(strict_types=1);

namespace App\Domain\Blog\Security\Voter;

use App\Core\Enum\Action;
use App\Domain\Blog\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [Action::View->value, Action::Edit->value], true) && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match (Action::from($attribute)) {
            Action::View => $this->canView($subject),
            Action::Edit => $this->canEdit($subject, $token->getUser()),
            default => false,
        };
    }

    private function canView(Post $subject): bool
    {
        return $subject->isPublished();
    }

    private function canEdit(mixed $subject, ?UserInterface $user): bool
    {
        return $user instanceof UserInterface;
    }
}
