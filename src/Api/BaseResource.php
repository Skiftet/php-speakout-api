<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use InvalidArgumentException;
use Illuminate\Support\Str;
use Skiftet\Speakout\Models\BaseModel;

abstract class BaseResource extends BaseClient implements ResourceInterface
{
    public function clientForResource(string $resource): BaseResource
    {
        if (ucfirst($resource) !== class_basename(static::class)) {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    private $subResources = [];

    public function subResourcePaths(): array
    {
        return [];
    }

    public function registerSubResource(BaseResource $resource)
    {
        $this->subResources[$resource->path()] = $resource;
    }

    public function subResource(string $path): BaseResource
    {
        $path = trim($path, '/');

        if (!isset($this->subResources[$path])) {
            throw new InvalidArgumentException();
        }

        return $this->subResources[$path];
    }

    public function by(string $path, int $id): Query
    {
        return $this->query()->by($path, $id);
    }

    /**
     *
     */
    public function path(): string
    {
        return Str::slug(class_basename(static::class));
    }

    public function hydrate($data): BaseModel
    {
        $class = 'Skiftet\\Speakout\\Models\\'.Str::singular(class_basename(static::class));
        return new $class($data, $this);
    }

    public function create(array $data, ?string $prefix = null): BaseModel
    {
        if (!$prefix) {
            $prefix = '';
        }

        $prefix = trim($prefix, '/');
        return $this->query("$prefix/$this->prefix")->create($data);
    }

    public function __construct(array $parameters)
    {
        $parameters['prefix'] = $this->path();
        parent::__construct($parameters);
    }

    public function find(int $id): BaseModel
    {
        return $this->query()->find($id);
    }

    /**
     *
     */
    public function all(): array
    {
        return $this->query()->get();
    }

    /**
     * @param ?string $prefix
     */
    public function query(?string $prefix = null): Query
    {
        if (!$prefix) {
            $prefix = $this->prefix;
        }

        return new Query($this, $prefix);
    }

    /**
     * @param string $column
     */
    public function orderBy(string $column): Query
    {
        return $this->query()->orderBy($column);
    }

}
