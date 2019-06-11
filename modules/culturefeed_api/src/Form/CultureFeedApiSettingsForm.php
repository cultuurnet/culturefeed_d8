<?php

namespace Drupal\culturefeed_api\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a settings form for the API settings.
 */
class CultureFeedApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'culturefeed_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['culturefeed_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('culturefeed_api.settings');

    $form['culturefeed_api']['api_location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API location'),
      '#required' => TRUE,
      '#default_value' => $config->get('api_location') ?: '',
    ];

    $form['culturefeed_api']['application_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Application key'),
      '#required' => TRUE,
      '#default_value' => $config->get('application_key') ?: '',
    ];

    $form['culturefeed_api']['shared_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shared secret'),
      '#required' => TRUE,
      '#default_value' => $config->get('shared_secret') ?: '',
    ];

    $form['culturefeed_api']['enable_cache'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable cache'),
      '#default_value' => $config->get('enable_cache') === NULL ? TRUE : $config->get('enable_cache'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('culturefeed_api.settings');
    $config->set('api_location', $form_state->getValue('api_location'));
    $config->set('application_key', $form_state->getValue('application_key'));
    $config->set('shared_secret', $form_state->getValue('shared_secret'));
    $config->save();

    Cache::invalidateTags(['culturefeed_api']);

    parent::submitForm($form, $form_state);
  }

}
