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
   * The search page query.
   *
   * @var \CultuurNet\SearchV3\SearchQueryInterface
   */
  protected $query;

  /**
   * CulturefeedSearchPagePrepareFacetsEvent constructor.
   *
   * @param \CultuurNet\SearchV3\SearchQueryInterface $query
   *   The search query.
   */
  public function __construct(SearchQueryInterface $query) {
    $this->query = $query;
  }

  /**
   * Get the prepared query.
   *
   * @return \CultuurNet\SearchV3\SearchQueryInterface
   *   The search query.
   */
  public function getQuery() {
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
