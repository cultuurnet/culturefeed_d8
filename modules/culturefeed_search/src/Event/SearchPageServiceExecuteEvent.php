<?php

namespace Drupal\culturefeed_search\Event;

use CultuurNet\SearchV3\SearchQueryInterface;
use Drupal\Component\EventDispatcher\Event;

/**
 * Event thrown when the search page is executed.
 *
 * This event allows other modules to alter the query that
 * is being prepared by the search page service.
 */
class SearchPageServiceExecuteEvent extends Event {

  const EXECUTE = 'culturefeed_search_page.execute';

  /**
   * CulturefeedSearchPagePrepareFacetsEvent constructor.
   *
   * @param \CultuurNet\SearchV3\SearchQueryInterface $query
   *   The search query.
   */
  public function __construct(protected SearchQueryInterface $query) {
  }

  /**
   * Get the prepared query.
   *
   * @return \CultuurNet\SearchV3\SearchQueryInterface
   *   The search query.
   */
  public function getQuery(): SearchQueryInterface {
    return $this->query;
  }

  /**
   * Set the prepared query.
   *
   * @param \CultuurNet\SearchV3\SearchQueryInterface $query
   *   The query.
   *
   * @return $this
   */
  public function setQuery(SearchQueryInterface $query) {
    $this->query = $query;
    return $this;
  }

}
