<?php

namespace Drupal\culturefeed_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\culturefeed_search\SearchPageServiceInterface;
use Drupal\culturefeed_search\SearchPageServiceManager;
use Drupal\culturefeed_search\SearchPageServiceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for Culturefeed organizer search page blocks.
 */
abstract class SearchPageBlockBase extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The search page service.
   *
   * @var \Drupal\culturefeed_search\SearchPageServiceInterface
   */
  protected $searchPageService;

  /**
   * The search page service manager.
   *
   * @var \Drupal\culturefeed_search\SearchPageServiceManagerInterface
   */
  protected $searchPageServiceManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('culturefeed_search.search_page_service_manager'),
      isset($configuration['service']) ? $container->get($configuration['service']) : NULL,
    );
  }

  /**
   * SearchPageBlockBase constructor.
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
   * @param \Drupal\culturefeed_search\SearchPageServiceManagerInterface $searchPageServiceManager
   *   The search page service manager.
   * @param \Drupal\culturefeed_search\SearchPageServiceInterface|null $searchPageService
   *   The search page service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SearchPageServiceManagerInterface $searchPageServiceManager, SearchPageServiceInterface $searchPageService = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->searchPageServiceManager = $searchPageServiceManager;
    $this->searchPageService = $searchPageService;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(
      [
        'culturefeed_search_api',
        'culturefeed_search_page',
      ],
      parent::getCacheTags()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $pageIds = array_keys($this->searchPageServiceManager->getSearchPages());

    $form['service'] = [
      '#type' => 'select',
      '#title' => $this->t('Search page to inject'),
      '#options' => array_combine($pageIds, $pageIds),
      '#default_value' => $config['service'] ?? '',
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['service'] = $values['service'];
  }

}
