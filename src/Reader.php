<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration;

use NucleusFramework\Configuration\Adapters\Adapter;
use RuntimeException;

final class Reader
{
    private array $content = [];

    public function __construct(private Adapter $adapter)
    {}

    public function get(string $key): string|array
    {
        if (array_key_exists($key, $this->content) === false) {
            throw new RuntimeException(sprintf('The key "%s" not exists', $key));
        }

        return $this->content[$key];
    }

    public function collectAll(): array
    {
        return $this->content;
    }

    public function readFromFile(string $fullPath): void
    {
        if (file_exists($fullPath) === false) {
            throw new RuntimeException('Configudation file not nound');
        }

        $this->adapter->readFromFile($fullPath);

        $this->content = $this->adapter->parse();
    }
}