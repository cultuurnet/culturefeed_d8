<?php

namespace Drupal\culturefeed_organizers\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a Culturefeed organizer search form.
 */
class OrganizerSearchForm extends FormBase {

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request|null
   */
  protected $request;

  /**
   * AgendaSearchForm constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function __construct(Request $request) {
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')->getCurrentRequest()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'culturefeed_organizer_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Disabling the token will result in a cached block.
    $form['#token'] = FALSE;

    $form['title'] = [
      '#markup' => $this->t('Search organizer', [], ['context' => 'culturefeed_organizer']),
    ];

    $form['term'] = [
      '#type' => 'textfield',
      '#placeholder' => $this->t('Enter your search term here', [], ['context' => 'culturefeed_organizer']),
      '#default_value' => $this->request->query->get('q'),
    ];

    $form['actions']['search'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search', [], ['context' => 'culturefeed_organizer']),
    ];

    $form['#theme'] = 'culturefeed_organizers_search_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $query = $this->request->query->all();
    $query['q'] = $form_state->getValue('term');

    // Redirect the user.
    $form_state->setRedirect('culturefeed_organizers.search', [], ['query' => array_filter($query)]);
  }

}
