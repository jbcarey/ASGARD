<?php

namespace Drupal\e2e_users\Plugin\ImcePlugin;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\imce\ImcePluginBase;

use Drupal\e2e_users\Includes\UserImceAccess;

/**
 * Defines Imce Section Folders plugin.
 *
 * @ImcePlugin(
 *   id = "sectionfolders",
 *   label = "SectionFolders",
 *   weight = 50
 * )
 */
class SectionFolders extends ImcePluginBase {
  
  /**
   * {@inheritdoc}
   */
  public function processUserConf(array &$conf, AccountProxyInterface $user) {
    
    if($user->id() > 1){
      $access = new UserImceAccess($user);
      $conf['folders'] = $access->getFolderPermissions();
    }
    
    return $conf;
  }
}

