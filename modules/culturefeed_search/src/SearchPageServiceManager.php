<?php

namespace Drupal\culturefeed_search;

use Drupal\Component\DependencyInjection\ReverseContainer;
use Drupal\Core\DrupalKernelInterface;

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
   * The ReverseContainer service.
   *
   * @var ReverseContainer
   */
  protected ReverseContainer $reverseContainer;

  public function __construct(ReverseContainer $reverseContainer) {
    $this->reverseContainer = $reverseContainer;
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
