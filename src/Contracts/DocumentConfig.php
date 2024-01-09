<?php

namespace LunaQL\Contracts;

interface DocumentConfig {
    public function getEndpoint(): string;
    public function getToken(): string;
    public function getData(): array;
    public function getType(): string;
}
