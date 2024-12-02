<?php

namespace App\Manager;

use Sonata\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Util\AdminAclUserManagerInterface;

final class AclUserManager implements AdminAclUserManagerInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function findUsers(): iterable
    {
        return $this->userManager->findAll();
    }
}