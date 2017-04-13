<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use Closure;
use DateTime;

/**
 *
 */
class Query
{

    /**
     * @var BaseClient
     */
    private $client;

    /**
     * @var string
     */
    private $orderBy;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $since;

    /**
     * @var array
     */
    private $subQueries;

    /**
     * @param BaseClient $client
     * @param string $path
     */
    public function __construct(BaseClient $client, string $path = null)
    {
        $this->client = $client;
        $this->path = $path;
        $this->subQueries = [];
    }

    /**
     * @param array $arguments
     */
    static public function create(...$arguments): self
    {
        return new static(...$arguments);
    }

    /**
     * @param string $relationship
     * @param Closure $function
     */
    public function has(string $relationship, Closure $function): self
    {
        $this->subQueries[$relationship] = $function(
            static::create($this->client, $relationship)
        );
        return $this;
    }

    /**
     * @param DateTime|string $date
     */
    public function since($date): self
    {
        if (!is_string($date)) {
            $this->since = (string)$date;
        }

        $this->since = $date;
        return $this;
    }

    /**
     * @param string $column
     */
    public function orderBy(string $column): self
    {
        $this->orderBy = $column;
        return $this;
    }

    /**
     *
     */
    private function queryData(): array
    {
        $queryData = [];
        if ($this->orderBy) {
            $queryData['order_by'] = $this->orderBy;
        }

        if ($this->since) {
            $queryData['since'] = $this->since;
        }

        foreach ($this->subQueries as $name => $query) {
            $subQueryData = $query->queryData();
            foreach ($subQueryData as $key => $value) {
                $queryData["$key.$name"] = $value;
            }
        }

        return $queryData;
    }

    /**
     *
     */
    public function get(string $path = null): array
    {
        $query = $this->queryData();

        if (!$path) {
            if (!$this->path) {
                throw new \Exception(
                    'No path is set'
                );
            }

            $path = $this->path;
        }

        return $this->client->get($path, $query, false);
    }
}
