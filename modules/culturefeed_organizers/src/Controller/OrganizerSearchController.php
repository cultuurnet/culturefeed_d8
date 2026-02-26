<?php

namespace Drupal\culturefeed_organizers\Controller;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a route controller for the organizer search page.
 */
class OrganizerSearchController extends ControllerBase {

  /**
   * AgendaSearchController constructor.
   *
   * @param \Drupal\Core\Block\BlockManagerInterface $blockManager
   *   The block manager.
   */
  public function __construct(protected readonly BlockManagerInterface $blockManager) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.block')
    );
  }

  /**
   * Organizer search page.
   *
   * @return array
   *   The response array.
   */
  public function searchPage(): array {
    return [
      '#theme' => 'culturefeed_organizers_search_page',
      '#search_form' => $this->buildBlockPlugin('culturefeed_organizer_search_form'),
      '#active_filters' => $this->buildBlockPlugin('culturefeed_search_active_filters_block'),
      '#results' => $this->buildBlockPlugin('culturefeed_search_search_page_results'),
      '#pager' => $this->buildBlockPlugin('culturefeed_search_search_page_pager'),
      '#result_count' => $this->buildBlockPlugin('culturefeed_search_search_result_count'),
      '#result_count_summary' => $this->buildBlockPlugin('culturefeed_search_search_result_count_summary'),
    ];
  }

  /**
   * Build a block by using the plugin Id.
   *
   * @param string $pluginId
   *   The block plugin Id.
   *
   * @return array
   *   The render array.
   */
  private function buildBlockPlugin($pluginId): array {
    $build = [];

    $blockPlugin = $this->blockManager->createInstance($pluginId, [
      'service' => 'culturefeed_organizer.search_page_service',
    ]);
    if ($blockPlugin instanceof BlockPluginInterface && $blockPlugin->access($this->currentUser())) {
      $build = [
        '#cache' => [
          'keys' => [$pluginId],
          'contexts' => $blockPlugin->getCacheContexts(),
          'tags' => Cache::mergeTags(['block_view'], $blockPlugin->getCacheTags()),
          'max-age' => $blockPlugin->getCacheMaxAge(),
        ],
      ] + $blockPlugin->build();
    }

    return $build;
  }

}
