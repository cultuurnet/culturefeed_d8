<?php

namespace Drupal\culturefeed_organizers\Controller;

use CultuurNet\SearchV3\ValueObjects\Organizer;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\culturefeed_search\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines a controller for Culturefeed organizer details.
 */
class OrganizerDetailController extends ControllerBase {

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
   * Detail route with an upcasted Culturefeed organizer.
   *
   * @param string $slug
   *   The organizer slug.
   * @param \CultuurNet\SearchV3\ValueObjects\Organizer $culturefeed_organizer
   *   The Culturefeed organizer to display.
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *   Return render array or redirect to event detail page.
   */
  public function detail(string $slug, Organizer $culturefeed_organizer) {
    if ($slug !== Url::slug($culturefeed_organizer->getName(), $this->currentLanguage->getId())) {
      return new RedirectResponse(Url::toOrganizerDetail($culturefeed_organizer)->toString(), 301);
    }

    return [
      '#theme' => 'culturefeed_organizer',
      '#item' => $culturefeed_organizer,
    ];
  }

  /**
   * Title callback for detail pages.
   *
   * @param \CultuurNet\SearchV3\ValueObjects\Organizer $culturefeed_organizer
   *   The Culturefeed event being displayed.
   *
   * @return \Drupal\Component\Render\FormattableMarkup|string
   *   The page title.
   */
  public function title(Organizer $culturefeed_organizer) {
    return $culturefeed_organizer->getName()->getValueForLanguage($this->currentLanguage->getId());
  }

}
