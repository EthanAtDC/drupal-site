<?php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;

class HelloWorldController extends ControllerBase {
    
    public function build() {
        $build['content'] = [
            '#type' => 'item',
            '#markup' => 'Hello World',
        ];
        return $build;
    }

    protected $repository;

    public static function create(ContainerInterface $container) {
        $controller = new static($container->get('hello_world.repository'));
        $controller->setStringTranslation($container->get('string_translation'));
        return $controller;
    }

    public function __construct(HelloWorldRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Render a list of entries in the database.
     */
    public function entryList() {
        $content = [];

        $content['message'] = [
        '#markup' => $this->t('Generate a list of all entries in the database. There is no filter in the query.'),
        ];

        $rows = [];
        $headers = [
        $this->t('Id'),
        $this->t('uid'),
        $this->t('Task'),
        ];

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

        return $content;
    }

    /**
     * Render a filtered list of entries in the database.
     */
    public function entryAdvancedList() {
        $content = [];

        $content['message'] = [
        '#markup' => $this->t('Database'),
        ];

        $headers = [
        $this->t('Id'),
        $this->t('Created by'),
        $this->t('Task'),
        ];

        $rows = [];

        $entries = $this->repository->advancedLoad();

        foreach ($entries as $entry) {
        // Sanitize each entry.
        $rows[] = array_map('Drupal\Component\Utility\Html::escape', $entry);
        }
        $content['table'] = [
        '#type' => 'table',
        '#header' => $headers,
        '#rows' => $rows,
        '#attributes' => ['id' => 'dbtng-example-advanced-list'],
        '#empty' => $this->t('No entries available.'),
        ];
        return $content;
    }


}

