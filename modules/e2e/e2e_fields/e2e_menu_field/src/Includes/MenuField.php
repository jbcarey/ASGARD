<?php

namespace Drupal\e2e_menu_field\Includes;

use Drupal\Core\Menu\MenuTreeParameters;

class MenuField {
  
  private $currentmenuname;
  private $item;
  private $treeoutput;
  
  public function __construct($item){
    $this->item = $item;
  }
  
  //get name of currently active menu
  private function getCurrentMenuName(){
    
    if(!$this->currentmenuname)
      $this->setCurrentMenuName();
    
    return $this->currentmenuname;
    
  }
  
  //get menu tree output
  public function getTreeOutput(){
    
    if(!$this->treeoutput)
      $this->setTreeOutput();
    
    return $this->treeoutput;
  }
  
   //set name of currently active menu
  private function setCurrentMenuName(){
    
    $currentmenuname = NULL;
    
    foreach(array_keys(menu_ui_get_menus()) as $menuname){
      
      $activelink = \Drupal::service('menu.active_trail')->getActiveLink($menuname);
      
      if(!empty($activelink))
        $currentmenuname = $menuname;
      
    }
    
    $this->currentmenuname = $currentmenuname;
    
  }
  
  //set tree menu output
  private function setTreeOutput(){
    
    $item = $this->item;
    
    $treeoutput = NULL;
    $menuname = NULL;
    $parameters = NULL;
    
    $menutree = \Drupal::menuTree();
    
    if($item->menu_name == 'active-menu'){
    
      $menuname = $this->getCurrentMenuName();
    
      if($menuname)
        $parameters = $menutree->getCurrentRouteMenuTreeParameters($menuname);
    
    }else{
      $parameters = new MenuTreeParameters();
      $parameters->root = $item->menu_plid;
      $menuname = $item->menu_name;
    }
    
    
    if($parameters && $menuname){
      
      $parameters->setMaxDepth($item->menu_level);

      $tree = $menutree->load($menuname, $parameters);
      $treeoutput = $menutree->build($tree);
      
    }
    
    $this->treeoutput = $treeoutput;
  }
  
}

