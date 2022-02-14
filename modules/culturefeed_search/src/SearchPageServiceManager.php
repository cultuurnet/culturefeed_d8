<?php

namespace Drupal\culturefeed_search;

/**
 * Provides a SearchPageServiceManager to manage the known search page services.
 */
class SearchPageServiceManager implements SearchPageServiceManagerInterface {

  /**
   * List of known search page services.
   *
   * @var array
   */
  protected $searchPageServices;

  /**
   * {@inheritdoc}
   */
  public function addSearchPage(SearchPageServiceInterface $searchPageService, $priority = 0): SearchPageServiceManagerInterface {
    $serviceId = $searchPageService->_serviceId;
    $this->searchPageServices[$serviceId] = $searchPageService;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchPages(): array {
    return $this->searchPageServices;
  }

}
