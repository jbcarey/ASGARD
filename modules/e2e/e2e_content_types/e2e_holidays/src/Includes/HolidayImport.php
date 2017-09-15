<?php

namespace Drupal\e2e_holidays\Includes;

use \Drupal\node\Entity\Node;

class HolidayImport {
  
  private $file;
  private $imported;
  private $dates;
  private $title;
  
  //constructor
  public function __construct($file) {
    $this->file = $file;
    $this->imported = FALSE;
    $this->rows = [];
  }
  
  //edit/insert node with csv dates
  public function importNode(){
    $dates = $this->getDates();
    
    if($dates && $this->title)
      $this->updateNode();
    
  }
  
  //check if the file was imported
  public function isImported(){
    return $this->imported;
  }
  
  //get node based on import title, create new node when no node found
  private function getNode(){
  
    $query = \Drupal::entityQuery('node');
    $nids = $query
      ->condition('type', 'holidays', '=')
      ->condition('title', $this->title)
      ->range(0, 1)
      ->execute();

    if(!empty($nids)){
      return \Drupal::entityTypeManager()->getStorage('node')->load(reset($nids));
    }
      
    $node = Node::create(['type' => 'holidays', 'status' => 1, 'title' => $this->title]);
    $node->save();
    
    return $node;
    
  }
  
  //get dates
  private function getDates(){
    if(!$this->dates)
      $this->setDates();
    
    return $this->dates;
  }
  

  //store dates based on file data
  private function setDates(){
    
    $handle = fopen($this->file, 'r');
    
    if($handle){
      while($row = fgetcsv($handle, 0, ';')){
        if(count($row)){
          if(is_null($this->title)){
            $this->title = $row[0];
          }elseif($row[0]){
            $this->setDate($row);
          }
        }
      }
    }
  }
  
  //store a start and end date in dates array
  private function setDate($row){
  
    $date = ['value' => $row[0]];

    if(count($row) > 1 && $row[1])
      $date['end_value'] = $row[1];
    else
      $date['end_value'] = $row[0];

    $this->dates[] = $date;
  
  }
  
  //update date values of holiday node
  private function updateNode(){
    $node = $this->getNode();
    $node->field_days->setValue($this->dates);
    $node->save();
    
    $this->imported = TRUE;
  }
}

