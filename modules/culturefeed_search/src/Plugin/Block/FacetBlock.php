<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\culturefeed_search\FacetHelper;
use Drupal\culturefeed_search\SearchPageServiceInterface;
use Drupal\culturefeed_search\SearchPageServiceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Culturefeed search facet block.
 *
 * @Block(
 *   id = "culturefeed_search_facet_block",
 *   admin_label = @Translation("Facet block"),
 *   category = @Translation("Culturefeed search"),
 *   deriver = "Drupal\culturefeed_search\Plugin\Derivative\FacetBlock"
 * )
 */
class FacetBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The search page service.
   *
   * @var \Drupal\culturefeed_search\SearchPageServiceInterface
   */
  protected $searchPageService;

  /**
   * The facet helper service.
   *
   * @var \Drupal\culturefeed_search\FacetHelper
   */
  protected $facetHelper;

  /**
   * The search page service manager.
   *
   * @var \Drupal\culturefeed_search\SearchPageServiceManagerInterface
   */
  protected $searchPageServiceManager;

  /**
   * Constructs a new FacetBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\culturefeed_search\SearchPageServiceManagerInterface $searchPageServiceManager
   *   The search page service manager.
   * @param \Drupal\culturefeed_search\SearchPageServiceInterface $searchPageService
   *   The search page service.
   * @param \Drupal\culturefeed_search\FacetHelper $facetHelper
   *   The facet helper service.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, SearchPageServiceManagerInterface $searchPageServiceManager, SearchPageServiceInterface $searchPageService, FacetHelper $facetHelper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->searchPageService = $searchPageService;
    $this->facetHelper = $facetHelper;
    $this->searchPageServiceManager = $searchPageServiceManager;
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
      $container->get($configuration['service'] ?? 'culturefeed_agenda.search_page_service'),
      $container->get('culturefeed_search.facet_helper')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'service' => 'culturefeed_agenda.search_page_service',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $config = $this->getConfiguration();

    // Settings.
    $minDepth = $config['min_depth'] ?? 1;
    $maxDepth = !empty($config['max_depth']) ? $config['max_depth'] : NULL;

    /** @var \Drupal\culturefeed_search\Facet\Facet $facet */
    $facet = $this->searchPageService->getFacets($this->getDerivativeId());

    if (!empty($facet) && !empty($facet->getBuckets())) {
      // Build the facet.
      $build[] = [
        '#theme' => 'culturefeed_search_facet',
        '#facet' => $this->facetHelper->limitFacet($facet, $minDepth, $maxDepth),
      ];
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['min_depth'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum depth'),
      '#description' => $this->t('The starting depth of the facets.'),
      '#default_value' => $config['min_depth'] ?? 1,
      '#min' => 1,
    ];

    $form['max_depth'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum depth'),
      '#description' => $this->t('The maximum depth of the facets, relative to the starting depth. Use 0 for unlimited.'),
      '#default_value' => $config['max_depth'] ?? 0,
      '#min' => 0,
    ];

    $pageIds = array_keys($this->searchPageServiceManager->getSearchPages());

    $form['service'] = [
      '#type' => 'select',
      '#title' => $this->t('Search page to inject'),
      '#options' => array_combine($pageIds, $pageIds),
      '#default_value' => $config['service'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['min_depth'] = $form_state->getValue('min_depth');
    $this->configuration['max_depth'] = $form_state->getValue('max_depth');
    $this->configuration['service'] = $form_state->getValue('service');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

}
