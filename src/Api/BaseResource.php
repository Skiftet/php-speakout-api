<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

use Closure;

abstract class BaseResource extends BaseClient
{
    /**
     *
     */
    public function path(): string
    {
        return str_slug(class_basename(static::class));
    }

    public function __construct(array $parameters)
    {
        $parameters['prefix'] = $this->path();
        parent::__construct($parameters);
    }

    public function all(): array
    {
        return $this->query()->get();
    }

    public function query(): Query
    {
        return Query::create($this, $this->prefix);
    }

    public function orderBy(string $column): Query
    {
        return $this->query()->orderBy($column);
    }

}
