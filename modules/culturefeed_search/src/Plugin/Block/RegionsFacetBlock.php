<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Form\FormBuilderInterface;
use Drupal\culturefeed_search\FacetHelper;
use Drupal\culturefeed_search\Form\RegionsFacetFilterForm;
use Drupal\culturefeed_search\SearchPageServiceInterface;
use Drupal\culturefeed_search\SearchPageServiceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Culturefeed regions search facet block.
 *
 * @Block(
 *   id = "culturefeed_search_regions_facet_block",
 *   admin_label = @Translation("Facet block: Regions"),
 *   category = @Translation("Culturefeed search"),
 * )
 */
class RegionsFacetBlock extends FacetBlock {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs a new RegionsFacetBlock.
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
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The form builder.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, SearchPageServiceManagerInterface $searchPageServiceManager, SearchPageServiceInterface $searchPageService, FacetHelper $facetHelper, FormBuilderInterface $formBuilder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $searchPageServiceManager, $searchPageService, $facetHelper);

    $this->formBuilder = $formBuilder;
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
      $container->get('culturefeed_search.facet_helper'),
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();

    if (!empty($build)) {
      // Add the regions autocomplete form.
      $build[] = $this->formBuilder->getForm(RegionsFacetFilterForm::class);
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeId() {
    return 'regions';
  }

}
