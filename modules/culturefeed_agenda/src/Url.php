<?php

namespace Drupal\culturefeed_agenda;

use CultuurNet\SearchV3\ValueObjects\Event;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Url as CoreUrl;

/**
 * Url class for culturefeed agenda.
 */
class Url extends CoreUrl {

  /**
   * Creates a new Url object that points to the event detail page.
   *
   * @param \CultuurNet\SearchV3\ValueObjects\Event $event
   *   The event to generate an url for.
   * @param array $options
   *   Extra options for the url.
   *
   * @return Drupal\Core\Url
   *   The Url
   */
  public static function toEventDetail(Event $event, array $options = []) {

    $language = $options['language'] ?? \Drupal::languageManager()->getCurrentLanguage();

    return CoreUrl::fromRoute('culturefeed_agenda.event_detail', [
      'slug' => static::eventSlug($event, $language->getId()),
      'event' => $event->getCdbid(),
    ], $options);
  }

  /**
   * Get the title slug for this event.
   *
   * @param \CultuurNet\SearchV3\ValueObjects\Event $event
   *   The event to generate a slug for.
   * @param string $langcode
   *   Langcode to use for generation.
   *
   * @return bool|null|string|string[]
   *   The title slug.
   */
  public static function eventSlug(Event $event, $langcode) {

    // Transliterate.
    $string = \Drupal::transliteration()->transliterate($event->getName()->getValueForLanguage($langcode));
    $separator = '-';
    $length = 50;

    // Lowercase.
    $string = Unicode::strtolower($string);

    // Replace non alphanumeric and non underscore charachters by separator.
    $string = preg_replace('/[^a-z0-9]/i', '-', $string);

    // Replace multiple occurences of separator by one instance.
    $string = preg_replace('/' . preg_quote($separator) . '[' . preg_quote($separator) . ']*/', $separator, $string);

    // Cut off to maximum length.
    if ($length > -1 && strlen($string) > $length) {
      $string = substr($string, 0, $length);
    }

    // Remove separator from start and end of string.
    $string = preg_replace('/' . preg_quote($separator) . '$/', '', $string);
    $string = preg_replace('/^' . preg_quote($separator) . '/', '', $string);

    return $string;
  }

}
