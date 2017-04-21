<?php

namespace Skiftet\Speakout\Api;
use Skiftet\Speakout\Models\BaseModel;

/**
 *
 */
interface ResourceInterface
{

        /**
         *
         */
        public function subResourcePaths(): array;

        /**
         * @param BaseResource $resource
         */
        public function registerSubResource(BaseResource $resource);

        /**
         * @param string $path
         */
        public function subResource(string $path): BaseResource;

        /**
         * @param string $path
         * @param int $id
         */
        public function by(string $path, int $id): Query;

        /**
         *
         */
        public function path(): string;

        /**
         * @param array $data
         */
        public function hydrate($data): BaseModel;

        /**
         * @param array $data
         * @param ?string $prefix
         */
        public function create(array $data, ?string $prefix = null): BaseModel;

        /**
         * @param int $id
         */
        public function find(int $id): BaseModel;

        /**
         *
         */
        public function all(): array;

        /**
         * @param ?string $prefix
         */
        public function query(?string $prefix = null): Query;

        /**
         * @param string $column
         */
        public function orderBy(string $column): Query;

    }
