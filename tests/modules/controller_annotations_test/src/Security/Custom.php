<?php

namespace Drupal\controller_annotations_test\Security;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

class Custom
{
    /**
     * @param AccountInterface $account
     * @return AccessResult
     */
    public function access(AccountInterface $account)
    {
        return AccessResult::allowedIf($account->id() === 1337);
    }
}
