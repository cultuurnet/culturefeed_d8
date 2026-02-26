<?php

namespace Drupal\culturefeed_search;

use CultuurNet\SearchV3\SearchQueryInterface;
use CultuurNet\SearchV3\ValueObjects\PagedCollection;
use Drupal\culturefeed_search\Facet\Facet;

/**
 * Defines an interface for Culturefeed search page services.
 */
interface SearchPageServiceInterface {

  /**
   * Get the number of items per page.
   *
   * @return int
   *   The number of items per page.
   */
  public function getItemsPerPage(): int;

  /**
   * Returns the search result items.
   *
   * @return array
   *   The search result items.
   */
  public function getSearchResultItems(): array;

  /**
   * Returns the search result object.
   *
   * @return \CultuurNet\SearchV3\ValueObjects\PagedCollection
   *   The current search result.
   */
  public function getSearchResult(): PagedCollection;

  /**
   * Get the total number of search results.
   *
   * @return int
   *   The total results.
   */
  public function getTotalResults(): int;

  /**
   * Mark the current search page as failed.
   */
  public function markAsFailed(): void;

  /**
   * Returns a boolean indicating if the search failed.
   *
   * @return bool
   *   TRUE if search failed.
   */
  public function searchHasFailed(): bool;

  /**
   * Retrieve the full SearchQuery object.
   *
   * @return \CultuurNet\SearchV3\SearchQueryInterface
   *   The current SearchQuery.
   */
  public function getSearchQuery(): SearchQueryInterface;

  /**
   * Get the current page.
   *
   * @return int
   *   The current page number.
   */
  public function getCurrentPage(): int;

  /**
   * Get an array of facets for the performed search.
   *
   * @param string|null $facetId
   *   Optional facet Id.
   *
   * @return null|\Drupal\culturefeed_search\Facet\Facet[]|Facet\
   *   Array of facets or a single facet.
   */
  public function getFacets($facetId = NULL): NULL|array|Facet;

}
