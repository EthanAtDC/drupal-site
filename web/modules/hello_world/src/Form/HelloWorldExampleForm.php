<?php

namespace Drupal\hello_world\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Render\Element\Table;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\hello_world\HelloWorldRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to add a database entry, with all the interesting fields.
 *
 * @ingroup dbtng_example
 */
class HelloWorldExampleForm implements FormInterface, ContainerInjectionInterface {

  use StringTranslationTrait;
  use MessengerTrait;

  protected $repository;
  protected $currentUser;

  public static function create(ContainerInterface $container) {
    $form = new static(
      $container->get('hello_world.repository'),
      $container->get('current_user')
    );
    // The StringTranslationTrait trait manages the string translation service
    // for us. We can inject the service here.
    $form->setStringTranslation($container->get('string_translation'));
    $form->setMessenger($container->get('messenger'));
    return $form;
  }

  /**
   * Construct the new form object.
   */
  public function __construct(HelloWorldRepository $repository, AccountProxyInterface $current_user) {
    $this->repository = $repository;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hello_world_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    // This is the form where user enters the task
    $form = [];

    $form['add']['task'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Task'),
      '#size' => 40,
    ];
    
    $form['add']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];
    $content = [];
    $rows = [];
    $headers = [
    $this->t('Tasks'),
    ];

    // This is where we grab the data from the db and display it for the user
    $entries = $this->repository->load();

    foreach ($entries as $entry) {
      // Sanitize each entry.
    $rows[] = array_map('Drupal\Component\Utility\Html::escape', (array) $entry);
    }
    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => $this->t('No entries available.'),
    ];

    $form['entry_list'] = $content;

    return $form;
  }

  
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('task');
    
    if (strlen($title) < 2) {

      $form_state->setErrorByName('task', $this->t('The title must be at least 2 characters long.'));

    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Gather the current user so the new record has ownership.
    $account = $this->currentUser;
    // Save the submitted entry.
    $entry = [
      'task' => $form_state->getValue('task'),
    ];
    $return = $this->repository->insert($entry);
    if ($return) {
      $this->messenger()->addMessage($this->t('Created entry @entry', ['@entry' => print_r($entry, TRUE)]));
    }
  }

}
