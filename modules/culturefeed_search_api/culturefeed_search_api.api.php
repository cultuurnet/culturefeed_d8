<?php

/**
 * @file
 * Describes hooks provided by the Culturefeed search API module.
 */

use CultuurNet\SearchV3\SearchQueryInterface;
use CultuurNet\SearchV3\Parameter\AudienceType;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter a Culturefeed search query before it is executed.
 *
 * @param \CultuurNet\SearchV3\SearchQueryInterface $searchQuery
 *   The search query to alter.
 * @param string $type
 *   The type of query that is executed. Can be one of the following:
 *   - events
 *   - event
 *   - places
 *   - offers.
 *
 * @see \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClient::searchEvents()
 * @see \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClient::searchEvent()
 * @see \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClient::searchPlaces()
 * @see \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClient::searchOffers()
 */
function hook_culturefeed_search_api_query_alter(SearchQueryInterface $searchQuery, $type = 'events') {
  if ($type == 'events') {
    $searchQuery->addParameter(new AudienceType('*'));
  }
}

/**
 * @} End of "addtogroup hooks".
 */
