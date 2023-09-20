<?php

namespace Drupal\culturefeed_search_api;

use CultuurNet\SearchV3\SearchClientInterface;

/**
 * Provides an interface for Culturefeed clients.
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

  /**
   * Search for a single organizer.
   *
   * @param string $organizerId
   *   The organizer Id to search for.
   * @param bool $reset
   *   Indicates if the cache should be reset.
   *
   * @return \CultuurNet\SearchV3\ValueObjects\Organizer|null
   *   The organizer or null.
   */
  public function searchOrganizer(string $organizerId, bool $reset = FALSE);

}
