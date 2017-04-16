<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use InvalidArgumentException;
use Skiftet\Speakout\Models\BaseModel;

abstract class BaseResource extends BaseClient
{

    public function clientForResource(string $resource)
    {
        if (ucfirst($resource) !== class_basename(static::class)) {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    /**
     *
     */
    public function path(): string
    {
        return str_slug(class_basename(static::class));
    }

    protected function hydrate($data): BaseModel
    {
        $class = 'Skiftet\\Speakout\\Models\\'.str_singular(class_basename(static::class));
        return new $class($data, $this);
    }

    public function __construct(array $parameters)
    {
        $parameters['prefix'] = $this->path();
        parent::__construct($parameters);
    }

    public function find(int $id): BaseModel
    {
        return $this->hydrate($this->query()->find($id));
    }

    /**
     *
     */
    public function all(): array
    {
        return collect($this->query()->get())
            ->map(function ($item) {
                return $this->hydrate($item);
            })
            ->toArray()
        ;
    }

    public function get(string $path, array $query = [], bool $usePrefix = true): array
    {
        return collect(parent::get($path, $query, $usePrefix))
            ->map(function ($item) {
                return $this->hydrate($item);
            })
            ->toArray()
        ;
    }

    /**
     *
     */
    public function query(): Query
    {
        return Query::create($this, $this->prefix);
    }

    /**
     * @param string $column
     */
    public function orderBy(string $column): Query
    {
        return $this->query()->orderBy($column);
    }

    public function in($idOrModel)
    {

    }

}
