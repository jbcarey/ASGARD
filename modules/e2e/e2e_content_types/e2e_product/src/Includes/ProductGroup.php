<?php

namespace Drupal\e2e_product\Includes;


class ProductGroup {
  
  private $element;
  private $fields;
  
  //constructor
  public function __construct($element) {
    $this->element = $element;
  }
  
  //get ipdc, sutom or options field
  private function getField($type){
    
    if(!$this->fields)
      $this->setFields();
    
    if(isset($this->fields[$type]))
      return $this->fields[$type];
    
    return FALSE;
    
  }
  
  //get product field group markup
  public function getMarkup(){
    
    $markup = '';

    $options = $this->getField('options');
    
    if(in_array('show_ipdc', $options))
      $markup .= $this->getField('ipdc');
    
    if(in_array('show_own', $options))
      $markup .= $this->getField('custom');
    
    if($markup)
      $markup = '<div class="content-main padding inner">' . $markup . '</div>';
      
    return $markup;
    
  }
  
  //store fields based on element
  private function setFields(){
    
    $fields = [];
    
    foreach($this->element as $fieldname => $field){
      
      if(substr($fieldname, -7) == '_custom')
          $fields['custom'] = (string)render($field);
      elseif(substr($fieldname, -8) == '_options'){
        
        if(!empty($field['#items'])){
          $items = $field['#items'];
          
          $fields['options'] = array_column($items->getValue(), 'value');
        }else
          $fields['options'] = [];
        
      }else
        $fields['ipdc'] = (string)render($field);
    }
    
    $this->fields = $fields;
  }
   
}

