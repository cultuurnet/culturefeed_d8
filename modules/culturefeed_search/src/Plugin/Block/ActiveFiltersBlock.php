<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\culturefeed_search\Event\SearchPagePrepareActiveFiltersEvent;
use Drupal\culturefeed_search\SearchPageServiceInterface;
use Drupal\culturefeed_search\SearchPageServiceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a Culturefeed search active filters block.
 *
 * @Block(
 *   id = "culturefeed_search_active_filters_block",
 *   admin_label = @Translation("Active filters block"),
 *   category = @Translation("Culturefeed search"),
 * )
 */
class ActiveFiltersBlock extends SearchPageBlockBase implements ContainerFactoryPluginInterface {

  /**
   * The search page service.
   *
   * @var \Drupal\culturefeed_search\SearchPageServiceInterface
   */
  protected $searchPageService;

  /**
   * The request.
   *
   * @var null|\Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructs a new ActiveFiltersBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\culturefeed_search\SearchPageServiceInterface $searchPageService
   *   The search page service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    array $plugin_definition,
    SearchPageServiceManagerInterface $searchPageServiceManager,
    SearchPageServiceInterface $searchPageService,
    RequestStack $requestStack,
    EventDispatcherInterface $eventDispatcher
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $searchPageServiceManager);

    $this->searchPageService = $searchPageService;
    $this->request = $requestStack->getCurrentRequest();
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('culturefeed_search.search_page_service_manager'),
      isset($configuration['service']) ? $container->get($configuration['service']) : $container->get('culturefeed_agenda.search_page_service'),
      $container->get('request_stack'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $links = [];

    // Search term.
    if ($searchTerm = $this->request->query->get('q')) {
      $query = $this->request->query->all();
      unset($query['q']);

      $links[] = Link::createFromRoute($searchTerm, '<current>', [], ['query' => array_filter($query)]);
    }

    // Facets.
    $facets = $this->searchPageService->getFacets();
    $activeBuckets = [];

    if (!empty($facets)) {
      foreach ($facets as $facet) {
        $activeBuckets[$facet->getId()] = $facet->getActiveBuckets();
      }
    }

    foreach ($activeBuckets as $facetId => $buckets) {

      /** @var \Drupal\culturefeed_search\Facet\FacetBucket $bucket */
      foreach ($buckets as $bucket) {
        $query = $this->request->query->all();

        // Note: At the moment, only 1 bucket per facet is allowed.
        // Un-setting the entire facet is ok at this point.
        unset($query[$facetId]);

        $links[] = Link::createFromRoute($bucket->getLabel(), '<current>', [], ['query' => array_filter($query)]);
      }
    }

    $event = $this->eventDispatcher->dispatch(new SearchPagePrepareActiveFiltersEvent($links), SearchPagePrepareActiveFiltersEvent::PREPARE);

    $links = $event->getLinks();

    return $links ? [
      '#theme' => 'culturefeed_search_active_filters',
      '#links' => $links,
    ] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
