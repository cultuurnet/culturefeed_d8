<?php

namespace Drupal\culturefeed_organizers\Plugin\Block;

use CultuurNet\SearchV3\Parameter\OrganizerId;
use CultuurNet\SearchV3\SearchQuery;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display the events organized by an organizer.
 *
 * @Block(
 *  id = "events_of_organizer",
 *  admin_label = @Translation("Events of organizer"),
 *  context_definitions = {
 *    "culturefeed_organizer" = @ContextDefinition("culturefeed_organizer", required = TRUE)
 *  }
 * )
 */
class EventsOfOrganizerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The search client.
   *
   * @var \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface
   */
  protected $searchClient;

  /**
   * Construct a new EventsOfOrganizerBlock.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface $searchClient
   *   The search client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, DrupalCulturefeedSearchClientInterface $searchClient) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->searchClient = $searchClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('culturefeed_search_api.client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'total_items' => 20,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();
    $form['total_items'] = [
      '#type' => 'number',
      '#title' => $this->t('Total items to display'),
      '#default_value' => $config['total_items'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('total_items', $form_state->getValue('total_items'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    try {
      $config = $this->getConfiguration();
      $query = new SearchQuery();
      $query->addParameter(new OrganizerId($this->getContextValue('culturefeed_organizer')->getCdbid()));
      $query->setLimit($config['total_items']);

      $searchResult = $this->searchClient->searchEvents($query);
      if ($searchResult->getTotalItems() === 0) {
        return [];
      }

      return [
        '#theme' => 'culturefeed_organizers_organizer_events',
        '#result' => $searchResult,
      ];

    }
    catch (\Exception $e) {
      return [
        '#cache' => [
          'max-age' => 0,
        ],
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['culturefeed_search_api']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

}
