<?php

namespace Drupal\changelog_display\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChangelogDisplayController.
 */
class ChangelogDisplayController extends ControllerBase {

  /**
   * Changelog.
   *
   * @return array
   *  Return Changelog string.
   */
  public function changelog() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('This is the changelog'),
    ];
  }

  /**
   * Webhook Controller.
   *
   * @return array
   *  Returns a success response when received
   */
  public function changelogUpdate(Request $request) {


    return [
      '#type' => 'markup',
      '#markup' => $this->t('hello'),
    ];
  }

}
