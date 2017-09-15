<?php

namespace Drupal\e2e_menu_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Plugin implementation of the 'MenuWidget' widget.
 *
 * @FieldWidget(
 *   id = "MenuWidget",
 *   label = @Translation("Menu"),
 *   field_types = {
 *     "Menu"
 *   },
 * )
 */
class MenuWidget extends WidgetBase implements WidgetInterface {


  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    
    $wrapperid = implode('_', $element['#field_parents']) . '_' . $delta . '_wrapper';
    
    $element['#prefix'] = '<div id="' . $wrapperid . '">';
    $element['#suffix'] = '</div>';
    
    $menuoptions = ['active-menu' => ' -' . t('Active menu') . '- '] + menu_ui_get_menus();
    
    $element['menu_name'] = [
      '#title' => t('Menu'),
      '#type' => 'select',
      '#options' => $menuoptions,
      '#default_value' => isset($items[$delta]->menu_name) && isset($menuoptions[$items[$delta]->menu_name]) ? $items[$delta]->menu_name : 'active-menu',
      '#multiple' => FALSE,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [get_class($this), 'rebuildCallback'], 
        'wrapper' => $wrapperid,
      ],
    ];
    
    if(!empty($items[$delta]->menu_name) && $items[$delta]->menu_name != 'active-menu'){
      
      $tree = \Drupal::menuTree()->load($items[$delta]->menu_name, new MenuTreeParameters());
      list($plidoptions, $menudepth) = ['' => '<ROOT>'] + $this->getMenuParentOptions($tree);
      
      if($plidoptions){
      
        $element['menu_plid'] = [
          '#title' => t('Menu parent'),
          '#type' => 'select',
          '#options' => $plidoptions,
          '#default_value' => isset($items[$delta]->menu_plid) && isset($plidoptions[$items[$delta]->menu_plid]) ? $items[$delta]->menu_plid : '',
          '#multiple' => FALSE,
          '#required' => FALSE,
        ];
      }
      
    }else{
      $menudepth = 10;
    }
    
    if($menudepth){
    
      $depthrange = range(1, $menudepth);
      
      $element['menu_level'] = [
        '#title' => t('Show links below until level'),
        '#type' => 'select',
        '#options' => array_combine($depthrange, $depthrange),
        '#default_value' => isset($items[$delta]->menu_level) && $items[$delta]->menu_level <= $menudepth ? $items[$delta]->menu_level : 1,
        '#multiple' => FALSE,
        '#required' => FALSE,
      ];

    }
    
    $columnrange = range(1, 5);
    
    $element['columns'] = [
      '#title' => t('Columns'),
      '#type' => 'select',
      '#options' => array_combine($columnrange, $columnrange),
      '#default_value' => isset($items[$delta]->columns) && $items[$delta]->columns <= 5 ? $items[$delta]->columns : 1,
      '#multiple' => FALSE,
      '#required' => FALSE,
    ];
    
    /*$displayoptions = ['list' => 'List', 'tree' => 'Tree', 'blocks' => 'Blocks'];
    
    $element['display'] = [
      '#title' => t('Display'),
      '#type' => 'select',
      '#options' => $displayoptions,
      '#default_value' => isset($items[$delta]->display) && isset($displayoptions[$items[$delta]->display]) ? $items[$delta]->display : 'list',
      '#multiple' => FALSE,
      '#required' => TRUE,
    ];*/
    
    return $element;
  }
  
  //get all menu items with children in a menu tree structure
  protected function getMenuParentOptions($tree){
    
    $options = [];
    $depth = 0;
    
    foreach($tree as $key => $item){
      if($item->hasChildren){
        $options[$key] = str_pad('', $item->depth, '-') . ' ' . $item->link->getTitle();
        list($suboptions, $subdepth) = $this->getMenuParentOptions($item->subtree);
        $options += $suboptions;
        $depth = max($depth, $item->depth, $subdepth);
      }
    }
    return [$options, $depth];
  }
  
  //ajax callback to rebuild parent menu items and menu level based on selected menu
  public function rebuildCallback(&$form, FormStateInterface $form_state){
  
    $menuselect = $form_state->getTriggeringElement();
  
    $element = $form;
    
    //get triggering element menu field parent
    foreach(array_slice($menuselect['#array_parents'], 0, -3) as $parent){
      if(isset($element[$parent]))
        $element = $element[$parent];
    }
    
    return $element;
  }
  
}