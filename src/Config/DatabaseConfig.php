<?php

namespace LunaQL\Config;

use LunaQL\Contracts\DatabaseConfig as Config;

class DatabaseConfig implements Config {
    /**
     * Create a new database config.
     */
    public function __construct(
        private string $endpoint,
        private string $token
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
}
