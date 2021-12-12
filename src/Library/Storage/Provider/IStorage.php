<?php

namespace App\Library\Storage\Provider;

interface IStorage
{
    public function write(array $data, $bulk = false): bool;
    public function read(): array;
    public function isAvailable(): bool;
    public function purge(): void;
    public function outputPath(): string;
}
