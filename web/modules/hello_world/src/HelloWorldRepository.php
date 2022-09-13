<?php

namespace Drupal\hello_world;

use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;


class HelloWorldRepository {

  use MessengerTrait;
  use StringTranslationTrait;

  protected $connection;

  public function __construct(Connection $connection, TranslationInterface $translation, MessengerInterface $messenger) {
    $this->connection = $connection;
    $this->setStringTranslation($translation);
    $this->setMessenger($messenger);
  }


  public function insert(array $entry) {
    try {
      $return_value = $this->connection->insert('hello_world')
        ->fields($entry)
        ->execute();
    }
    catch (\Exception $e) {
      $this->messenger()->addMessage($this->t('Insert failed. Message = %message', [
        '%message' => $e->getMessage(),
      ]), 'error');
    }
    return $return_value ?? NULL;
  }

  public function update(array $entry) {
    try {
      // Connection->update()...->execute() returns the number of rows updated.
      $count = $this->connection->update('hello_world')
        ->fields($entry)
        ->condition('task', $entry['task'])
        ->execute();
    }
    catch (\Exception $e) {
      $this->messenger()->addMessage($this->t('Update failed. Message = %message, query= %query', [
        '%message' => $e->getMessage(),
        '%query' => $e->query_string,
      ]
      ), 'error');
    }
    return $count ?? 0;
  }


  public function delete(array $entry) {
    $this->connection->delete('hello_world')
      ->condition('task', $entry['task'])
      ->execute();
  }

  public function load(array $entry = []) {
    // Read all the fields from the dbtng_example table.
    $select = $this->connection
      ->select('hello_world')
      // Add all the fields into our select query.
      ->fields('hello_world');

    // Add each field and value as a condition to this query.
    foreach ($entry as $field => $value) {
      $select->condition($field, $value);
    }
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

  
  public function advancedLoad() {
    // Get a select query for our dbtng_example table. We supply an alias of e
    // (for 'example').
    $select = $this->connection->select('hello_world', 'e');
    // Join the users table, so we can get the entry creator's username.
    // Select these specific fields for the output.
    $select->addField('e', 'task');

    // Filter only persons named "John".
    $select->condition('e.task', 'Running');
    $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);

    return $entries;
  }

}
