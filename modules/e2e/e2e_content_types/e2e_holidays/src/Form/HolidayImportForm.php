<?php

namespace Drupal\e2e_holidays\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\e2e_holidays\Includes\HolidayImport;

class HolidayImportForm extends FormBase{

  /*
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'e2e_holiday_import_form';
  }
  
  /*
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['importfile'] = [
      '#title' => 'List files',
      '#type' => 'select',
      '#options' => $this->getFileOptions(),
      '#required' => TRUE,
      '#multiple' => TRUE,
    ];
    
    $form['submit'] = [
      '#type' => 'submit', 
      '#value' => t('Import holidays'),
    ];

    return $form;
  }
  
  /*
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $files = $form_state->getValue('importfile');
    $imported = FALSE;
    
    foreach($files as $file){
      $import = new HolidayImport($file);
      $import->importNode();
      
      if(!$imported)
        $imported = $import->isImported();
    }
    
    if(!$imported){
      $message = t('No files where imported.');
      $status = 'alert';
    }else{
      $message = t('Files where imported.');
      $status = 'status';
    }
    
    drupal_set_message($message, $status);
    
  }
  
  private function getFileOptions(){
    
    $files = file_scan_directory(drupal_get_path('module', 'e2e_holidays') . '/files', '/.*\.csv$/');
  
    if(is_array($files))
      return array_column($files, 'name', 'uri');
  
    return [];
  }
  
}