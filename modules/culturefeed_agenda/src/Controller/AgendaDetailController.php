<?php

namespace Drupal\culturefeed_agenda\Controller;

use CultuurNet\SearchV3\ValueObjects\Event;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\culturefeed_agenda\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines a route controller for Culturfeed event details.
 */
class AgendaDetailController extends ControllerBase {

  /**
   * The current language.
   *
   * @var \Drupal\Core\Language\LanguageInterface
   */
  protected $currentLanguage;

  /**
   * AgendaDetailController constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   */
  public function __construct(LanguageManagerInterface $languageManager) {
    $this->currentLanguage = $languageManager->getCurrentLanguage();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager')
    );
  }

  /**
   * Detail route with an upcasted Culturefeed event.
   *
   * @param string $slug
   *   The event slug.
   * @param \CultuurNet\SearchV3\ValueObjects\Event $event
   *   The Culturefeed event to display.
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *   Return render array or redirect to event detail page.
   */
  public function detail($slug, Event $event) {

    if ($slug !== Url::eventSlug($event, $this->currentLanguage->getId())) {
      return new RedirectResponse(Url::toEventDetail($event)->toString(), 301);
    }

    return [
      '#theme' => 'culturefeed_event',
      '#item' => $event,
      '#attached' => [
        'library' => [
          'culturefeed_agenda/google-maps',
        ],
      ],
    ];
  }

  /**
   * Title callback for detail pages.
   *
   * @param \CultuurNet\SearchV3\ValueObjects\Event $event
   *   The Culturefeed event being displayed.
   *
   * @return \Drupal\Component\Render\FormattableMarkup
   *   The page title.
   */
  public function title(Event $event) {
    return $event->getName()->getValueForLanguage($this->currentLanguage->getId());
  }

}
