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

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Construct a repository object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   *   The translation service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(Connection $connection, TranslationInterface $translation, MessengerInterface $messenger) {
    $this->connection = $connection;
    $this->setStringTranslation($translation);
    $this->setMessenger($messenger);
  }

  /**
   * Save an entry in the database.
   *
   * Exception handling is shown in this example. It could be simplified
   * without the try/catch blocks, but since an insert will throw an exception
   * and terminate your application if the exception is not handled, it is best
   * to employ try/catch.
   *
   * @param array $entry
   *   An array containing all the fields of the database record.
   *
   * @return int
   *   The number of updated rows.
   *
   * @throws \Exception
   *   When the database insert fails.
   */
  public function insert(array $entry) {
    try {
      $return_value = $this->connection->insert('dbtng_example')
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

  /**
   * Update an entry in the database.
   *
   * @param array $entry
   *   An array containing all the fields of the item to be updated.
   *
   * @return int
   *   The number of updated rows.
   */
  public function update(array $entry) {
    try {
      // Connection->update()...->execute() returns the number of rows updated.
      $count = $this->connection->update('hello_world')
        ->fields($entry)
        ->condition('pid', $entry['pid'])
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

  /**
   * Delete an entry from the database.
   *
   * @param array $entry
   *   An array containing at least the person identifier 'pid' element of the
   *   entry to delete.
   *
   * @see Drupal\Core\Database\Connection::delete()
   */
  public function delete(array $entry) {
    $this->connection->delete('hello_world')
      ->condition('pid', $entry['pid'])
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
    $select->join('users_field_data', 'u', 'e.uid = u.uid');
    // Select these specific fields for the output.
    $select->addField('e', 'pid');
    $select->addField('u', 'name', 'username');
    $select->addField('e', 'task');


    // Filter only persons named "John".
    $select->condition('e.task', 'Running');


    $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);

    return $entries;
  }

}
