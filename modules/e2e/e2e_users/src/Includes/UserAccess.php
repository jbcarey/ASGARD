<?php

namespace Drupal\e2e_users\Includes;

use Drupal\user\Entity\User;

abstract class UserAccess {
  
  protected $account;
  private $internalsections;
  private $user;
  private $usersections;
  
    //constructor
  public function __construct($account){
    $this->account = $account;
  }
  
  //get all sections set as internal
  protected function getInternalSections(){
    
    if(!$this->internalsections)
      $this->setInternalSections();
    
    return $this->internalsections;
  }
  
//get user
  protected function getUser(){
    
    if(!$this->user)
      $this->setUser();
    
    return $this->user;
  }
  
  //get user sections
  protected function getUserSections(){
    
    if(!$this->usersections)
      $this->setUserSections();
    
    return $this->usersections;
  }
  
  //store all sections set as internal
  private function setInternalSections(){
    
    $this->internalsections = [];
    
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', 'section');
    $query->condition('field_internal_section.value', 1);
    $results = $query->execute();
    
    if($results)
      $this->internalsections = array_keys($results);
  }
  
  //set user based on account data
  private function setUser(){
    $this->user = User::load($this->account->id());
  }
  
  //store user sections based on user data
  private function setUserSections(){
    $user = $this->getUser();
    
    $this->usersections = array_column($user->field_sections->getValue(),'target_id');
  }
}