<?php

namespace Drupal\culturefeed_search_api\Logger;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Render\Markup;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use Drupal\dblog\Logger\DBlog;

/**
 * Provides a monolog handler to log messages that should not be translated (example api debug info).
 */
class UntranslatedDatabaseLogHandler extends AbstractHandler {

  /**
   * The database log.
   *
   * @var \Drupal\dblog\Logger\DBlog
   */
  protected $dbLog;

  /**
   * Logging levels map to drupal levels.
   *
   * @var array
   */
  protected static $levels = [
    Logger::ERROR     => RfcLogLevel::ERROR,
    Logger::INFO      => RfcLogLevel::INFO,
    Logger::NOTICE    => RfcLogLevel::NOTICE,
    Logger::WARNING   => RfcLogLevel::WARNING,
    Logger::ERROR     => RfcLogLevel::ERROR,
    Logger::CRITICAL  => RfcLogLevel::CRITICAL,
    Logger::ALERT     => RfcLogLevel::ALERT,
    Logger::EMERGENCY => RfcLogLevel::EMERGENCY,
    Logger::DEBUG     => RfcLogLevel::DEBUG,
  ];

  /**
   * UntranslatedDatabaseLogHandler constructor.
   *
   * @param \Drupal\dblog\Logger\DBlog $dbLog
   *   The db log object.
   * @param bool|int $level
   *   Level to log.
   * @param bool $bubble
   *   Bubble the log or not.
   */
  public function __construct(DBlog $dbLog, $level = Logger::DEBUG, $bubble = TRUE) {
    parent::__construct($level, $bubble);
    $this->dbLog = $dbLog;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(array $record) {

    // Set up context with the data Drupal loggers expect.
    // @see Drupal\Core\Logger\LoggerChannel::log()
    $context = $record['context'] + [
      'channel' => $record['channel'],
      'link' => '',
      'user' => isset($record['extra']['user']) ? $record['extra']['user'] : NULL,
      'uid' => isset($record['extra']['uid']) ? $record['extra']['uid'] : 0,
      'request_uri' => isset($record['extra']['request_uri']) ? $record['extra']['request_uri'] : '',
      'referer' => isset($record['extra']['referer']) ? $record['extra']['referer'] : '',
      'ip' => isset($record['extra']['ip']) ? $record['extra']['ip'] : 0,
      'timestamp' => $record['datetime']->format('U'),
      '@log_message' => Markup::create($record['message']),
    ];

    $this->dbLog->log(self::$levels[$record['level']], '@log_message', $context);
  }

}
