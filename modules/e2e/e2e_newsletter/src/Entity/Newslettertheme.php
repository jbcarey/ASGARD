<?php

namespace Drupal\e2e_newsletter\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\e2e_newsletter\NewsletterthemeInterface;

/**
 * Defines the Newslettertheme entity.
 *
 * @ConfigEntityType(
 *   id = "newslettertheme",
 *   label = @Translation("Newsletter theme"),
 *   handlers = {
 *     "list_builder" = "Drupal\e2e_newsletter\Controller\NewsletterthemeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\e2e_newsletter\Form\NewsletterthemeForm",
 *       "edit" = "Drupal\e2e_newsletter\Form\NewsletterthemeForm",
 *       "delete" = "Drupal\e2e_newsletter\Form\NewsletterthemeDeleteForm",
 *     }
 *   },
 *   config_prefix = "newslettertheme",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/services/e2e-newsletter/{newslettertheme}",
 *     "delete-form" = "/admin/config/services/e2e-newsletter/delete/{newslettertheme}",
 *   }
 * )
 */

class Newslettertheme extends ConfigEntityBase implements NewsletterthemeInterface{

  
  public $id;
  public $label;
  public $theme;
  
}