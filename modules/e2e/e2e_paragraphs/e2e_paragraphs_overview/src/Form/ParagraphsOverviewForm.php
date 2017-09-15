<?php
namespace Drupal\e2e_paragraphs_overview\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;

use Drupal\e2e_paragraphs_overview\Includes\OverviewQuery;


/**
 * Contribute form.
 */
class ParagraphsOverviewForm extends FormBase {

  /**
   * Get form id
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'e2e_paragraphs_overview_form';
  }

  /**
   * Build form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $settings = array()) {
    
    $form['#prefix'] = '<div id="Overview-Filter-Form">';
    $form['#suffix'] = '</div>';
    
    $form['#attributes']['class'] = ['filters','gutter-small','iblocks','va-top'];

    // keywords text field
    if(in_array('keywords',$settings['filters'])) {
      $form['keywords'] = [
        '#title' => t('Keywords'),
        '#type' => 'textfield',
        '#size' => 32,
        '#maxlength' => 255,
        '#default_value' => $settings['keywords'],
        '#attributes' => ['placeholder' => t('Keywords')],
        '#filter' => TRUE,
      ];
    }
	
    //taxonomy select field
    if(in_array('taxonomy', $settings['filters']))
      $form['taxonomy'] = $this->getTaxonomyFilters($settings);
    
    $form += $this->getSelectFilters($settings);
    
    // date filters
    if(in_array('event',$settings['content_types'])) {
      $form['date_from'] = $this->getDateFilter('Date', $settings['date_from']);
      $form['date_to'] = $this->getDateFilter('End date', $settings['date_to']);
    }

    // submit button
    $form['submit_button'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#prefix' => '<div class="form-item form-type-button form-item-submit iblock va-bottom">',
      '#suffix' => '</div>',
    ];
    
    $form['#cache'] = ['max-age' => 0];
    
    return $form;

  }
  
  //get select filters
  private function getSelectFilters($settings){
    
    $definitions = \Drupal::service('entity_field.manager')->getFieldStorageDefinitions('paragraph', 'overview');
    $displayoptions = options_allowed_values($definitions['field_display']);
    
    $filters = [];
    
    $filteroptions = [
      'order' => ['title' => t('Alphabetical'),'created' => t('Post date'), 'changed' => t('Last edited')],
      'sort' => ['ASC' => t('Ascending'),'DESC' => t('Descending')],
      'display' => $displayoptions,
    ];
    
    foreach($filteroptions as $filtername => $selectoptions){
      if(in_array($filtername, $settings['filters'])){
        $filters[$filtername] = [
          '#title' => t(ucfirst($filtername)),
          '#type' => 'select',
          '#options' => $selectoptions,
          '#default_value' => $settings[$filtername],
          '#filter' => TRUE,
        ];
      }
    }
    
    return $filters;
    
  }
  
  //get taxonomy filters
  private function getTaxonomyFilters($settings){
    
    $vids = $this->getVids($settings);
    $filters = [];
    
    $storage = \Drupal::service('entity_type.manager')->getStorage('0'
        . '_term');
    
    
    foreach($vids as $vid){
      
      $vocabulary = Vocabulary::load($vid);
      $termtree = $storage->loadTree($vid);
      
      $filters['voc_' . $vid] = [
        '#title' => $vocabulary->label(),
        '#type' => 'select',
        '#options' => $this->getTaxonomyFilterOptions($termtree),
        '#default_value' => isset($settings['taxonomy']['voc_' . $vid]) ? [0 => $settings['taxonomy']['voc_' . $vid]] : [],
        '#filter' => TRUE,
      ];
    }
    
    return $filters;
  }
  

  //get all vids of vocabulary used by overview nodes
  private function getVids($settings) {

    $vids = [];
    
    $query = new OverviewQuery();
    $query->baseConditions($settings['content_types'], $settings['nid'], $settings['langcode']);
    
    if($settings['tags'])
      $query->tagConditions($settings['tags']);
    
    if(count($settings['content_types']) == 1 && in_array('event',$settings['content_types']))
      $query->eventConditions($settings['date_from'], $settings['date_to']);
    
    if ($nids = $query->getNids()) {
      
      $query = \Drupal::database()->select('taxonomy_index', 't');
      $query->join('taxonomy_term_data', 'td', 't.tid = td.tid');
      $query->addField('td', 'vid');
      $query->addField('td', 'vid');
      $query->condition('t.nid', $nids, 'IN');
      $query->condition('td.vid', ['tags', 'block_tags'], 'NOT IN');
      $query->groupBy('td.vid');
      
      $vocs = $query->execute()->fetchAll();
      
      if($vocs)
        $vids = array_column($vocs, 'vid');
    }
    
    return $vids;
  }
  
  //get options based for a single taxonomy filter
  private function getTaxonomyFilterOptions($terms){
    
    $filteroptions = ['' => ' --' .t('All categories') . '-- '];
    
    foreach($terms as $term){
      $filteroptions[$term->tid] = $term->name;
    }
    
    return $filteroptions;
  }
  
  //get date popup filter
  private function getDateFilter($label, $default){
    
    return [
      '#title' => t($label),
      '#type' => 'date',
      '#date_timezone' => date_default_timezone_get(),
      '#date_format' => 'd-m-Y',
      '#date_year_range' => '-3:+3',
      '#date_label_position' => 'hidden',
      '#default_value' => $default,
      '#prefix' => '<div class="form-item filter iblock">',
      '#suffix' => '</div>',
    ];
    
  }

/**
 * {@inheritdoc}
 */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $route = \Drupal::routeMatch();
    
    $formvalues = $form_state->getValues();
    
    
    
    $parameters = [
      'node' => $route->getRawParameter('node'),
      'taxonomy' => [],
    ];
    
    $paramkeys = ['keywords', 'order', 'sort', 'display', 'date_from', 'date_to'];
    
    foreach($paramkeys as $paramkey){
      if(!empty($formvalues[$paramkey]))
        $parameters[$paramkey] = $formvalues[$paramkey];
    }
    
    
    foreach($formvalues as $key => $value){
      
      if(substr($key, 0, 4) == 'voc_' && $value)
          $parameters['taxonomy'][$key] = $value;
    }
    
    $form_state->setRedirect($route->getRouteName(), $parameters);
  }

}