<?php

namespace Drupal\e2e_users\Includes;

use Drupal\Core\Access\AccessResult;
use Drupal\taxonomy\Entity\Term;

class UserNodeAccess extends UserAccess {
  
  private $node;
  private $op;
  private $sections;
  private $types;
  
  //constructor
  public function __construct($account, $node, $op){
    
    parent::__construct($account);

    $this->node = $node;
    $this->op = $op;
  }
  
  //get node access result
  public function getAccessResult(){
    
    if(!empty($this->node->field_section)){
      if($accessresult = $this->getAccessResultSection())
        return $accessresult;
    }
    
    if($this->account->id() != 0 && $this->op != 'view'){
      
      if($accessresult = $this->getAccessResultType())
        return $accessresult;
    }
    
    return AccessResult::neutral();
  }
  
  //get access result based on node section
  private function getAccessResultSection(){
          
    $sections = $this->getSections();
    $internalsections = $this->getInternalSections();
      
    if($this->account->id() == 0){
      //deny access to internal nodes
      if(!empty(array_intersect($internalsections, $sections))) 
          return AccessResult::forbidden();
    }else{
        
      $usersections = $this->getUserSections();
        
      //deny access to internal nodes unless section permission
      if(!empty(array_intersect($internalsections,$sections))) {
        
        if (empty(array_intersect($internalsections,$usersections))) 
          return AccessResult::forbidden();
      }
        
      //deny access to users without required section for editing/deleting nodes
      if(empty(array_intersect($usersections, $sections)) && $this->op != 'view')
        return AccessResult::forbidden();
    }
    
    return FALSE;
  }
  
  //get access result based on node type
  private function getAccessResultType(){
    
    $types = $this->getTypes();
    $node = $this->node;
    $type = $node->type->target_id;
    
    if($types && !in_array($type, $types)) 
      return AccessResult::forbidden();
    
    /*$action = ($this->op == 'update')?'edit':$this->op;
    $account = $this->account;
    
    if ($action == 'create' && $account->hasPermission('create ' . $type . ' content'))
      return AccessResult::allowed();
    
    if ($action == 'edit' || $action == 'delete') {
      if ($account->hasPermission($action . ' any ' . $type . ' content') || ($account->hasPermission($action . ' own ' . $type . ' content') && $account->id() == $node->uid->target_id))
        return AccessResult::allowed();
    }*/
    
    return FALSE;
  }

  //get the sections of the node
  private function getSections(){
    
    if(!$this->sections)
      $this->setSections();
    
    return $this->sections;
  }
  
  //get allowed content types
  private function getTypes(){
    
    if(!$this->types)
      $this->setTypes();
    
    return $this->types;
    
  }

  //store node sections
  private function setSections(){
    $this->sections = array_column($this->node->field_section->getValue(),'target_id');
  }
  
  //set allowed content types
  private function setTypes(){
    
    $user = $this->getUser();
    
    //use user content types when available
    if(!empty($user->field_content_types->getValue())){
      $this->types = array_column($user->field_content_types->getValue(), 'contenttypes');
    }else{
      $sections = $this->getUserSections();

      //get section content types
      if(!empty($sections)){

        $terms = Term::loadMultiple($sections);

        $types = [];

        foreach ($terms as $term) {
          if (!empty($term->field_user_content_types->getValue())) 
            $types = array_merge(array_column($term->field_user_content_types->getValue(),'contenttypes'),$types);
        }

        $this->types = $types;
      }
    }
  }
}

