<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Adapters;

interface Adapter
{
    public function readFromFile(string $fullPath): void;

    /** @return array<mixed> */
    public function parse(): array;
}
