<?php

namespace Drupal\e2e_users\Includes;

use Drupal\taxonomy\Entity\Term;

class UserMenuAccess extends UserAccess{
  
  private $allowedmenus;
  private $menu;
  private $parentoptions;
  
  //constructor
  public function __construct($account, $menu){
    
    parent::__construct($account);
    
    $this->menu = $menu;
  }
  
  //get menus allowed by user
  private function getAllowedMenus(){
    
    if(!$this->allowedmenus)
      $this->setAllowedMenus ();
    
    return $this->allowedmenus;
  }
  
  //get menu settings with allowed menu options
  public function getMenu(){
    
    if($this->getAllowedMenus())
      $this->menu['link']['menu_parent']['#options'] = $this->getParentOptions();
    else
      $this->menu['#access'] = FALSE;
    
    return $this->menu;
    
  }
  
  //get menu widget parent options
  private function getParentOptions(){
    
    if(!$this->parentoptions)
      $this->setParentOptions();
    
    return $this->parentoptions;
    
  }
  
  //set allowed menus based on user sections
  private function setAllowedMenus(){
    $sections = $this->getUserSections();
    $allowedmenus = [];
    
    if($sections){
      $terms = Term::loadMultiple($sections);
      
      foreach($terms as $term){
        $allowedmenus = array_merge($allowedmenus, array_column($term->field_menu_access->getValue(), 'value'));
      }
      
    }
    
    $this->allowedmenus = $allowedmenus;
  }
  
  //set parent menu options based on allowed menus from user sections
  private function setParentOptions(){
    
    $parentoptions = $this->menu['link']['menu_parent']['#options'];
    $allowedmenus = $this->getAllowedMenus();
    $menuLinkManager = \Drupal::service('plugin.manager.menu.link');
    
    foreach($parentoptions as $parentkey => $parentoption){
      
      $parentparts = explode(':', $parentkey, 3);
      
      $menuitems = [$parentparts[0]];
      
      if(count($parentparts) == 3){
        
        $parentitems = array_values($menuLinkManager->getParentIds($parentparts[1] . ':' . $parentparts[2]));
        
        $menuitems = array_merge([$parentparts[0]], $parentitems);
      }elseif(count($parentparts) == 2){
        $menuitems[] = $parentparts[0] . ':' . $parentparts[1];
      }
      
      if(!array_intersect($allowedmenus, $menuitems))
        unset($parentoptions[$parentkey]);
    }
    
    $this->parentoptions = $parentoptions;
    
  }
  
}