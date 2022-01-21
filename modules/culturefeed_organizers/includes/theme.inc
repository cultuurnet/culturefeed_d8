<?php

/**
 * @file
 * Template preprocessors for Culturefeed organisation templates.
 */

/**
 * Preprocess an organizer item.
 *
 * @param array $variables
 *   Array of preprocessed variables.
 */
function template_preprocess_culturefeed_organizer(array &$variables) {
  /** @var \CultuurNet\SearchV3\ValueObjects\Organizer $organizer */
  $organizer = $variables['elements']['#item'];
  $langcode = $variables['elements']['#langcode'] ?? \Drupal::languageManager()->getCurrentLanguage()->getId();

  $variables['view_mode'] = $variables['elements']['#view_mode'] ?? 'full';
  $variables['teaser'] = $variables['view_mode'] == 'teaser';

  $variables['id'] = $organizer->getCdbid();
  $variables['name'] = $organizer->getName()->getValueForLanguage($langcode);
  $variables['description'] = $organizer->getDescription() ? $organizer->getDescription()->getValueForLanguage($langcode) : NULL;
  $variables['image'] = $organizer->getImage();

  if ($address = $organizer->getAddress()) {
    if ($translatedAddress = $address->getAddressForLanguage($langcode)) {
      $variables['street'] = $translatedAddress->getStreetAddress() ?? '';
      $variables['postalcode'] = $translatedAddress->getPostalCode() ?? '';
      $variables['city'] = $translatedAddress->getAddressLocality() ?? '';
    }
  }

  if ($contactPoint = $organizer->getContactPoint()) {
    $variables['emails'] = $contactPoint->getEmails();
    $variables['phone_numbers'] = $contactPoint->getPhoneNumbers();
    $variables['links'] = $contactPoint->getUrls();
  }

}

/**
 * Preprocess the list of events that are organized by an organizer.
 *
 * @param array $variables
 *   Array of preprocessed variables.
 */
function template_preprocess_culturefeed_organizers_organizer_events(array &$variables) {
  $variables['items'] = [];

  /** @var \CultuurNet\SearchV3\ValueObjects\PagedCollection $result */
  $result = $variables['result'];
  foreach ($result->getMember()->getItems() as $item) {
    $variables['items'][] = [
      '#theme' => 'culturefeed_event',
      '#view_mode' => 'teaser_mini',
      '#item' => $item,
    ];
  }
}