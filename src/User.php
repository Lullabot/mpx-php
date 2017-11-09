<?php

namespace Mpx;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class User implements UserInterface {

    use HasCachePoolTrait;
    use HasClientTrait;
    use HasLoggerTrait;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \Mpx\Token|null
     */
    private $token;

    /**
     * @param string $username
     * @param string $password
     * @param \Mpx\ClientInterface $client
     * @param \Psr\Cache\CacheItemPoolInterface $cachePool
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct($username, $password, ClientInterface $client = NULL, CacheItemPoolInterface $cachePool = NULL, LoggerInterface $logger = NULL) {
        $this->username = $username;
        $this->password = $password;
        $this->client = $client;
        $this->cachePool = $cachePool;
        $this->logger = $logger;
        $this->token = $this->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @return \Mpx\Token|null
     */
    public function getToken() {
        return $this->token;
    }

    public function setToken(Token $token) {
        $this->token = $token;

        // Save the token to the cache pool.
        $item = $this->getCachePool()->getItem('token|' . md5($this->getUsername()));
        $item->set($token);
        $item->expiresAfter($token->getLifetime());
        $this->getCachePool()->save($item);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateToken() {
        $this->token = NULL;
        $this->getCachePool()->deleteItem('token|' . md5($this->getUsername()));
    }

    /**
     * {@inheritdoc}
     */
    public function acquireToken($duration = NULL, $force = FALSE) {
        $token = $this->getToken();

        if ($force || !$token || !$token->isValid($duration)) {
            if ($token) {
                $this->signOut();
            }
            $token = $this->signIn();
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function signIn($duration = NULL) {
        $options['auth'] = [
            $this->getUsername(),
            $this->getPassword(),
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

        $data = $this->getClient()->request(
            'GET',
            'https://identity.auth.theplatform.com/idm/web/Authentication/signIn',
            NULL,
            $options
        );

        $lifetime = (int) floor(min($data['signInResponse']['duration'], $data['signInResponse']['idleTimeout']) / 1000);
        $token = new Token($data['signInResponse']['token'], $lifetime);

        $this->getLogger()->info(
            'Fetched new mpx token {token} for user {username} that expires on {date}.',
            [
                'token' => $token->getValue(),
                'username' => $this->getUsername(),
                'date' => date(DATE_ISO8601, $token->getExpiration()),
            ]
        );

        // Save the token to the cache and return it.
        $this->setToken($token);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function signOut() {
        if ($token = $this->getToken()) {
            $this->getClient()->request(
                'GET',
                'https://identity.auth.theplatform.com/idm/web/Authentication/signOut',
                NULL,
                array(
                    'query' => array(
                        'schema' => '1.0',
                        'form' => 'json',
                        '_token' => (string) $token,
                    ),
                )
            );

            $this->getLogger()->info(
                'Expired mpx token {token} for {username}.',
                array(
                    'token' => $token->getValue(),
                    'username' => $this->getUsername(),
                )
            );

            $this->invalidateToken();
        }
    }

    protected function getSelfId() {
        $item = $this->getCachePool()->getItem('user|' . md5($this->getUsername()));
        $data = $item->get();

        if (!$item->isHit()) {
            $data = $this->getClient()->request(
                'GET',
                'https://identity.auth.theplatform.com/idm/web/Self/getSelfId',
                $this,
                array(
                    'query' => array(
                        'schema' => '1.0',
                        'form' => 'json',
                    ),
                )
            );
            $data = $data['getSelfIdResponse'];
            $item->set($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        $data = $this->getSelfId();
        return basename($data['userId']);
    }

}
