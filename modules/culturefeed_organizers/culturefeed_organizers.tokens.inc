<?php

/**
 * @file
 * Builds placeholder replacement tokens for culturefeed organizer related data.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function culturefeed_organizers_token_info() {
  $info = [];

  $info['types']['culturefeed_organizer'] = [
    'name' => t('Culturefeed organizer'),
    'description' => t('Tokens for organizer.'),
    'needs-data' => 'culturefeed_organizer',
  ];

  $info['tokens']['culturefeed_organizer']['name'] = [
    'name' => t('Name'),
    'description' => t('Name of the organizer'),
  ];

  return $info;
}

/**
 * Implements hook_tokens().
 */
function culturefeed_organizers_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type == 'culturefeed_organizer' && !empty($data['culturefeed_organizer'])) {
    /** @var \CultuurNet\SearchV3\ValueObjects\Organizer $organizer */
    $organizer = $data['culturefeed_organizer'];
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'name':
          $replacements[$original] = $organizer->getName()->getValueForLanguage($langcode);
          break;
      }
    }
  }

  return $replacements;
}
