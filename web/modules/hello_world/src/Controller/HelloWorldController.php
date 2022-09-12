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
}


// return array(
//             '#title' => 'hello world',
//             '#markup' => 'Some content here.'
//         );