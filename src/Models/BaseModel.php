<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Models;
use ArrayAccess;
use RuntimeException;
use Illuminate\Support\Str;
use Skiftet\Speakout\Api\BaseClient;
use Skiftet\Speakout\Api\BaseResource;
use Skiftet\Speakout\Api\Query;
use Skiftet\Speakout\Api\ResourceInterface;
use Skiftet\Speakout\Api\NestedResourceProxy;

/**
 *
 */
class BaseModel implements ArrayAccess
{

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): mixed
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function __sleep()
    {
        return ['data'];
    }


    /**
     * @var BaseResource
     */
    protected $client;

    /**
     * @var array
     */
    protected $data;

    protected function client(): BaseResource
    {
        if (!$this->client) {
            throw new RuntimeException('There is no client set');
        }

        return $this->client;
    }

    private function path(): string
    {
        return strtolower(Str::plural(class_basename(static::class)));
    }

    public function setClient(BaseClient $client): self
    {
        $this->client = $client->clientForResource($this->path());
        return $this;
    }

    public function subResource(string $path): ResourceInterface
    {
        $path = trim($path, '/');
        $subResource = $this->client()->subResource($path);

        return new NestedResourceProxy($subResource, $this->path(), $this['id']);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->subResource($name);
    }

    /**
     * @param array $data
     */
    public function __construct(array $data, ?BaseClient $client = null)
    {
        if ($client) {
            $this->setClient($client);
        }

        $this->data = $data;
    }
}
