<?php

namespace LunaQL\Config;

use LunaQL\Contracts\RelationshipConfig as Config;

class RelationshipConfig implements Config {
    /**
     * Create a new relationship config.
     */
    public function __construct(
        private string $type,
        private string $collection,
    ) {}

    /**
     * Get the collection.
     */
    public function getCollection(): string {
        return $this->collection;
    }

    /**
     * Get the type.
     */
    public function getType(): string {
        return $this->type;
    }
}
