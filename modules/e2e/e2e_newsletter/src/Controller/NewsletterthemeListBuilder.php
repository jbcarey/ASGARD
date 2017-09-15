<?php

namespace Drupal\e2e_newsletter\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

class NewsletterthemeListBuilder extends ConfigEntityListBuilder{
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    
    $header['label'] = $this->t('Name');
    $header['theme'] = $this->t('Theme');
    
    return $header + parent::buildHeader();
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    
    $row['label'] = $this->getLabel($entity);
    $row['theme'] = $entity->theme;
    
    return $row + parent::buildRow($entity);
  }
}