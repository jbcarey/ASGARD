<?php

namespace Drupal\e2e_subsites\Includes;

class SubsiteHelper {
  
  //get subsite entity node type paths based on section id
  public static function getNodeTypePaths($tid){
    
    $subsites = self::getSubsites([['field' => 'section', 'value' => $tid, 'operator' => '=']]);
    
    $paths = [];
    
    if($subsites){
    
      foreach($subsites as $subsite){

        if($subsite->nodetypepaths){

          $nodetypepaths = explode("\n", $subsite->nodetypepaths);

          foreach($nodetypepaths as $nodetypepath){

            $aliasfields = explode("|", $nodetypepath);

            if(count($aliasfields) > 2){
              $language = $aliasfields[1];

              if(!isset($paths[$language]))
                $paths[$language] = [];

              $paths[$language][$aliasfields[0]] = $aliasfields[2];
            }
          }
        }
      }
    }
    
    return [$tid => $paths];
  }
  
  //get subsite entities
  public static function getSubsites($conditions = array()){
    
    $subsites = FALSE;
    
    $query = \Drupal::entityQuery('subsite');
    
    foreach($conditions as $condition){
      $query->condition($condition['field'], $condition['value'], $condition['operator']);
    }
    
    $query->sort('weight', 'ASC');
    
    $ids = $query->execute();
      
    if($ids)
      $subsites =  \Drupal::entityTypeManager()->getStorage('subsite')->loadMultiple($ids);
    
    return $subsites;
  }
}