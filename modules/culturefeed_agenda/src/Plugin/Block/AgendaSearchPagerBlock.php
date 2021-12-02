<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Cache\Cache;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'AgendaSearchPagerBlock' block.
 *
 * @Block(
 *  id = "culturefeed_agenda_search_page_pager",
 *  admin_label = @Translation("Agenda search page pager")
 * )
 */
class AgendaSearchPagerBlock extends AgendaSearchPageBlockBase {

  /**
   * Maximum number of pager links to show.
   */
  const PAGER_MAX_LINKS = 5;

  /**
   * The Pager manager service.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('pager.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Initialize the pager.
    $this->pagerManager->createPager($this->searchPageService->getTotalResults(), $this->searchPageService->getItemsPerPage());

    return [
      '#theme' => 'culturefeed_agenda_search_pager',
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
