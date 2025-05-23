<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Config\UserRoleType;
use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends Voter<string, Client>
 */
class ClientVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const DOWNLOAD = 'DOWNLOAD';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Specifies the attributes and subjects this voter supports.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!\in_array($attribute, [self::VIEW, self::DOWNLOAD], true)) {
            return false;
        }

        if (!$subject instanceof Client) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Admin Tech role bypasses further checks
        if ($this->accessDecisionManager->decide($token, [UserRoleType::ROLE_ADMIN_TECH->value])) {
            return true;
        }

        $message = $this->translator->trans('error.forbidden_download', [], 'messages');

        return match ($attribute) {
            self::VIEW     => $this->canView($subject, $user, $token),
            self::DOWNLOAD => $this->canUserDownload($subject, $user, $token),
            default        => throw new AccessDeniedException($message),
        };
    }

    private function canView(Client $client, User $user, TokenInterface $token): bool
    {
        if ($this->accessDecisionManager->decide($token, [UserRoleType::ROLE_ADMIN->value])) {
            return $client->getOrganization() === $user->getOrganization();
        }

        if ($this->accessDecisionManager->decide($token, [UserRoleType::ROLE_USER->value])) {
            return $user->getClients()->exists(fn ($key, $c) => $c->getId() === $client->getId());
        }

        return false; // Deny access by default
    }

    private function canUserDownload(Client $subject, User $user, TokenInterface $token): bool
    {
        if ($this->accessDecisionManager->decide($token, [UserRoleType::ROLE_ADMIN->value])) {
            // Allow download if the user's organization matches the client's organization
            return $subject->getOrganization() === $user->getOrganization();
        }

        if ($this->accessDecisionManager->decide($token, [UserRoleType::ROLE_USER->value])) {
            // Check if the user's client collection contains the specified client
            return $user->getClients()->exists(fn ($key, $c) => $c->getId() === $subject->getId());
        }

        return false; // Deny access by default
    }
}