<?php

namespace Drupal\hello_world\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hello_world\HelloWorldRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sample UI to update a record.
 *
 * @ingroup dbtng_example
 */
class HelloWorldUpdateForm extends FormBase {


  protected $repository;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hello_world_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $form = new static($container->get('hello_world.repository'));
    $form->setStringTranslation($container->get('string_translation'));
    $form->setMessenger($container->get('messenger'));
    return $form;
  }

  /**
   * Construct the new form object.
   */
  public function __construct(HelloWorldRepository $repository) {
    $this->repository = $repository;
  }

  /**
   * Sample UI to update a record.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Wrap the form in a div.
    $form = [
      '#prefix' => '<div id="updateform">',
      '#suffix' => '</div>',
    ];
    // Add some explanatory text to the form.
    $form['message'] = [
      '#markup' => $this->t('Demonstrates a database update operation.'),
    ];
    // Query for items to display.
    $entries = $this->repository->load();
    // Tell the user if there is nothing to display.
    if (empty($entries)) {
      $form['no_values'] = [
        '#value' => $this->t('No entries exist in the table hello_world table.'),
      ];
      return $form;
    }

    $keyed_entries = [];
    $options = [];
    foreach ($entries as $entry) {
      $options[$entry->task] = $this->t('@name', [
        '@task' => $entry->task,
      ]);
      $keyed_entries[$entry->task] = $entry;
    }

    // Grab the pid.
    $task = $form_state->getValue('task');
    // Use the pid to set the default entry for updating.
    $default_entry = !empty($task) ? $keyed_entries[$task] : $entries[0];

    // Save the entries into the $form_state. We do this so the AJAX callback
    // doesn't need to repeat the query.
    $form_state->setValue('entries', $keyed_entries);

    $form['task'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Task'),
      '#size' => 15,
      '#default_value' => $default_entry->task,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];
    return $form;
  }

  public function updateCallback(array $form, FormStateInterface $form_state) {
    // Gather the DB results from $form_state.
    $entries = $form_state->getValue('entries');
    // Use the specific entry for this $form_state.
    $entry = $entries[$form_state->getValue('task')];
    // Setting the #value of items is the only way I was able to figure out
    // to get replaced defaults on these items. #default_value will not do it
    // and shouldn't.
    foreach (['task'] as $item) {
      $form[$item]['#value'] = $entry->$item;
    }
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Gather the current user so the new record has ownership.
    $account = $this->currentUser();
    // Save the submitted entry.
    $entry = [
      'task' => $form_state->getValue('task'),
    ];
    $count = $this->repository->update($entry);
    $this->messenger()->addMessage($this->t('Updated entry @entry (@count row updated)', [
      '@count' => $count,
      '@entry' => print_r($entry, TRUE),
    ]));
  }

}
