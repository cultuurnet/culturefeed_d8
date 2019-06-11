<?php

namespace Drupal\culturefeed_content\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'culturefeed_content_field_type' field type.
 *
 * @FieldType(
 *   id = "culturefeed_content",
 *   label = @Translation("Selection of search results"),
 *   description = @Translation("Culturefeed content"),
 *   category = @Translation("Culturefeed"),
 *   default_widget = "culturefeed_content_default",
 *   default_formatter = "culturefeed_content_default"
 * )
 */
class CultureFeedContentFieldType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 255,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['filter_query'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Filter query'));
    $properties['query_string'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Query'));
    $properties['rows'] = DataDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Query'));
    $properties['sort'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Sort'));
    $properties['sort_direction'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Sort direction'));
    $properties['title'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Title'));
    $properties['show_more_link'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Show more link'));
    $properties['more_link'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('More link'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'filter_query' => [
          'type' => 'text',
          'size' => 'medium',
        ],
        'query_string' => [
          'type' => 'text',
          'size' => 'medium',
        ],
        'rows' => [
          'type' => 'int',
          'size' => 'medium',
        ],
        'sort' => [
          'type' => 'text',
          'size' => 'medium',
        ],
        'sort_direction' => [
          'type' => 'text',
          'size' => 'small',
        ],
        'title' => [
          'type' => 'text',
          'size' => 'medium',
        ],
        'show_more_link' => [
          'type' => 'int',
          'default' => 1,
        ],
        'more_link' => [
          'type' => 'text',
          'size' => 'medium',
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return FALSE;
  }

}
