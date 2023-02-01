<?php

/**
 * @file
 * Contains Drupal\paragraphs\Form\ParagraphsTypeForm.
 */

namespace Drupal\paragraphs\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

class ParagraphsTypeForm extends EntityForm
{

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    // Somehow paragraphs type are not loaded anymore from the route
    // for the edit form, so $this->entity won't work. Workaround it for now.
    $paragraphs_type_parameter = \Drupal::routeMatch()->getRawParameter('paragraph');
    if (!empty($paragraphs_type_parameter)) {
      $paragraphs_type = \Drupal::entityTypeManager()
        ->getStorage('paragraphs_type')
        ->load($paragraphs_type_parameter);
      $this->entity = $paragraphs_type;
      if (!$paragraphs_type->isNew()) {
        $form['#title'] = ($this->t('Edit %title paragraph type', [
          '%title' => $paragraphs_type->label(),
        ]));
      }
    }
    else {
      $paragraphs_type = $this->entity;
    }

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $paragraphs_type->label(),
      '#description' => $this->t("Label for the Paragraphs type."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $paragraphs_type->id(),
      '#machine_name' => array(
        'exists' => 'paragraphs_type_load',
      ),
      '#maxlength' => 32,
      '#disabled' => !$paragraphs_type->isNew(),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $paragraphs_type = $this->entity;
    $status = $paragraphs_type->save();

    if ($status) {
      $this->messenger()->addMessage($this->t('Saved the %label Paragraphs type.', [
        '%label' => $paragraphs_type->label(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('The %label Paragraphs type was not saved.', [
        '%label' => $paragraphs_type->label(),
      ]));
    }
    $form_state->setRedirect('entity.paragraphs_type.collection');
  }
}
