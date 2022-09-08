<?php

namespace Drupal\form_task\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class FormTask extends FormBase {
    
    /* 
        Get our form
    */
    public function getFormId()
    {
        return 'form_task';
    }

    /* 
        Create our form
    */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['task_one'] = array(
            '#type' => 'textfield',
            '#title' => t('Enter task:'),
            '#required' => TRUE,
        );
        return $form;
    }

    /* 
        Check our form
    */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if(strlen($form_state->getValue('task_one')) < 2) {
            $form_state->setErrorByName('task_one', $this->t('Please enter a valid task'));
        }
    }

    /*
        Submnit the form and it's data
    */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        \Drupal::messenger()->addMessage(t("Task list submitted!"));
            foreach ($form_state->getValues() as $key => $value) {
                \Drupal::messenger()->addMessage($key . ': ' . $value);
            }
    }

}