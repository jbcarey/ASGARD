<?php

namespace Drupal\e2e_paragraphs_overview\Includes;

use Drupal\Core\Url;
use Drupal\Core\Link;

class OverviewMarkup{
 
  //order nodes in columns
  public static function getColumns($nodes, $columns, $classes, $direction = 'horizontal', $attributes = ''){
    
    //order in rows
    if($direction == 'horizontal'){
        $attributes = ' data-columns="' . $columns . '"';
        $classes .= ' hsort';
        $horder = self::getHorizontalOrder(count($nodes), $columns);
        $count = 0;
        
        foreach($nodes as $key => &$node){
          
          if(is_numeric($key)){
            
            $node['#node']->order = $count;
            $node['#weight'] = $horder[$count];
            $count++;
          
          }
        }
        
    }
    
    return '<div class="columns ' . $classes . '"' . $attributes . '>' . render($nodes) . '</div>'; 
  }
  
  //get nodes ordered in glossary
  public static function getGlossary($nodes, $columns, $currentletter) {
    
    $parameters = \Drupal::request()->query->all();
    $currentpath = \Drupal::service('path.current')->getPath();
    $content = array();

    $index = array('#prefix' => '<div class="index inline-menu"><ul class="menu">', '#suffix' => '</ul></div>');
    $index['all'] = self::getGlossaryIndex($currentpath, $parameters, $currentletter, 'all');

    foreach($nodes as $nid => $node) {

        $letter = strtolower($node->title->value[0]);
        
        if (!isset($content[$letter])) {
          $index[$letter] = self::getGlossaryIndex($currentpath, $parameters, $currentletter, $letter);
          $content[$letter] = self::getGlossaryLetter($index[$letter]['#markup']);
        }
        
        if($currentletter == 'all' || $currentletter == $letter){
          $link = Link::fromTextAndUrl($node->title->value, Url::fromUri('internal:/node/' . $nid));
          $content[$letter]['#markup'] .= '<li>' . $link->toString() . '</li>';
        }
    }
    
    if($currentletter == 'all' || !isset($content[$currentletter]))
      $glossary = self::getColumns ($content, $columns, 'gutter', 'vertical');
    else
      $glossary = render($content[$currentletter]);

    return '<div class="glossary">' . render($index) . $glossary . '</div>';

  }
  
  //get glossary index item
  private static function getGlossaryIndex($path, $parameters, $currentletter, $letter){
    
    $parameters['letter'] = $letter;
    
    $url = Url::fromUri('base:' . $path);
    $url->setOptions(array('attributes' => array('class' => array('padding-small', ($currentletter == $letter) ? 'active' : null)), 'query' => $parameters));
    
    return array(
      '#markup' => Link::fromTextAndUrl(t(ucfirst($letter)), $url)->toString(),
      '#prefix' => '<li>',
      '#suffix' => '</li>',
    );

  }
  
  //get glossary letter
  private static function getGlossaryLetter($header){
    
    return array(
      '#markup' => '',
      '#prefix' => '<div class="box teaser inner glossary-teaser padding-small"><h3 class="node-title">' . $header . '</h3><ul class="menu list">',
      '#suffix' => '</ul></div>',
    );
  }
  
  //get node links
  public static function getLinks($nodes) {
    
    $view = array('#prefix' => '<ul class="menu list">', '#suffix' => '</ul>');
    
    foreach ($nodes as $nid => $node) {
      
      $link = Link::fromTextAndUrl($node->title->value, Url::fromUri('internal:/node/' . $nid));
      $view[$nid] = array('#markup' => '<li>' . $link->toString() . '</li>');
    }
    
    return $view;
  }
  
  //get order of nodes in horizontal sorting
  private static function getHorizontalOrder($total, $columns) {
    
    $rows = ceil($total / $columns);
    
    $order = array();
    
    for($i = 0; $i < $total; $i++){
      $order[$i] = ceil(($i + 1) / $rows) + (($i % $rows) * $columns);
    }
    
    asort($order);
    
    return array_flip(array_keys($order));
  }
}

