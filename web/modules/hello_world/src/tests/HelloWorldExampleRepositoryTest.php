<?php

namespace Drupal\Tests\hellow_world\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Kernel testing of the DbtngExampleRepository service.
 *
 * @coversDefaultClass \Drupal\dbtng_example\DbtngExampleRepository
 *
 * @group dbtng_example
 * @group examples
 *
 * @ingroup dbtng_example
 */
class HelloWorldRepositoryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['hello_world'];

  /**
   * {@inheritdoc}
   *
   * Kernel tests do not invoke hook_schema() or hook_install(). Therefore we
   * have to do it if our tests expect them to have been run.
   */
  protected function setUp() {
    parent::setUp();
    // Install the schema we defined in hook_schema().
    $this->installSchema('hello_world', 'hello_world');
    // Inovke hook_install().
    $this->container->get('module_handler')->invoke('hello_world', 'install');
  }

  /**
   * Tests several combinations, adding entries, updating and deleting.
   */
  public function testDbtngExampleStorage() {
    /* @var $repository \Drupal\dbtng_example\DbtngExampleRepository */
    $repository = $this->container->get('hello_world.repository');
    // Create a new entry.
    $entry = [
      'task' => 'Go for a run',
    ];
    $repository->insert($entry);

    // Save another entry.
    $entry = [
      'task' => 'Go for a hike',
    ];
    $repository->insert($entry);

    // Verify that 4 records are found in the database.
    $result = $repository->load();
    $this->assertCount(2, $result);

   
  }

}
