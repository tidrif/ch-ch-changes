<?php

namespace Drupal\changelog_display\Controller;

use Drupal\Core\Controller\ControllerBase;

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

}
