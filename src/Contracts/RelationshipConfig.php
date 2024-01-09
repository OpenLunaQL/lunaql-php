<?php

namespace LunaQL\Contracts;

interface RelationshipConfig {
    public function getCollection(): string;
    public function getType(): string;
}
