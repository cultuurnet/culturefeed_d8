<?php

namespace Drupal\culturefeed_search_v2_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a settings form for the CultureFeed search API v2 settings.
 */
class CultureFeedSearchV2ApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'culturefeed_search_v2_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['culturefeed_search_v2_api.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    \Drupal::service('module_installer')->uninstall(['role_mixin'], FALSE);
    $config = $this->config('culturefeed_search_v2_api.settings');

    $form['culturefeed_search_v2_api']['endpoint_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Endpoint URL'),
      '#required' => TRUE,
      '#default_value' => $config->get('endpoint_url') ?: '',
    ];

    $form['culturefeed_search_v2_api']['authorization_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Authorization key'),
      '#required' => TRUE,
      '#default_value' => $config->get('authorization_key') ?: '',
    ];

    $form['culturefeed_search_v2_api']['secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret'),
      '#required' => TRUE,
      '#default_value' => $config->get('secret') ?: '',
    ];

    $form['culturefeed_search_v2_api']['enable_cache'] = [
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

    $config = $this->config('culturefeed_search_v2_api.settings');
    $config->set('endpoint_url', $form_state->getValue('endpoint_url'));
    $config->set('authorization_key', $form_state->getValue('authorization_key'));
    $config->set('secret', $form_state->getValue('secret'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
