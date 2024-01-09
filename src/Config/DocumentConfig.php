<?php

namespace LunaQL\Config;

use LunaQL\Contracts\DocumentConfig as Config;

class DocumentConfig implements Config {
    /**
     * Create a new document config.
     */
    public function __construct(
        private string $endpoint,
        private string $token,
        private string $type,
        private array $data,
    ) {}

    /**
     * Get the endpoint.
     */
    public function getEndpoint(): string {
        return $this->endpoint;
    }

    /**
     * Get the token.
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * Get the type.
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * Get the data.
     */
    public function getData(): array {
        return $this->data;
    }
}
