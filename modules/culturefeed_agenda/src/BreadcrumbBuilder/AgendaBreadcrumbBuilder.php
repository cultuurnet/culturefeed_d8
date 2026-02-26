<?php

namespace Drupal\culturefeed_agenda\BreadcrumbBuilder;

use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a breadcrumb builder for agenda.
 */
class AgendaBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * The breadcrumb to build.
   *
   * @var null|\Drupal\Core\Breadcrumb\Breadcrumb
   */
  protected ?Breadcrumb $breadcrumb = NULL;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $routeMatch) {
    return $routeMatch->getRouteName() === 'culturefeed_agenda.event_detail';
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $routeMatch) {
    $this->breadcrumb = new Breadcrumb();

    $this->breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
    $this->breadcrumb->addLink(Link::createFromRoute($this->t('Agenda'), 'culturefeed_agenda.agenda'));

    $this->breadcrumb->addCacheContexts(['url.path']);

    return $this->breadcrumb;
  }

}
