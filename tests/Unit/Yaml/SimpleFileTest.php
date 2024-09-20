<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Tests\Unit\Yaml;

use NucleusFramework\Configuration\Adapters\Yaml\Yaml;
use NucleusFramework\Configuration\Reader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SimpleFileTest extends TestCase
{
    public function testFileNotFound(): void
    {
        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Configudation file not nound');

        $read = new Reader(new Yaml());
        $read->readFromFile('/invalid/path/invalid-file.yaml');
    }

    public function testReadFile(): void
    {
        $read = new Reader(new Yaml());
        $read->readFromFile(__DIR__ . '/files/simple.yaml');

        $configurations = $read->collectAll();

        $this->assertIsArray($configurations);
        $this->assertTrue($configurations !== []);
        $this->assertEquals('José Couves', $configurations['name']);
        $this->assertEquals('36', $configurations['age']);
        $this->assertEquals('123 Elm Street', $configurations['street']);
        $this->assertEquals('Arda', $configurations['city']);
        $this->assertEquals('AR', $configurations['state']);
        $this->assertEquals('62704', $configurations['postal_code']);
    }

    public function testFindByKey(): void
    {
        $read = new Reader(new Yaml());
        $read->readFromFile(__DIR__ . '/files/simple.yaml');

        $this->assertEquals('José Couves', $read->get('name'));
    }

    public function testFindInvalidKey(): void
    {
        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(sprintf('The key "foo" not exists'));

        $read = new Reader(new Yaml());
        $read->readFromFile(__DIR__ . '/files/simple.yaml');

        $read->get('foo');
    }
}
