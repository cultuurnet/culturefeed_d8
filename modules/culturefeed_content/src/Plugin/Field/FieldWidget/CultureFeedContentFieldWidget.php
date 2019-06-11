<?php

namespace Drupal\culturefeed_content\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'culturefeed_content_field_widget' widget.
 *
 * @FieldWidget(
 *   id = "culturefeed_content_default",
 *   label = @Translation("Default CultureFeed content widget"),
 *   field_types = {
 *     "culturefeed_content"
 *   }
 * )
 */
class CultureFeedContentFieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $item =& $items[$delta];

    $element += [
      '#type' => 'fieldset',
    ];

    $element['title'] = [
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
      '#default_value' => isset($item->title) ? $item->title : '',
      '#maxlength' => $this->getFieldSetting('max_length'),
    ];

    $element['query_string'] = [
      '#title' => $this->t('Query string'),
      '#type' => 'textfield',
      '#default_value' => isset($item->query_string) ? $item->query_string : '',
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#description' => $this->t('Search using the query (q) parameter. See <a target="_blank" href="https://github.com/cultuurnet/udb3-search-docs">UDB3 search documentation</a>.'),
    ];

    $element['filter_query'] = [
      '#title' => $this->t('Filter query (deprecated)'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->filter_query) ? $items[$delta]->filter_query : NULL,
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#description' => $this->t('Please use SOLR filter query (fq). You can use advanced combinations with AND, OR, double quotes (exact match), and wildcards (*)'),
    ];

    $element['rows'] = [
      '#title' => $this->t('Rows'),
      '#type' => 'number',
      '#default_value' => isset($items[$delta]->rows) ? $items[$delta]->rows : 10,
      '#description' => $this->t('Number of results to show, defaults to "10"'),
    ];

    $element['sort'] = [
      '#title' => 'Sort',
      '#type' => 'radios',
      '#options' => [
        '_none' => $this->t('None'),
        'score' => $this->t('Score'),
        'availableTo' => $this->t('Available to'),
      ],
      '#default_value' => isset($items[$delta]->sort) ? $items[$delta]->sort : '_none',
    ];

    $element['sort_direction'] = [
      '#title' => $this->t('Sort direction'),
      '#type' => 'radios',
      '#options' => [
        'desc' => $this->t('Descending'),
        'asc' => $this->t('Ascending'),
      ],
      '#default_value' => isset($items[$delta]->sort_direction) ? $items[$delta]->sort_direction : 'desc',
    ];

    $element['show_more_link'] = [
      '#title' => $this->t('Show more link'),
      '#type' => 'checkbox',
      '#default_value' => isset($items[$delta]->show_more_link) ? $items[$delta]->show_more_link : TRUE,
      '#description' => $this->t('An automatic more link will be provided when this is checked.
      Important: does not support all filter query parameters.'),
    ];

    $element['more_link'] = [
      '#title' => $this->t('More link'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->more_link) ? $items[$delta]->more_link : NULL,
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#description' => $this->t('You can provide a custom URL in this field.'),
      '#states' => [
        'visible' => [
          ':input[name*="show_more_link"]' => ['checked' => FALSE],
        ],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $formValues) {
      if (isset($formValues['sort']) && $formValues['sort'] === '_none') {
        $values[$delta]['sort'] = NULL;
      }
    }

    return $values;
  }

}
