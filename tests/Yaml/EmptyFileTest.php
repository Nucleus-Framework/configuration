<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Tests\Yaml;

use NucleusFramework\Configuration\Adapters\Yaml\Yaml;
use NucleusFramework\Configuration\Reader;
use PHPUnit\Framework\TestCase;

final class EmptyFileTest extends TestCase
{
    public function testLoadEmptyFile(): void
    {
        $reader = new Reader(new Yaml());
        $reader->readFromFile(__DIR__ . '/files/empty.yaml');

        $configuration = $reader->collectAll();

        $this->assertIsArray($configuration);

        $this->assertEmpty($configuration);
    }
}
