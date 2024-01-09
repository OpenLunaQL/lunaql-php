<?php

namespace LunaQL\Contracts;

interface DatabaseConfig {
    public function getEndpoint(): string;
    public function getToken(): string;
}
