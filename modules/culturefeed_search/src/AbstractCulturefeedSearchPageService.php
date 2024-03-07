<?php

namespace Drupal\culturefeed_search;

use CultuurNet\SearchV3\Parameter\Facet;
use CultuurNet\SearchV3\Parameter\Query;
use CultuurNet\SearchV3\Parameter\Regions;
use CultuurNet\SearchV3\Parameter\TermIds;
use CultuurNet\SearchV3\SearchQuery;
use CultuurNet\SearchV3\SearchQueryInterface;
use CultuurNet\SearchV3\ValueObjects\PagedCollection;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\culturefeed_search\Event\SearchPagePrepareFacetsEvent;
use Drupal\culturefeed_search\Event\SearchPageServiceExecuteEvent;
use Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Abstract class for a central Culturefeed search page service.
 *
 * The search page service is an intermediate service
 * for performing a search and requesting details about the performed search.
 * It ensures all blocks requesting details about a search
 * get the same fully loaded search information.
 */
abstract class AbstractCulturefeedSearchPageService implements SearchPageServiceInterface {

  use StringTranslationTrait;

  /**
   * The Culturefeed search client.
   *
   * @var \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface
   */
  protected $searchClient;

  /**
   * Current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * The facet helper service.
   *
   * @var \Drupal\culturefeed_search\FacetHelper
   */
  protected $facetHelper;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcher
   */
  protected $eventDispatcher;

  /**
   * The Pager manager service.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * The search query.
   *
   * @var \CultuurNet\SearchV3\SearchQueryInterface
   */
  protected $searchQuery;

  /**
   * The search result.
   *
   * @var \CultuurNet\SearchV3\ValueObjects\PagedCollection
   */
  protected $searchResult;

  /**
   * The number of items per page.
   *
   * @var int
   */
  protected $itemsPerPage = 20;

  /**
   * Indicates if a search has been performed.
   *
   * @var bool
   */
  protected $searched = FALSE;

  /**
   * Indicates if the search failed.
   *
   * @var bool
   */
  protected $searchFailed = FALSE;

  /**
   * The facets.
   *
   * @var null
   */
  protected $facets = NULL;

  /**
   * AbstractCulturefeedSearchPageService constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The current request stack.
   * @param \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface $searchClient
   *   The Culturefeed search client.
   * @param \Drupal\culturefeed_search\FacetHelper $facetHelper
   *   Facet helper service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Pager\PagerManagerInterface $pager_manager
   *   The Pager manager service.
   */
  public function __construct(RequestStack $requestStack, DrupalCulturefeedSearchClientInterface $searchClient, FacetHelper $facetHelper, EventDispatcherInterface $eventDispatcher, PagerManagerInterface $pager_manager) {
    $this->currentRequest = $requestStack->getCurrentRequest();
    $this->searchClient = $searchClient;
    $this->facetHelper = $facetHelper;
    $this->eventDispatcher = $eventDispatcher;
    $this->pagerManager = $pager_manager;

    // Initialize with an empty search query and search result.
    $this->searchQuery = new SearchQuery(TRUE);
    $this->searchResult = new PagedCollection();
  }

  /**
   * Check if a search has been executed.
   *
   * @return bool
   *   TRUE if the search was executed.
   */
  protected function hasSearched(): bool {
    return $this->searched;
  }

  /**
   * Change the search flag.
   *
   * @param bool $searched
   *   TRUE to indicate a search was executed.
   */
  protected function setSearched(bool $searched = TRUE) {
    $this->searched = $searched;
  }

  /**
   * Execute the search and set the search result.
   */
  protected function execute() {
    try {
      // Build the search query.
      $this->searchQuery->setStart($this->pagerManager->findPage() * $this->itemsPerPage);
      $this->searchQuery->setLimit($this->itemsPerPage);

      // Set API search parameters according to query parameters.
      $this->setSearchParameters($this->currentRequest->query->all());

      // Allow others to alter the query before execution.
      $this->eventDispatcher->dispatch(new SearchPageServiceExecuteEvent($this->searchQuery), SearchPageServiceExecuteEvent::EXECUTE);

      // Add hard-coded facet types.
      $this->addFacets();

      // Execute the query.
      $this->executeQuery();
    }
    catch (\Exception $e) {
      $this->markAsFailed();
    }

    // Set the "searched" flag.
    $this->setSearched();
  }

