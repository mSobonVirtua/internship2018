<?php
/**
 * VI-77 ProductCategoryVoter
 *
 * @category   Voter
 * @package    Virtua_ProductCategory
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Security\Voter;

use App\Entity\ProductCategory;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductCategoryVoter extends Voter
{
    const ADD = 'add';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, [self::ADD, self::EDIT, self::DELETE])){
            return false;
        }

        if(!$subject instanceof ProductCategory){
            return false;
        }

        return true;
    }

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


    private function canEdit(User $user): bool
    {
        return $this->haveRoleWithPermission($user);
    }

    private function canAdd(User $user): bool
    {
        return $this->haveRoleWithPermission($user);
    }

    private function canDelete(User $user): bool
    {
        return $this->haveRoleWithPermission($user);
    }

    private function haveRoleWithPermission(User $user) : bool
    {
        foreach ($user->getRoles() as $role)
        {
            if($role === "ROLE_USER"){
                return true;
            }
        }
        return false;
    }
}
