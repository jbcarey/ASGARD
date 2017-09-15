<?php
namespace Drupal\e2e_paragraphs_map_overview\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Contribute form.
 */
class ParagraphsMapOverviewForm extends FormBase {

  /**
   * Get form id
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'map-filters';
  }

  /**
   * Build form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $settings = array()) {
    
    $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($settings['tids']);
    
    $options = array();
    
    foreach($terms as $tid => $term){
      $options['type' . $tid] = $term->name->value;
    }
    
    $form['filters'] = array(
      '#type' => 'checkboxes',
      '#options' => $options,
	);
    
    $form['#cache'] = ['max-age' => 0];
    
    return $form;

  }
  
 /**
 * {@inheritdoc}
 */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $route = \Drupal::routeMatch();
    
    $parameters = [
      'node' => $route->getRawParameter('node'),
    ];
    
    $form_state->setRedirect($route->getRouteName(), $parameters);
  }

}