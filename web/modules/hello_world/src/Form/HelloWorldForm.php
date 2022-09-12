<?php

namespace Drupal\hello_world\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the SimpleForm form controller.
 *
 * This example demonstrates a simple form with a single text input element. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class HelloWorldForm extends FormBase {


  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['task'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Task'),
      '#description' => $this->t('Task must be at least 2 characters in length.'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function getFormId() {
    return 'hello_world_hello_world_form';
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('task');
    
    if (strlen($title) < 2) {

      $form_state->setErrorByName('task', $this->t('The title must be at least 2 characters long.'));

    }

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $title = $form_state->getValue('task');
    $this->messenger()->addMessage($this->t('You specified a title of %title.', ['%title' => $title]));

  }

}
