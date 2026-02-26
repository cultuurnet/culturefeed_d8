<?php

namespace Drupal\culturefeed_search_api\Logger;

use Drupal\Core\Render\Markup;
use Monolog\Handler\AbstractHandler;
use Monolog\Level;
use Drupal\dblog\Logger\DbLog;
use Monolog\LogRecord;

/**
 * Provides a monolog handler to log messages that should not be translated.
 *
 * We use this to log api debug info.
 */
class UntranslatedDatabaseLogHandler extends AbstractHandler {

  /**
   * UntranslatedDatabaseLogHandler constructor.
   *
   * @param \Drupal\dblog\Logger\DbLog $dbLog
   *   The db log object.
   * @param int|string|\Monolog\Level $level
   *   Level to log.
   * @param bool $bubble
   *   Bubble the log or not.
   */
  public function __construct(protected DbLog $dbLog, int|string|Level $level = Level::Debug, $bubble = TRUE) {
    // @phpstan-ignore-next-line
    parent::__construct($level, $bubble);
  }

  /**
   * {@inheritdoc}
   */
  public function handle(LogRecord $record): bool {

    // Set up context with the data Drupal loggers expect.
    // @see Drupal\Core\Logger\LoggerChannel::log()
    $context = $record['context'] + [
      'channel' => $record['channel'],
      'link' => '',
      'user' => $record->extra['user'] ?? NULL,
      'uid' => $record->extra['uid'] ?? 0,
      'request_uri' => $record->extra['request_uri'] ?? '',
      'referer' => $record->extra['referer'] ?? '',
      'ip' => $record->extra['ip'] ?? 0,
      'timestamp' => $record->datetime->format('U'),
      '@log_message' => Markup::create($record->message),
    ];

    $this->dbLog->log($record->level->toRFC5424Level(), '@log_message', $context);

    return TRUE;
  }

}
