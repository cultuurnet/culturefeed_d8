<?php

namespace Drupal\culturefeed_search_api;

use CultuurNet\SearchV3\SearchClientInterface;

/**
 * Provides an interface for Culturefeed clients that implement extended functionality.
 */
interface DrupalCulturefeedSearchClientInterface extends SearchClientInterface {

  /**
   * Search for a single event.
   *
   * @param string $eventId
   *   The event Id to search for.
   * @param bool $reset
   *   Indicates if the cache should be reset.
   *
   * @return \CultuurNet\SearchV3\ValueObjects\Event|null
   *   The event or null.
   */
  public function searchEvent(string $eventId, bool $reset = FALSE);

}
