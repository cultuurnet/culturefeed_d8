<?php

namespace Drupal\culturefeed_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'culturefeed_content_field_type' field type.
 *
 * @FieldFormatter(
 *   id = "culturefeed_content_default",
 *   label = @Translation("Default CultureFeed content formatter"),
 *   field_types = {
 *     "culturefeed_content"
 *   }
 * )
 */
class CultureFeedContentDefaultFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Culturefeed search client.
   *
   * @var \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface
   */
  protected $searchClient;

  /**
   * Constructs an ImageFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\culturefeed_search_api\DrupalCulturefeedSearchClientInterface $searchClient
   *   The Culturefeed search client.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, DrupalCulturefeedSearchClientInterface $searchClient) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->searchClient = $searchClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('culturefeed_search_api.client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'view_mode' => 'teaser',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['view_mode'] = [
      '#title' => $this->t('Item view mode'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('view_mode'),
      '#description' => $this->t('Enter the desired output view mode for the search items'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return ['#markup' => $this->t('View mode: %view_mode', ['%view_mode' => $this->getSetting('view_mode')])];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    /** @var \Drupal\culturefeed_content\Plugin\Field\FieldType\CultureFeedContentFieldType $item */
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#lazy_builder' => [
          'culturefeed_content.field_lazy_builder:buildCulturefeedContent', [
            $item->get('title')->getValue() ?? '',
            $item->get('query_string')->getValue() ?? '',
            $this->getSetting('view_mode'),
            $item->get('rows')->getValue(),
            $item->get('sort')->getValue(),
            $item->get('sort_direction')->getValue() ?? 'desc',
            $item->get('show_more_link')->getValue() ?? TRUE,
            $item->get('more_link')->getValue() ?? '',
          ],
        ],
        '#create_placeholder' => TRUE,
      ];
    }

    return $elements;
  }

}
