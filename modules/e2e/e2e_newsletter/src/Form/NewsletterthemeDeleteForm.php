<?php

namespace Drupal\e2e_newsletter\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

class NewsletterthemeDeleteForm extends EntityConfirmFormBase{

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete newsletter theme %label?', ['%label' => $this->entity->label()]);
  }
  
  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.newslettertheme.list');
  }
  
  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete newsletter theme');
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   
    $this->entity->delete();
    
    drupal_set_message($this->t('Newsletter theme %label has been deleted.', ['%label' => $this->entity->label()]));
    
    $form_state->setRedirectUrl($this->getCancelUrl());
    
  }
  
}