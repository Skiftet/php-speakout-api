<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use InvalidArgumentException;
use Skiftet\Speakout\Models\BaseModel;

/**
 *
 */
class NestedResourceProxy implements ResourceInterface
{

    /**
     *
     */
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
        return str_slug(class_basename(static::class));
    }

    public function hydrate($data): BaseModel
    {
        return $this->resource->hydrate($data);
    }

    public function create(array $data, ?string $prefix = null): BaseModel
    {
        return $this->resource->create(
            $data,
            $this->prefix()
        );
    }

    /**
     *
     */
    private function prefix()
    {
        return "$this->path/$this->id";
    }

    /**
     * @var ResourceInterface $resource
     */
     private $resource;

     /**
      * @var string $path
      */
     private $path;

     /**
      * @var int $id
      */
     private $id;

     /**
      * @param ResourceInterface $resource
      * @param string $path
      * @param int $id
      */
    public function __construct(
        ResourceInterface $resource,
        string $path,
        int $id
    ) {
        $this->resource = $resource;
        $this->path = trim($path, '/');
        $this->id = $id;
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
        return $this->resource->by($this->path, $this->id)->get();
    }

    /**
     *
     */
    public function query(?string $prefix = null): Query
    {
        return new Query($this, $this->prefix);
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
