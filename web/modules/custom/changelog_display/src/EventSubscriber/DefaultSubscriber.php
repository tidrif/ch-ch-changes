<?php

namespace Drupal\changelog_display\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DefaultSubscriber.
 */
class DefaultSubscriber implements EventSubscriberInterface {


  /**
   * Constructs a new DefaultSubscriber object.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['kernel.request'] = ['kernel_request'];

    return $events;
  }

  /**
   * This method is called whenever the kernel.request event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function kernel_request(Event $event) {
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());

    /** @var \Drupal\flag\FlagServiceInterface $flag_service */
    $flag_service = \Drupal::service('flag');

    $viewed_flag_id = 'changelog_viewed';
    $viewed_flag = $flag_service->getFlagById($viewed_flag_id);

    $dismissed_flag_id = 'changelog_dismissed';
    $dismissed_flag = $flag_service->getFlagById($dismissed_flag_id);

    if ($current_user->isAuthenticated()) {
      if (!$flag_service->getFlagging($viewed_flag, $user) && !$flag_service->getFlagging($dismissed_flag, $user)) {
        $messenger = \Drupal::messenger();
        $messenger->addMessage(t('The changelog is updated! View @here or @dismiss this message.', [
          '@here' => \Drupal::l(t('here'), \Drupal\Core\Url::fromRoute('changelog_display.changelog')),
          '@dismiss' => \Drupal::l(t('dismiss'), \Drupal\Core\Url::fromRoute('changelog_display.changelog_dismiss')),
        ]), $messenger::TYPE_STATUS);
      }
    }

  }

}
