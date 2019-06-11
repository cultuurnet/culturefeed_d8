<?php

/**
 * @file
 * Template preprocessors for Culturefeed content templates.
 */

/**
 * Preprocess a culturefeed content formatter.
 *
 * @param array $variables
 *   Array of currently known variables.
 */
function template_preprocess_culturefeed_content_formatter(array &$variables) {
  $items = $variables['items'];
  $variables['items'] = [];

  foreach ($items as $item) {
    $variables['items'][] = [
      '#theme' => 'culturefeed_event',
      '#view_mode' => $variables['view_mode'] ?? 'teaser',
      '#item' => $item,
      '#settings' => [
        'image' => [
          'width' => 150,
          'height' => 150,
          'fit' => 'auto',
        ],
      ],
    ];
  }
}
