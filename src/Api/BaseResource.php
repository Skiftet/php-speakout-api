<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use Skiftet\Speakout\Models\BaseModel;

abstract class BaseResource extends BaseClient
{
    /**
     *
     */
    public function path(): string
    {
        return str_slug(class_basename(static::class));
    }

    protected function hydrate(): BaseModel
    {

    }

    public function __construct(array $parameters)
    {
        $parameters['prefix'] = $this->path();
        parent::__construct($parameters);
    }

    public function find(int $id): array
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
