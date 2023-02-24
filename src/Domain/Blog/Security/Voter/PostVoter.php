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
            Action::View => $this->canView($subject, $token->getUser()),
            Action::Edit => $this->canEdit($subject, $token->getUser()),
        };
    }

    private function canView(Post $subject, ?UserInterface $user): bool
    {
        if ($subject->isPublished()) {
            return true;
        }

        if ($user instanceof UserInterface) {
            return true;
        }

        return false;
    }

    private function canEdit(Post $subject, ?UserInterface $user): bool
    {
        return $user instanceof UserInterface;
    }
}
