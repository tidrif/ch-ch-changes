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
    $parsedown = new \Parsedown();
    $changelog = \Drupal::getContainer()
      ->get('config.factory')
      ->getEditable('changelog_display.settings')
      ->get('changelog_contents');

    // Flag that user viewed the updated changelog.
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $flag_id = 'changelog_viewed';
    /** @var \Drupal\flag\FlagServiceInterface $flag_service */
    $flag_service = \Drupal::service('flag');
    $flag = $flag_service->getFlagById($flag_id);
    if (!$flag_service->getFlagging($flag, $user)) {
      $flag_service->flag($flag, $user);
    }

    return [
      '#type' => 'markup',
      '#markup' => $parsedown->text($changelog),
    ];
  }

  /**
   * Webhook Controller.
   *
   * @return array
   *  Returns a success response when received
   */
  public function changelogUpdate(Request $request) {
    $changelog = \Drupal::getContainer()->get('config.factory')->getEditable('changelog_display.settings')
      ->get('changelog_contents');
    $new_changelog = file_get_contents('https://raw.githubusercontent.com/tidrif/ch-ch-changes/master/CHANGELOG.md');
    if (!empty($new_changelog) && ($changelog !== $new_changelog)) {
      \Drupal::getContainer()->get('config.factory')->getEditable('changelog_display.settings')
        ->set('changelog_contents', $new_changelog)
        ->save();

      // Unflag all changelog_viewed flags.
      $flag_id = 'changelog_viewed';
      /** @var \Drupal\flag\FlagServiceInterface $flag_service */
      $flag_service = \Drupal::service($flag_id);
      $flag = $flag_service->getFlagById($flag_id);
      $flag_service->unflagAllByFlag($flag);

      return [
        '#type' => 'markup',
        '#markup' => $this->t('Changelog updated!'),
      ];
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Changelog not updated'),
    ];

  }

}
