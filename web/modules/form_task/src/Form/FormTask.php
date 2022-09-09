<?php

namespace Drupal\form_task\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements a example task form using FORM API.
 */
class FormTask extends FormBase {


  public function getFormId() {
    return 'form_task';
  }

  /**
   * Creates form using form api
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['task_one'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your task'),
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * Validates the length of the task entered
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('task_one')) < 1) {
      $form_state->setErrorByName('task_one', $this->t('The task entered is too short, enter a longer one!'));
    }
  }

  /**
   * Submits form and displays entered task
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('Your task is @task', ['@task' => $form_state->getValue('task_one')]));
  }

}