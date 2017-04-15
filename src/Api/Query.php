<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use Closure;
use DateTime;
use InvalidArgumentException;

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
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

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
     * @param int $page
     */
    public function page(int $page): self
    {
        if ($page < 1) {
            throw new InvalidArgumentException("Pages are 1-indexed. The provided page was $page");
        }

        $this->page;
        return $this;
    }

    public function limit(int $limit): self
    {
        if ($limit < 1) {
            throw new InvalidArgumentException("The limit must be at least 1. The provided page was $limit");
        }

        $this->limit = $limit;
        return $this;
    }

    /**
     *
     */
    private function commonQueryData(): array
    {
        return [];
    }

    private function singleQueryData(): array
    {
        $queryData = $this->commonQueryData();

        foreach ($this->subQueries as $name => $query) {
            $subQueryData = $query->arrayQueryData();
            foreach ($subQueryData as $key => $value) {
                $queryData["$key.$name"] = $value;
            }
        }

        return $queryData;
    }

    /**
     *
     */
    private function arrayQueryData(): array
    {
        $queryData = $this->commonQueryData();
        if ($this->orderBy) {
            $queryData['order_by'] = $this->orderBy;
        }

        if ($this->since) {
            $queryData['since'] = $this->since;
        }

        if ($this->page) {
            $queryData['page'] = $this->page;
        }

        if ($this->limit) {
            $queryData['limit'] = $this->limit;
        }

        foreach ($this->subQueries as $name => $query) {
            $subQueryData = $query->arrayQueryData();
            foreach ($subQueryData as $key => $value) {
                $queryData["$key.$name"] = $value;
            }
        }

        return $queryData;
    }

    public function find(int $id): array
    {
        $query = $this->singleQueryData();

        if (!$this->path) {
            throw new \Exception(
                'No path is set'
            );
        }

        return $this->client->get("/$id", $query);
    }

    /**
     * @param ?string $path
     */
    public function get(string $path = null): array
    {
        $query = $this->arrayQueryData();

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
