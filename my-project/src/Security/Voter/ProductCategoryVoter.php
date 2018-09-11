<?php
/**
 * VI-77 ProductCategoryVoter
 *
 * @category  Voter
 * @package   Virtua_ProductCategory
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Security\Voter;

use App\Entity\ProductCategory;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ProductCategoryVoter
 */
class ProductCategoryVoter extends Voter
{
    /**
     * @param string ADD
     */
    const ADD = 'add';
    /**
     * @param string EDIT
     */
    const EDIT = 'edit';
    /**
     * @param string DELETE
     */
    const DELETE = 'delete';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::ADD, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof ProductCategory) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user);
                break;
            case self::ADD:
                return $this->canAdd($user);
                break;
            case self::DELETE:
                return $this->canDelete($user);
                break;
        }

        return false;
    }


    /**
     * @param User $user
     * @return bool
     */
    private function canEdit(User $user): bool
    {
        return $this->haveRoleWithPermission($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canAdd(User $user): bool
    {
        return $this->haveRoleWithPermission($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canDelete(User $user): bool
    {
        return $this->haveRoleWithPermission($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function haveRoleWithPermission(User $user) : bool
    {
        foreach ($user->getRoles() as $role) {
            if ($role === "ROLE_USER") {
                return true;
            }
        }
        return false;
    }
}
