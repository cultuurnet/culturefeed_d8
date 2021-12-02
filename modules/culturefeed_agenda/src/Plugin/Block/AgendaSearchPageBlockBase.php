<?php

namespace Drupal\culturefeed_agenda\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\culturefeed_search\SearchPageServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for Culturefeed agenda search page blocks.
 */
abstract class AgendaSearchPageBlockBase extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The agenda search page service.
   *
   * @var \Drupal\culturefeed_search\SearchPageServiceInterface
   */
  protected $searchPageService;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('culturefeed_agenda.search_page_service'),
      $container->get('form_builder')
    );
  }

  /**
   * AgendaSearchPageBlockBase constructor.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\culturefeed_search\SearchPageServiceInterface $searchPageService
   *   The search page service.
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The form builder.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SearchPageServiceInterface $searchPageService, FormBuilderInterface $formBuilder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->searchPageService = $searchPageService;
    $this->formBuilder = $formBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return ['#markup' => 'hello wereld'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(
      [
        'culturefeed_search_api',
        'culturefeed_agenda_search',
      ],
      parent::getCacheTags()
    );
  }

}
