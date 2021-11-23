<?php

namespace Drupal\culturefeed_agenda\Utility;

use Drupal\Component\Utility\Unicode as CoreUnicode;

/**
 * Provides Unicode-related conversions and operations.
 *
 * @ingroup utility
 */
class Unicode extends CoreUnicode {

  /**
   * Counts the number of characters in a UTF-8 string.
   *
   * This is less than or equal to the byte count.
   *
   * @param string $text
   *   The string to run the operation on.
   *
   * @return int
   *   The length of the string.
   */
  public static function strlen($text) {
    if (static::getStatus() == static::STATUS_MULTIBYTE) {
      return mb_strlen($text);
    }
    else {
      // Do not count UTF-8 continuation bytes.
      return strlen(preg_replace("/[\x80-\xBF]/", '', $text));
    }
  }

}
