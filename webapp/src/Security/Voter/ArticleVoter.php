<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\PlumeUser;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    final public const USER_CAN_VIEW_ARTICLE = 'user-view-article';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::USER_CAN_VIEW_ARTICLE === $attribute && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        /** @var Article $subject */
        $article = $subject;

        return match ($attribute) {
            self::USER_CAN_VIEW_ARTICLE => $user instanceof PlumeUser && $user === $article->getAuthor(),
            default => throw new LogicException('This code should not be reached!'),
        };
    }
}
