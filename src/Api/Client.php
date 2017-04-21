<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use BadMethodCallException;

/**
 * @method Campaigns campaigns()
 */
class Client extends BaseClient
{
    /**
     * @var array
     */
    private $resources = [
        'campaigns' => null,
        'actions' => null,
        'surveys' => null,
    ];

    public function clientForResource(string $resource): BaseResource
    {
        return $this->$resource();
    }

    /**
     * @inheritDoc
     */
    public function __call(string $name, array $parameters)
    {
        if (!array_key_exists($name, $this->resources)) {
            throw new BadMethodCallException(
                "There isn't any resource named $name"
            );
        }

        if (!$this->resources[$name]) {
            $class = __NAMESPACE__.'\\'.ucfirst($name);
            $this->resources[$name] = $resource = new $class([
                'user'     => $this->user,
                'password' => $this->password,
                'endpoint' => $this->endpoint,
                'version'  => $this->version,
                'client'   => $this->client,
            ]);
            foreach ($resource->subResourcePaths() as $path) {
                $resource->registerSubResource($this->clientForResource($path));
            }
        }

        return $this->resources[$name];
    }
}
