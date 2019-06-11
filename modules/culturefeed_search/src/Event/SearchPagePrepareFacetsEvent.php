<?php

namespace Drupal\culturefeed_search\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event thrown when the facets are being prepared for a Culturfeed search page.
 *
 * This event allows other modules to alter the facets when they
 * are being prepared by the search page service.
 */
class SearchPagePrepareFacetsEvent extends Event {

  const PREPARE = 'culturefeed_search_page.prepare_facets';

  /**
   * The search page facets.
   *
   * @var \Drupal\culturefeed_search\Facet\Facet[]
   */
  protected $facets = [];

  /**
   * CulturefeedSearchPagePrepareFacetsEvent constructor.
   *
   * @param array $facets
   *   Collection of facets being prepared.
   */
  public function __construct(array $facets) {
    $this->facets = $facets;
  }

  /**
   * Get the prepared facets.
   *
   * @return \Drupal\culturefeed_search\Facet\Facet[]
   *   The prepared facets.
   */
  public function getFacets() {
    return $this->facets;
  }

  /**
   * Set the prepared facets.
   *
   * @param array $facets
   *   The facets to set.
   *
   * @return $this
   */
  public function setFacets(array $facets) {
    $this->facets = $facets;
    return $this;
  }

}
