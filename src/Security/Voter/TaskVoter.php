<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const ACCESS = 'ACCESS';
    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }
    protected function supports(string $attribute, mixed $subject): bool {
        if ($attribute != self::ACCESS) {
            return false;
        }
        if (!$subject instanceof Task) {
            return false;
        }
        return true;
    }
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        if ($this->accessDecisionManager->decide($token, ['ROLE_PROJECT_OWNER'])) {
            return true;
        }

        return match($attribute) {
            self::ACCESS => $this->canAccess($subject, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
    private function canAccess(Task $task, User $user): bool {
        $employeeList = $task->getProject()->getTeamList();
        foreach ($employeeList as $employee) {
            if ($employee->getUser()->getId() === $user->getId()) {
                return true;
            }
        }
        return false;
    }
}