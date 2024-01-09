<?php

namespace LunaQL\Builder;

use LunaQL\Config\RelationshipConfig;

class RelationshipBuilder {
    /**
     * Raw query.
     */
    private array $_builder = [
        "query" => []
    ];

    /**
     * Create a new relationship builder.
     */
    public function __construct(
        private RelationshipConfig $config,
    ) {
        $this->_builder["query"][$this->config->getType()] = [
            $this->config->getCollection() => []
        ];
    }

    /**
     * Select fields from the collection.
     */
    public function select(array $fields): RelationshipBuilder {
        return $this->updateQuery('select', $fields);
    }

    /**
     * Hide fields from the collection.
     */
    public function hidden(array $fields): RelationshipBuilder {
        return $this->updateQuery('hidden', $fields);
    }

    /**
     * Filter the collection.
     */
    public function where(string $field, string $operator, mixed $value): RelationshipBuilder {
        return $this->updateQuery(
            keys: "where",
            query: [ $field, $operator, $value ],
            create: true,
            push: true,
        );
    }

    /**
     * Filter the collection.
     */
    public function orWhere(string $field, string $operator, mixed $value): RelationshipBuilder {
        return $this->updateQuery(
            keys: "orWhere",
            query: [ $field, $operator, $value ],
            create: true,
            push: true,
        );
    }

    public function orderBy(string $field, string $direction = "asc"): RelationshipBuilder {
        return $this->updateQuery("orderBy", $field)->sort($direction);
    }

    /**
     * Sort the collection.
     */
    public function sort($direction = "asc"): RelationshipBuilder {
        return $this->updateQuery("sort", $direction);
    }

    /**
     * Group the collection.
     *
     * @param string[] $fields
     */
    public function groupBy(array $fields): RelationshipBuilder {
        return $this->updateQuery("groupBy", $fields);
    }

    /**
     * Filter the collection.
     */
    public function having(string $field, string $operator, mixed $value): RelationshipBuilder {
        return $this->updateQuery("having", [
            $field, $operator, $value
        ], true);
    }

    /**
     * Limit the collection.
     */
    public function limit(int $limit): RelationshipBuilder {
        return $this->updateQuery("limit", $limit);
    }

    /**
     * Skip documents in the collection.
     */
    public function skip(int $skip): RelationshipBuilder {
        return $this->updateQuery("skip", $skip);
    }

    /**
     * Join collection to another collection.
     */
    public function hasMany(string $collection, callable $callback): RelationshipBuilder {
        $builder = new RelationshipBuilder(
            new RelationshipConfig( 'hasMany',  $collection )
        );

        $callback($builder);

        return $this->updateQuery(
            keys: ["hasMany", $collection],
            query: $builder->getQuery()["query"]["hasMany"][$collection],
            create: true
        );
    }

    /**
     * Join collection to another collection.
     */
    public function belongsTo(string $collection, callable $callback): RelationshipBuilder {
        $builder = new RelationshipBuilder(
            new RelationshipConfig( 'belongsTo',  $collection )
        );

        $callback($builder);

        return $this->updateQuery(
            keys: ["belongsTo", $collection],
            query: $builder->getQuery()["query"]["belongsTo"][$collection],
            create: true
        );
    }

    /**
     * Update query.
     */
    private function updateQuery(string | array $keys, mixed $query, bool $create = false, bool $push = false) {
        if (is_string($keys)) {
            $keys = [ $keys ];
        }

        if (!isset($this->_builder["query"][$this->config->getType()][$this->config->getCollection()][$keys[0]]) && $create) {
            $this->_builder["query"][$this->config->getType()][$this->config->getCollection()][$keys[0]] = [];
        }

        if (count($keys) === 1) {
            $this->_builder["query"][$this->config->getType()][$this->config->getCollection()][$keys[0]] = $push
                ? [...$this->_builder["query"][$this->config->getType()][$this->config->getCollection()][$keys[0]], $query]
                : $query;
        } else {
            $this->_builder["query"][$this->config->getType()][$this->config->getCollection()][$keys[0]][$keys[1]] = $push
                ? [...$this->_builder["query"][$this->config->getType()][$this->config->getCollection()][$keys[0]][$keys[1]], $query]
                : $query;
        }

        return $this;
    }

    /**
     * Get raw query.
     */
    public function getQuery(): array {
        return $this->_builder;
    }
}
