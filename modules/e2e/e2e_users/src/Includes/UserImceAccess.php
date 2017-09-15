<?php

namespace Drupal\e2e_users\Includes;

use Drupal\taxonomy\Entity\Term;

class UserImceAccess extends UserAccess {
  
  private $folders;
  
  //constructor
  public function __construct($account){
    parent::__construct($account);
  }
  
  //get user IMCE folder access permissions
  public function getFolderPermissions(){
    $permissions = [];
    $folders = $this->getFolders();
    
    if($folders){
      foreach($folders as $folder){
        $permissions[$folder] = ['permissions' => ['all' => 1]];
      }
    }
    
    if(!$permissions)
      $permissions['.'] = ['permissions' => []];
    
    return $permissions;
  }
  
  //get permitted folders
  private function getFolders(){
    
    if(!is_array($this->folders))
      $this->setFolders();
    
    return $this->folders;
    
  }

  //set permitted folders
  private function setFolders(){
    
    $sections = $this->getUserSections();
    $folders = [];

    //get section folders
    if(!empty($sections)){

      $terms = Term::loadMultiple($sections);

      foreach ($terms as $term) {
        if (!empty($term->field_user_folders->getValue())) 
          $folders = array_merge(array_column($term->field_user_folders->getValue(),'folders'),$folders);
      }
    }
    
    $this->folders = $folders;
    
  }
}

