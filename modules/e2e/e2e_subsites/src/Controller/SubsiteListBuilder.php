<?php

namespace Drupal\e2e_subsites\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

class SubsiteListBuilder extends ConfigEntityListBuilder{
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    
    $header['label'] = $this->t('Subsite');
    $header['id'] = $this->t('Machine name');
    $header['theme'] = $this->t('Theme');
    $header['section'] = $this->t('Section');
    $header['role'] = $this->t('Role');
    $header['weight'] = $this->t('Weight');
    
    return $header + parent::buildHeader();
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    
    $row['label'] = $this->getLabel($entity);
    $row['id'] = $entity->id();
    $row['theme'] = $entity->theme;
    $row['section'] = $entity->getSectionName();
    $row['role'] = $entity->role;
    $row['weight'] = $entity->weight;
    
    return $row + parent::buildRow($entity);
  }
}