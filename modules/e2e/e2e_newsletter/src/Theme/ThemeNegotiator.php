<?php

namespace Drupal\e2e_newsletter\Theme;

use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class ThemeNegotiator implements ThemeNegotiatorInterface{
  
  /*
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match){
    
    $parameters = $route_match->getParameters();
    
    if($parameters->has('node') && $parameters->get('node')){
      $node = $parameters->get('node');
      
      if($node->getType() == 'newsletter')
        return TRUE;
      
    }
    
    return FALSE;
  }
  
  /*
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    
    $node = $route_match->getParameters()->get('node');
    $config = $node->field_newsletter_theme->entity;
    
    if($config)
      return $config->theme;
  }
  
}

