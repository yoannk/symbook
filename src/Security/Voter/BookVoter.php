<?php

namespace App\Security\Voter;

use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BookVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['BOOK_NEW', 'BOOK_EDIT', 'BOOK_DELETE'])
            && $subject instanceof Book;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Book $book */
        $book = $subject;

        switch ($attribute) {
            case 'BOOK_EDIT':
                return $this->isAuthor($user, $book);
                break;
            case 'BOOK_DELETE':
                return $this->isAuthor($user, $book);
                break;
        }

        return false;
    }

    private function isAuthor(User $user, Book $book)
    {
        return $user->getId() === $book->getAuthor()->getId();
    }
}
