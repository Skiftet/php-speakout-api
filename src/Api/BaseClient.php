<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use GuzzleHttp\Client as Guzzle;

/**
 *
 */
abstract class BaseClient
{
    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var string
     */
    protected $version = 'v1';

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        foreach (['user', 'password', 'endpoint'] as $mandatory) {
            if (!isset($parameters[$mandatory])) {
                throw new \Exception();
            }

            $this->$mandatory = $parameters[$mandatory];
        }

        foreach (['prefix', 'version'] as $optional) {
            if (!isset($parameters[$optional])) {
                continue;
            }

            $this->$optional = $parameters[$optional];
        }

        if (isset($parameters['client'])) {
            $this->client = $parameters['client'];
        } else {
            $this->client = new Guzzle([
                'base_uri' => $this->endpoint,
            ]);
        }
    }

    public function endpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param string $path
     * @param array $query
     * @param bool $usePrefix
     */
    public function get(string $path, array $query = [], bool $usePrefix = true): array
    {
        if ($usePrefix) {
            if (0 !== strpos($path, '/')) {
                $path = '/'.$path;
            }
            $path = rtrim($this->prefix, '/').$path;
        }

        if (0 !== strpos($path, '/')) {
            $path = '/'.$path;
        }

        return json_decode(
            (string)$this->client
                ->request('GET', '/api/'.$this->version.$path, [
                    'query' => $query,
                    'auth' => [
                        $this->user, $this->password
                    ],
                ])
                ->getBody(),
            true
        );
    }

    public function post(
        string $path,
        array $query = [],
        array $data = [],
        bool $userPrefix = true
    ): array {
        $path = trim($path, '/');
        $path = "/$path";

        return json_decode(
            (string)$this->client
                ->request('POST', '/api/'.$this->version.$path, [
                    'query' => $query,
                    'json' => $data,
                    'auth' => [
                        $this->user, $this->password
                    ],
                ])
                ->getBody(),
            true
        );
    }

    abstract public function clientForResource(string $resource): BaseResource;
}
