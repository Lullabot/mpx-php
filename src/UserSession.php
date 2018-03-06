<?php

namespace Mpx;

class UserSession {

  use HasLoggerTrait;

  /**
   * @var \Mpx\User
   */
  protected $user;

  /**
   * @var \Mpx\TokenCachePool
   */
  protected $tokenCachePool;

  /**
   * @var \Mpx\Client
   */
  protected $client;

  public function __construct(Client $client, User $user, TokenCachePool $tokenCachePool) {
    $this->client = $client;
    $this->user = $user;
    $this->tokenCachePool = $tokenCachePool;
  }

  /**
   * Get a current authentication token for the account.
   *
   * @param int $duration
   *   The number of seconds for which the token should be valid.
   * @param bool $force
   *   Set to TRUE if a fresh authentication token should always be fetched.
   *
   * @return string
   *   A valid MPX authentication token.
   */
  public function acquireToken($duration = NULL, $force = FALSE) {
    $token = $this->tokenCachePool->getToken($this->user);

    if ($force || !$token || !$token->isValid($duration)) {
      if ($token) {
        $this->signOut();
      }
      $token = $this->signIn();
    }

    return $token;
  }

  /**
   * Sign in the user and return the current token.
   */
  public function signIn($duration = NULL) {
    $options = [];
    $options['auth'] = [
      $this->user->getUsername(),
      $this->user->getPassword(),
    ];
    $options['query'] = [
      'schema' => '1.0',
      'form' => 'json',
    ];

    if (!empty($duration)) {
      // API expects this value in milliseconds, not seconds.
      $options['query']['_duration'] = $duration * 1000;
      $options['query']['_idleTimeout'] = $duration * 1000;
    }

    $data = $this->client->request(
      'GET',
      'https://identity.auth.theplatform.com/idm/web/Authentication/signIn',
      $options
    );

    $lifetime = (int) floor(min($data['signInResponse']['duration'], $data['signInResponse']['idleTimeout']) / 1000);
    $token = new Token($data['signInResponse']['token'], $lifetime);

    $this->getLogger()->info(
      'Fetched new mpx token {token} for user {username} that expires on {date}.',
      [
        'token' => $token->getValue(),
        'username' => $this->user->getUsername(),
        'date' => date(DATE_ISO8601, $token->getExpiration()),
      ]
    );

    // Save the token to the cache and return it.
    $this->tokenCachePool->setToken($this->user, $token);

    return $token;
  }

  /**
   * Sign out the user.
   */
  public function signOut() {
    $this->client->request(
      'GET',
      'https://identity.auth.theplatform.com/idm/web/Authentication/signOut',
      [
        'query' => [
          'schema' => '1.0',
          'form' => 'json',
          '_token' => (string) $this->tokenCachePool->getToken($this->user),
        ],
      ]
    );
  }

}
