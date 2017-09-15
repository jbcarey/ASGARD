<?php

namespace Drupal\e2e_folders_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;
use Drupal\Core\Entity\FieldableEntityInterface;

use Drupal\e2e_folders_field\Includes\FoldersFieldHelper;

/**
 * Plugin implementation of the 'FoldersWidget' widget.
 *
 * @FieldWidget(
 *   id = "FoldersWidget",
 *   label = @Translation("Folder select"),
 *   field_types = {
 *     "Folders"
 *   },
 *   multiple_values = TRUE
 * )
 */
class FoldersWidget extends OptionsWidgetBase {
 
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
      '#options' => FoldersFieldHelper::getFolders(),
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
      '#multiple' => TRUE,
    ];

    return $element;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getOptions(FieldableEntityInterface $entity) {
    
    if (!isset($this->options)) 
      $this->options = FoldersFieldHelper::getFolders();
    
    
    return $this->options;
  }
}