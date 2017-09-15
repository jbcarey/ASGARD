<?php

namespace Drupal\e2e_product\Includes;


class ProductForm {
  
  private $form;
  
  private static $fieldnames = [
    'field_ipdc_title', 'field_ipdc_what', 'field_ipdc_when', 'field_ipdc_conditions', 'field_ipdc_procedure',
    'field_ipdc_bringwhat', 'field_ipdc_amount', 'field_ipdc_exceptions', 'field_ipdc_rules',
    'field_ipdc_type', 'field_ipdc_target', 'field_ipdc_scope', 'field_ipdc_theme', 'field_ipdc_keywords',
    'field_ipdc_reference',
  ];
  
  //constructor
  public function __construct($form) {
    $this->form = $form;
  }
  
  //get field default value
  public function getWidgetDefault($widget){
    
    if(!empty($widget[0])){
      
      $defaultvalue = '';
      
      foreach($widget as $widgetvalue){
        
        if(!empty($widgetvalue['target_id']['#default_value'])){
          $term = $widgetvalue['target_id']['#default_value'];
          
          $defaultvalue .= '<p>' . $term->label() . '</p>';
          
        }elseif(isset($widgetvalue['value']['#type']) && $widgetvalue['value']['#type'] == 'datetime'){
          $defaultvalue .= $this->getDateDefault($widgetvalue);
        }else{
          
          if(!empty($widgetvalue['#default_value']))
            $defaultvalue .= $widgetvalue['#default_value'];

          if(!empty($widgetvalue['value']['#default_value']))
            $defaultvalue .= $widgetvalue['value']['#default_value'];

        }
      }
      
      if($defaultvalue)
        return $defaultvalue;
    }
    
    return '<p>' . t('No content available.') . '</p>';
  }
  
  public function getDateDefault($widgetvalue){
    
    $defaultvalue = '';
    
    if(!empty($widgetvalue['value']['#default_value'])){
      $datetime = $widgetvalue['value']['#default_value'];
      
      $defaultvalue = $datetime->format('d-m-Y H:i:s');
    }
    
    if(!empty($widgetvalue['end_value']['#default_value'])){
      $datetime = $widgetvalue['end_value']['#default_value'];
      
      if($defaultvalue)
        $defaultvalue .= ' - ';
      
      $defaultvalue .= $datetime->format('d-m-Y H:i:s');
    }
    
    return '<p>' . $defaultvalue . '</p>';
    
  }
  
  //return all ipdc fieldnames
  public function getFieldNames(){
    return self::$fieldnames;
  }
  
  public function getFormField($fieldname){
    
    $widget = $this->form[$fieldname]['widget'];
    
    $title = '';
    
    if(!empty($widget['#title']))
      $title = '<strong>' . $widget['#title'] . '</strong>';
      
    $defaultvalue = $this->getWidgetDefault($widget);
    
    return ['#markup' => $title . $defaultvalue, '#prefix' => '<div class="form-item type-rendered">', '#suffix' => '</div>'];
  }
  
  
}