  /**
   * Execute the search query.
   */
  protected function executeQuery() {}

  /**
   * Perform a search if no search has been done yet.
   */
  protected function search() {
    if (!$this->hasSearched()) {
      $this->execute();
    }
  }

  /**
   * Set API search parameters according to query parameters.
   *
   * @param array $params
   *   The search params.
   */
  protected function setSearchParameters(array $params) {
    // Set the search query parameters.
    if (!empty($params['q'])) {
      $this->searchQuery->addParameter(new Query($params['q']));
    }

    if (!empty($params['organiser'])) {
      // OrganizerId parameter not working at time of writing.
      $this->searchQuery->addParameter(new Query('organizer.id:' . $params['organiser']));
    }

    // Parameter mapping.
    $parameters = [
      'regions' => Regions::class,
      'types' => TermIds::class,
      'facilities' => TermIds::class,
      'themes' => TermIds::class,
    ];

    foreach ($parameters as $id => $class) {
      // Add the region parameters.
      $parameterValues = $this->currentRequest->query->all()[$id] ?? NULL;
      if ($parameterValues !== NULL) {
        if (!is_array($parameterValues)) {
          $parameterValues = [$parameterValues => $parameterValues];
        }

        foreach ($parameterValues as $parameterValue => $parameterLabel) {
          $this->searchQuery->addParameter(new $class($parameterValue));
        }
      }
    }
  }

  /**
   * Add the needed facets to the search query.
   */
  protected function addFacets() {
    $this->searchQuery->addParameter(new Facet('regions'));
    $this->searchQuery->addParameter(new Facet('types'));
    $this->searchQuery->addParameter(new Facet('facilities'));
    $this->searchQuery->addParameter(new Facet('themes'));
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchResultItems() {
    $this->search();
    return !empty($this->searchResult->getMember()) ? $this->searchResult->getMember()->getItems() ?? [] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchResult() {
    $this->search();
    return $this->searchResult;
  }

  /**
   * {@inheritdoc}
   */
  public function getTotalResults() {
    $this->search();
    return $this->searchResult->getTotalItems();
  }

  /**
   * {@inheritdoc}
   */
  public function getItemsPerPage(): int {
    return $this->itemsPerPage;
  }

  /**
   * {@inheritdoc}
   */
  public function markAsFailed() {
    $this->searchFailed = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function searchHasFailed(): bool {
    return $this->searchFailed;
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchQuery(): SearchQueryInterface {
    return $this->searchQuery;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentPage(): int {
    return (int) $this->currentRequest->query->get('page', 0);
  }

  /**
   * {@inheritdoc}
   */
  public function getFacets($facetId = NULL) {
    if (!$this->facets) {
      // Perform a search.
      $this->search();

      // Parse the resulting facets into generic facets and buckets.
      $this->facets = !empty($this->searchResult->getFacets()) ? $this->facetHelper->buildFacetsFromResult($this->searchResult->getFacets()) : [];

      // Set the active buckets.
      /** @var \Drupal\culturefeed_search\Facet\Facet $facet */
      foreach ($this->facets as $facet) {
        $queryParams = $this->currentRequest->query->all()[$facet->getId()] ?? [];

        // We accept both arrays and plain values.
        if (!is_array($queryParams)) {
          $queryParams = [$queryParams => $queryParams];
        }

        $facet->setActiveBuckets($queryParams);
      }

      // Allow other modules/scripts to react to the facets being prepared.
      /** @var \Drupal\culturefeed_search\Event\SearchPagePrepareFacetsEvent $event */
      $event = $this->eventDispatcher->dispatch(new SearchPagePrepareFacetsEvent($this->facets), SearchPagePrepareFacetsEvent::PREPARE);
      $this->facets = $event->getFacets();
    }

    return !empty($facetId) ? $this->facets[$facetId] ?? NULL : $this->facets;
  }

}
