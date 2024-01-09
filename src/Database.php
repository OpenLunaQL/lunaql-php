<?php

namespace LunaQL;

use LunaQL\Builder\CollectionBuilder;
use LunaQL\Builder\DocumentBuilder;
use LunaQL\Config\DatabaseConfig;
use LunaQL\Config\DocumentConfig;

class Database {
    /**
     * Collection builder instance.
     */
    private CollectionBuilder $collectionBuilder;

    /**
     * Create a new database instance.
     */
    public function __construct (
        private DatabaseConfig $config
    ) {
        $this->collectionBuilder = new CollectionBuilder($config);
    }

    /**
     * Create query.
     */
    public function query(): CollectionBuilder {
        return $this->collectionBuilder;
    }

    /**
     * Insert new document.
     */
    public function insert(array $data, array $options = []) {
        if (isset($data[0])) {
            throw new \Exception('You can only insert one document at a time.');
        }

        return new DocumentBuilder(
            new DocumentConfig(
                endpoint: $this->config->getEndpoint(),
                token: $this->config->getToken(),
                type: 'document',
                data: [
                    'data' => $data,
                    'options' => $options,
                ],
            )
        );
    }

    /**
     * Insert new documents.
     */
    public function insertMany(array $data, array $options = []) {
        if (!isset($data[0])) {
            throw new \Exception('You can only insert multiple documents.');
        }

        return new DocumentBuilder(
            new DocumentConfig(
                endpoint: $this->config->getEndpoint(),
                token: $this->config->getToken(),
                type: 'documents',
                data: [
                    'data' => $data,
                    'options' => $options,
                ],
            )
        );
    }
}
