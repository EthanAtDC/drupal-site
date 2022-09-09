<?php

function form_task_tasks_page() {
    return drupal_get_form('form_task_page');
}

function form_task_page(&$form_state) {
    $form = array();

    $form['Task'] => array(
        '#type' => 'select',
        '#title' => t('Task type'),
        '#description' => t('What would you like to do'),
        '#options' => array(
            'run' => t('Go for a run'),
            'sit' => t('Sitting is no fun!')
            'bike' => t('Go for a bike ride'),
            'ski' => t('Go skiing!!'),
        ),
        '#defualt_value' => 'sit',
        '#required' => TRUE,
    );

    $form['buttons']['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit task'),
    );

    return $form;
}