<?php

namespace Drupal\culturefeed_search;

use CultuurNet\SearchV3\ValueObjects\Event;
use CultuurNet\SearchV3\ValueObjects\Organizer;
use CultuurNet\SearchV3\ValueObjects\TranslatedString;
use Drupal\Core\Url as CoreUrl;

/**
 * Url class for Culturefeed search items.
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
   * @return \Drupal\Core\Url
   *   The Url.
   */
  public static function toEventDetail(Event $event, array $options = []): CoreUrl {
    $language = $options['language'] ?? \Drupal::languageManager()->getCurrentLanguage();

    return CoreUrl::fromRoute('culturefeed_agenda.event_detail', [
      'slug' => static::slug($event->getName(), $language->getId()),
      'event' => $event->getCdbid(),
    ], $options);
  }

  /**
   *  Creates a new Url object that points to the organizer detail page.
   *
   * @param \CultuurNet\SearchV3\ValueObjects\Organizer $organizer
   *   The organizer to generate an url for.
   * @param array $options
   *   Extra options for the url.
   *
   * @return \Drupal\Core\Url
   *   The url object.
   */
  public static function toOrganizerDetail(Organizer $organizer, array $options = []): CoreUrl {
    $language = $options['language'] ?? \Drupal::languageManager()->getCurrentLanguage();

    return CoreUrl::fromRoute('culturefeed_organizers.organizer_detail', [
      'slug' => static::slug($organizer->getName(), $language->getId()),
      'culturefeed_organizer' => $organizer->getCdbid(),
    ], $options);
  }

  /**
   * Gets a slug from a given TranslatedString object.
   *
   * @param string $langcode
   *   Language to use for generation.
   *
   * @return string
   *   The title slug.
   */
  public static function slug(?TranslatedString $name, string $langcode): string {
    if ($name === NULL) {
      return '';
    }

    $string = \Drupal::transliteration()->transliterate($name->getValueForLanguage($langcode));
    $separator = '-';
    $length = 50;

    // Lowercase.
    $string = mb_strtolower($string);

    // Replace non alphanumeric and non underscore charachters by separator.
    $string = preg_replace('/[^a-z0-9]/i', '-', $string);

    assert(is_string($string));

    // Replace multiple occurences of separator by one instance.
    $string = preg_replace('/' . preg_quote($separator) . '[' . preg_quote($separator) . ']*/', $separator, $string);

    assert(is_string($string));

    // Cut off to maximum length.
    if (strlen($string) > $length) {
      $string = substr($string, 0, $length);
    }

    // Remove separator from start and end of string.
    $string = preg_replace('/' . preg_quote($separator) . '$/', '', $string);
    assert(is_string($string));
    $string = preg_replace('/^' . preg_quote($separator) . '/', '', $string);

    return empty($string) ? '-' : $string;
  }

}
