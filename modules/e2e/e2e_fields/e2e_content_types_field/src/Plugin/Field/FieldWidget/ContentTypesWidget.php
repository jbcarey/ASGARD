<?php

namespace Drupal\e2e_content_types_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\node\Entity\NodeType;

/**
 * Plugin implementation of the 'ContentTypesWidget' widget.
 *
 * @FieldWidget(
 *   id = "ContentTypesWidget",
 *   label = @Translation("Content type select"),
 *   field_types = {
 *     "ContentTypes"
 *   },
 *   multiple_values = TRUE
 * )
 */
class ContentTypesWidget extends OptionsWidgetBase {

    /**
  * {@inheritdoc}
  */
  public static function defaultSettings() {
    
    $settings = parent::defaultSettings();
    $settings['exclusive'] = [];
    
    return $settings;
  }
  
  /**
  * {@inheritdoc}
  */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    
    $element['exclusive'] = [
      '#type' => 'checkboxes',
      '#title' => t('Exclude selected folders'),
      '#options' => $this->getContentTypes(),
      '#default_value' => $this->getSetting('exclusive'),
      '#multiple' => TRUE,
    ];
    
    return $element;
  }
  
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element += [
      '#type' => 'checkboxes',
      '#options' => $this->getOptions($items->getEntity()),
      '#default_value' => $this->getSelectedOptions($items),
      // Do not display a 'multiple' select box if there is only one option.
      '#multiple' => $this->multiple && count($this->options) > 1,
    ];

    return $element;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getOptions(FieldableEntityInterface $entity) {
    
    if (!isset($this->options))
      $this->options = $this->getContentTypes();
    
    return $this->options;
  }
  
  //get list op content types
  private function getContentTypes(){
  
    $nodetypes = NodeType::loadMultiple();
    $nodetypeoptions = [];
      
    // Add an empty option if the widget needs one.
    if ($empty_label = $this->getEmptyLabel())
      $nodetypeoptions['_none'] = $empty_label;
      
    foreach($nodetypes as $nodetype){
      $nodetypeoptions[$nodetype->id()] = $nodetype->label();
    }
    
    return $nodetypeoptions;
    
  }

}


