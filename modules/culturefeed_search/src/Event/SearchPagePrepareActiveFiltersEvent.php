<?php

namespace Drupal\culturefeed_search\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event thrown when the before the active filters are rendered.
 *
 * Here other modules can add their active filter links.
 */
class SearchPagePrepareActiveFiltersEvent extends Event {

  const PREPARE = 'culturefeed_search_page.prepare_active_filters';

  /**
   * The links.
   *
   * @var array
   */
  protected $links;

  /**
   * SearchPagePrepareActiveFiltersEvent constructor.
   *
   * @param array $links
   *   The links.
   */
  public function __construct(array $links) {
    $this->links = $links;
  }

  /**
   * Get the prepared query.
   *
   * @return array
   *   The links.
   */
  public function getLinks() {
    return $this->links;
  }

  /**
   * Set the links query.
   *
   * @param array $links
   *   The links.
   *
   * @return $this
   */
  public function setLinks(array $links) {
    $this->links = $links;
    return $this;
  }

}
