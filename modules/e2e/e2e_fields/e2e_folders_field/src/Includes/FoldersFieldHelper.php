<?php

namespace Drupal\e2e_folders_field\Includes;

class FoldersFieldHelper {
  
  //get data for location fields
  public static function getFolders(){
      
    $folders = ['public' => 'All folders (checkboxes below are ignored)'];
      
    if ($uri = \Drupal::service('stream_wrapper_manager')->getViaUri('public://')) {
      
      $root = $uri->realpath() . '/';
      
      $subdirs = glob($root . 'public/*', GLOB_ONLYDIR);
        
      foreach($subdirs as $subdir){
        $folders[str_replace( $root, '', $subdir)] = str_replace($root . 'public/', '', $subdir);
      }
    }
    
    return $folders;
  }
}

