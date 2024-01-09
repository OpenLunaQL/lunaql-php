<?php

namespace LunaQL\Builder;

use LunaQL\Config\DatabaseConfig;

class CollectionBuilder {
    /**
     * Create a new collection builder.
     */
    public function __construct(
        private DatabaseConfig $config
    ) {}

    /**
     * Define the collection to query.
     */
    function from(string $collection): QueryBuilder {
        return new QueryBuilder($this->config, $collection);
    }
}
