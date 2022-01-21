<?php

namespace Drupal\culturefeed_organizers\BreadcrumbBuilder;

use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a breadcrumb builder for organisations.
 */
class OrganisationsBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * The breadcrumb to build.
   *
   * @var \Drupal\Core\Breadcrumb\Breadcrumb
   */
  protected $breadcrumb;

  /**
   * AgendaBreadcrumbBuilder constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The current request stack.
   */
  public function __construct(RequestStack $requestStack) {
    $this->currentRequest = $requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $routeMatch) {
    return $routeMatch->getRouteName() === 'culturefeed_organizers.organizer_detail';
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $routeMatch) {
    $this->breadcrumb = new Breadcrumb();

    $this->breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
    $this->breadcrumb->addLink(Link::createFromRoute($this->t('Organisations'), 'culturefeed_agenda.agenda'));

    $this->breadcrumb->addCacheContexts(['url.path']);

    return $this->breadcrumb;
  }

}
