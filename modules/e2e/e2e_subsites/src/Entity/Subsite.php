<?php

namespace Drupal\e2e_subsites\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\e2e_subsites\SubsiteInterface;

/**
 * Defines the Subsite entity.
 *
 * @ConfigEntityType(
 *   id = "subsite",
 *   label = @Translation("Subsite"),
 *   handlers = {
 *     "list_builder" = "Drupal\e2e_subsites\Controller\SubsiteListBuilder",
 *     "form" = {
 *       "add" = "Drupal\e2e_subsites\Form\SubsiteForm",
 *       "edit" = "Drupal\e2e_subsites\Form\SubsiteForm",
 *       "delete" = "Drupal\e2e_subsites\Form\SubsiteDeleteForm",
 *     }
 *   },
 *   config_prefix = "subsite",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/services/e2e-subsites-settings/{subsite}",
 *     "delete-form" = "/admin/config/services/e2e-subsites-settings/delete/{subsite}",
 *   }
 * )
 */

class Subsite extends ConfigEntityBase implements SubsiteInterface{

  
  public $id;
  public $label;
  public $theme;
  public $section;
  public $role;
  public $weight;
  public $paths;
  public $nodetypepaths;
  
  public function getSectionName(){
    $term = Term::load($this->section);
    
    if($term)
      return $term->getName();
    
    return '';
  }
  
}