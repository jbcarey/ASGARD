<?php

namespace Drupal\e2e_hours_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetBase;

/**
 * Plugin implementation of the 'HoursWidget' widget.
 *
 * @FieldWidget(
 *   id = "HoursWidget",
 *   label = @Translation("Hours select"),
 *   field_types = {
 *     "Hours"
 *   },
 * )
 */
class HoursWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    
    //$element['#theme'] = 'field_hours_formatter';
    
    $element['title'] = array(
      '#type' => 'markup',
      '#markup' => '<strong>' . $element['#title'] . '</strong>',
    );
    
    $element['time_from'] = array(
      '#type' => 'textfield',
      '#size' => 5,
      '#default_value' => isset($items[$delta]->time_from) ? $items[$delta]->time_from : NULL,
      '#empty_value' => '',
      '#placeholder' => '00:00',
      '#attributes' => array('class' => array('timefield')),
    );
    
    $element['time_until'] = array(
      '#type' => 'textfield',
      '#size' => 5,
      '#default_value' => isset($items[$delta]->time_until) ? $items[$delta]->time_until : NULL,
      '#empty_value' => '',
      '#placeholder' => '00:00',
      '#attributes' => array('class' => array('timefield')),
    );
    
    
    $element['time_note'] = array(
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => isset($items[$delta]->time_note) ? $items[$delta]->time_note : NULL,
      '#empty_value' => '',
      '#placeholder' => 'Note',
    );
    
    // add js
	//$element['#attached']['library'][] = 'e2e_hours_field/hours_field_widget';
    
    return $element;
  }
}

