<?php

namespace Drupal\culturefeed_search;

use Drupal\Component\DependencyInjection\ReverseContainer;

/**
 * Provides a SearchPageServiceManager to manage the known search page services.
 */
class SearchPageServiceManager implements SearchPageServiceManagerInterface {

  /**
   * List of known search page services.
   *
   * @var array
   */
  protected array $searchPageServices = [];

  /**
   * Constructs SearchPageServiceManager.
   *
   * @param \Drupal\Component\DependencyInjection\ReverseContainer $reverseContainer
   *   The reverse container.
   */
  public function __construct(protected readonly ReverseContainer $reverseContainer) {
  }

  /**
   * {@inheritdoc}
   */
  public function addSearchPage(SearchPageServiceInterface $searchPageService, $priority = 0): SearchPageServiceManagerInterface {
    $serviceId = $this->reverseContainer->getId($searchPageService);
    if (NULL !== $serviceId) {
      $this->searchPageServices[$serviceId] = $searchPageService;
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchPages(): array {
    return $this->searchPageServices;
  }

}
