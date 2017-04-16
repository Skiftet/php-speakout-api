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

    /**
     * @var BaseClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(BaseClient $client, array $data)
    {
        $this->client = $client->clientForResource(
            strtolower(
                str_plural(
                    class_basename(static::class)
                )
            )
        );
        $this->data = $data;
    }
}
