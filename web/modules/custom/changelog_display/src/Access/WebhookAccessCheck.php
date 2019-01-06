<?php

namespace Drupal\changelog_display\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Checks access for displaying configuration translation page.
 */
class WebhookAccessCheck implements AccessInterface{

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for the request and this account.
   */
  public function access(Request $request, AccountInterface $account) {

    if ($request->isMethod('post')) {
      $payload = $request->getContent();
      if (empty($payload)) {
        return AccessResult::forbidden();
      }
      else {
        $webhookSecret = $this->getWebhookSecret();
        $hubSignature = $request->headers->get('x-hub-signature');
        list($algo, $hash) = explode('=', $hubSignature, 2);
        $payloadHash = hash_hmac($algo, $payload, $webhookSecret);

        if ($hash === $payloadHash) {
          return AccessResult::allowed();
        }
      }
    }
    elseif ($account->hasPermission('update changelog from github')) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();

  }

  /**
   * Retrieve the webhook secret from configuration.
   */
  private function getWebhookSecret() {
    return $this->webhookSecret = \Drupal::service('config.factory')->get('changelogDisplayWebhookSecret')->get('token');
  }

}
