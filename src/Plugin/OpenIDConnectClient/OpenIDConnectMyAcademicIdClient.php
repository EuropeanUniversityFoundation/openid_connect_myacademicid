<?php

namespace Drupal\openid_connect_myacademicid\Plugin\OpenIDConnectClient;

use Drupal\Core\Form\FormStateInterface;
use Drupal\openid_connect\Plugin\OpenIDConnectClientBase;

/**
 * MyAcademicID OpenID Connect client.
 *
 * @OpenIDConnectClient(
 *   id = "myacademicid",
 *   label = @Translation("MyAcademicID")
 * )
 */
class OpenIDConnectMyAcademicIdClient extends OpenIDConnectClientBase {

  const PLACEHOLDER_CLIENT_ID = 'myacademicid_client_id';
  const PLACEHOLDER_CLIENT_SECRET = 'myacademicid_client_secret';

  /**
   * Base URLs for MyAcademicID environments.
   *
   * @var array
   */
  protected $envUrls = [
    'acceptance' => 'https://proxy.acc.erasmus.eduteams.org',
    'production' => 'https://proxy.prod.erasmus.eduteams.org',
  ];

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'environment' => 'acceptance',
      'scopes' => ['openid', 'email'],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);

    $requirement = $this->t('To @complete, a @step is necessary. See below.', [
      '@complete' => $this->t('complete the client setup'),
      '@step' => $this->t('configuration override'),
    ]);

    $form['client_id']['#type'] = 'item';
    $form['client_id']['#default_value'] = self::PLACEHOLDER_CLIENT_ID;
    $form['client_id']['#description'] = $this->t('Value set to %value.', [
      '%value' => self::PLACEHOLDER_CLIENT_ID,
    ]) . ' ' . $requirement;

    $form['client_secret']['#type'] = 'item';
    $form['client_secret']['#default_value'] = self::PLACEHOLDER_CLIENT_SECRET;
    $form['client_secret']['#description'] = $this->t('Value set to %value.', [
      '%value' => self::PLACEHOLDER_CLIENT_SECRET,
    ]) . ' ' . $requirement;

    $form['iss_allowed_domains']['#type'] = 'value';

    $form['override_help'] = [
      '#type' => 'details',
      '#title' => $this->t('How to override this configuration'),
      '#open' => !($this->parentEntityId),
    ];

    $markup[] = $this->t('Add the following to your %global or %local file:', [
      '%global' => 'settings.php',
      '%local' => 'settings.local.php',
    ]);

    $common = '$config[\'openid_connect.client.<i>machine_name</i>\']';
    $status = $common . '[\'status\']';
    $settings = $common . '[\'settings\']';
    $settings_id = $settings . '[\'client_id\']';
    $settings_secret = $settings . '[\'client_secret\']';

    $markup[] = '<code>';
    $markup[] = '/* MyAcademicID settings */';
    $markup[] = '#' . $status . ' = FALSE;';
    $markup[] = $settings_id . ' = <b>REAL_CLIENT_ID</b>;';
    $markup[] = $settings_secret . ' = <b>REAL_CLIENT_SECRET</b>;';
    $markup[] = '</code>';

    $markup[] = $this->t('Alternatively, use the %generic client instead.', [
      '%generic' => $this->t('Generic OAuth 2.0'),
    ]);

    $form['override_help']['help_text'] = [
      '#type' => 'markup',
      '#markup' => implode('<br />', $markup),
    ];

    $form['scopes'] = [
      '#title' => $this->t('Scopes'),
      '#type' => 'textfield',
      '#description' => $this->t('Custom scopes, separated by spaces, for example: openid email'),
      '#default_value' => implode(' ', $this->configuration['scopes']),
    ];

    $form['environment'] = [
      '#title' => $this->t('Environment'),
      '#type' => 'select',
      '#options' => [
        'acceptance' => $this->t('Acceptance'),
        'production' => $this->t('Production'),
      ],
      '#default_value' => $this->configuration['environment'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $configuration = $form_state->getValues();

    if (!empty($configuration['scopes'])) {
      $this->setConfiguration([
        'scopes' => explode(' ', $configuration['scopes'])
      ]);
    }

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getClientScopes(): ?array {
    return $this->configuration['scopes'];
  }

  /**
   * {@inheritdoc}
   */
  public function getEndpoints() : array {
    $base_url = $this->envUrls[$this->configuration['environment']];

    return [
      'authorization' => $base_url . '/OIDC/authorize',
      'token' => $base_url . '/OIDC/token',
      'userinfo' => $base_url . '/OIDC/userInfo',
    ];
  }

}
