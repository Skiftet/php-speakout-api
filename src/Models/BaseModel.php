<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Models;
use ArrayAccess;
use Skiftet\Speakout\Api\BaseClient;

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
    public function offsetGet($offset)
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
     * @var BaseClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $data;

    public function setClient(BaseClient $client): self
    {
        $this->client = $client->clientForResource(
            strtolower(
                str_plural(
                    class_basename(static::class)
                )
            )
        );
        return $this;
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
