<?php

namespace LunaQL\Builder;

use Closure;
use GuzzleHttp\Client;
use LunaQL\Config\DatabaseConfig;
use LunaQL\Config\RelationshipConfig;

class QueryBuilder {
    /**
     * Raw query.
     */
    private array $_builder = [
        "query" => [ "from" => [] ]
    ];

    /**
     * Create a new query builder.
     */
    public function __construct(
        private DatabaseConfig $config,
        private string $collection
    ) {
        $this->_builder["query"]["from"][$collection] = [];
    }

    /**
     * Select fields from the collection.
     */
    public function select(array $fields): QueryBuilder {
        return $this->updateQuery("select", $fields);
    }

    /**
     * Hide fields from the collection.
     */
    public function hidden(array $fields): QueryBuilder {
        return $this->updateQuery("hidden", $fields);
    }

    /**
     * Filter the collection.
     */
    public function where(string $field, string $operator, mixed $value): QueryBuilder {
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
    public function orWhere(string $field, string $operator, mixed $value): QueryBuilder {
        return $this->updateQuery(
            keys: "orWhere",
            query: [ $field, $operator, $value ],
            create: true,
            push: true,
        );
    }

    /**
     * Order the collection.
     */
    public function orderBy(string $field, string $direction = "asc"): QueryBuilder {
        return $this->updateQuery("orderBy", $field)->sort($direction);
    }

    /**
     * Sort the collection.
     */
    public function sort($direction = "asc"): QueryBuilder {
        return $this->updateQuery("sort", $direction);
    }

    /**
     * Group the collection.
     *
     * @param string[] $fields
     */
    public function groupBy(array $fields): QueryBuilder {
        return $this->updateQuery("groupBy", $fields);
    }

    /**
     * Filter the collection.
     */
    public function having(string $field, string $operator, mixed $value): QueryBuilder {
        return $this->updateQuery("having", [
            $field, $operator, $value
        ], true);
    }

    /**
     * Limit the collection.
     */
    public function limit(int $limit): QueryBuilder {
        return $this->updateQuery("limit", $limit);
    }

    /**
     * Skip documents in the collection.
     */
    public function skip(int $skip): QueryBuilder {
        return $this->updateQuery("skip", $skip);
    }

    /**
     * Join collection to another collection.
     */
    public function hasMany(string $collection, Closure $callback): QueryBuilder {
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
    public function belongsTo(string $collection, callable $callback): QueryBuilder {
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
     * Delete documents in the collection.
     */
    public function delete() {
        $this->updateQuery("do", "delete");

        return $this->persist();
    }

    /**
     * Count documents in the collection.
     */
    public function count() {
        $this->updateQuery("do", "count");

        return $this->persist();
    }

    /**
     * Check if documents exist in the collection.
     */
    public function exists() {
        $this->updateQuery("do", "exists");

        return $this->persist();
    }

    /**
     * Pluck a property from the collection.
     */
    public function list(?string $property = null): array {
        $this->updateQuery("do", "list");

        if ($property && $property !== "") {
            $this->updateQuery("listBy", $property);
        }

        return $this->persist();
    }

    /**
     * Fetch documents from the collection.
     */
    public function fetch() {
        $this->updateQuery("do", "fetch");

        return $this->persist();
    }

    /**
     * Fetch the first document from the collection.
     */
    public function fetchFirst() {
        $this->updateQuery("do", "fetchFirst");

        return $this->persist();
    }

    /**
     * Update documents in the collection.
     */
    public function update(array $data) {
        $this->updateQuery("data", $data);
        $this->updateQuery("do", "fetchFirst");

        return $this->persist();
    }

    /**
     * Get raw query.
     */
    public function getQuery(): array {
        return $this->_builder;
    }

    /**
     * Update query.
     */
    private function updateQuery(string | array $keys, mixed $query, bool $create = false, bool $push = false) {
        if (is_string($keys)) {
            $keys = [ $keys ];
        }

        if (!isset($this->_builder["query"]["from"][$this->collection][$keys[0]]) && $create) {
            $this->_builder["query"]["from"][$this->collection][$keys[0]] = [];
        }

        if (count($keys) === 1) {
            $this->_builder["query"]["from"][$this->collection][$keys[0]] = $push
                ? [...$this->_builder["query"]["from"][$this->collection][$keys[0]], $query]
                : $query;
        } else {
            $this->_builder["query"]["from"][$this->collection][$keys[0]][$keys[1]] = $push
                ? [...$this->_builder["query"]["from"][$this->collection][$keys[0]][$keys[1]], $query]
                : $query;
        }

        return $this;
    }

    /**
     * Persist query to the database.
     */
    private function persist() {
        $client = new Client();

        $response = $client->request(
            "POST", $this->config->getEndpoint(), [
                "json" => $this->getQuery(),
                "headers" => [ "Authorization" => "Bearer " . $this->config->getToken() ]
            ]
        );

        return json_decode((string) $response->getBody())->{$this->collection};
    }
}
