<?php

namespace Drupal\hello_world\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\dbtng_example\DbtngExampleRepository;
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
    $form = [];

    $form['add']['task'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Task'),
      '#size' => 15,
    ];

    $form['add']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Verify that the user is logged-in.
    if ($this->currentUser->isAnonymous()) {
      $form_state->setError($form['add'], $this->t('You must be logged in to add values to the database.'));
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
      'uid' => $account->id(),
    ];
    $return = $this->repository->insert($entry);
    if ($return) {
      $this->messenger()->addMessage($this->t('Created entry @entry', ['@entry' => print_r($entry, TRUE)]));
    }
  }

}
