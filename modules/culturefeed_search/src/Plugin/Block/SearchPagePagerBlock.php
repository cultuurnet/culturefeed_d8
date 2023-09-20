<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\culturefeed_search\SearchPageServiceInterface;
use Drupal\culturefeed_search\SearchPageServiceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'SearchPagePagerBlock' block.
 *
 * @Block(
 *  id = "culturefeed_search_search_page_pager",
 *  admin_label = @Translation("Search page pager")
 * )
 */
class SearchPagePagerBlock extends SearchPageBlockBase {

  /**
   * Maximum number of pager links to show.
   */
  const PAGER_MAX_LINKS = 5;

  /**
   * The pager manager.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * Construct the search page pager block.
   *
   * @param array $configuration
   *   Block configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\culturefeed_search\SearchPageServiceInterface $searchPageService
   *   The search page service.
   * @param \Drupal\Core\Pager\PagerManagerInterface $pagerManager
   *   The pager manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PagerManagerInterface $pagerManager, SearchPageServiceManagerInterface $searchPageServiceManager, SearchPageServiceInterface $searchPageService = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $searchPageServiceManager, $searchPageService);
    $this->pagerManager = $pagerManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('pager.manager'),
      $container->get('culturefeed_search.search_page_service_manager'),
      isset($configuration['service']) ? $container->get($configuration['service']) : NULL,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Initialize the pager.
    $this->pagerManager->createPager($this->searchPageService->getTotalResults(), $this->searchPageService->getItemsPerPage());

    return [
      '#theme' => 'culturefeed_search_search_pager',
      '#pager' => [
        '#type' => 'pager',
        '#quantity' => self::PAGER_MAX_LINKS,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
