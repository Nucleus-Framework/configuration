<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Tests\Yaml;

use NucleusFramework\Configuration\Adapters\DotEnv;
use NucleusFramework\Configuration\Reader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DotEnvTest extends TestCase
{
    public function testFileNotFound(): void
    {
        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Configudation file not nound');

        $read = new Reader(new DotEnv());
        $read->readFromFile('/invalid/path/invalid-file.env');
    }

    public function testLoadFile(): void
    {
        $reader = new Reader(new DotEnv());
        $reader->readFromFile(__DIR__ . '/files/.env');

        $configuration = $reader->collectAll();

        $this->assertIsArray($configuration);

        $this->assertTrue($configuration !== []);
    }

    public function testLoadEmptyFile(): void
    {
        $reader = new Reader(new DotEnv());
        $reader->readFromFile(__DIR__ . '/files/empty.env');

        $configuration = $reader->collectAll();

        $this->assertIsArray($configuration);

        $this->assertEmpty($configuration);
    }

    public function testCollectAllInfo(): void
    {
        $reader = new Reader(new DotEnv());
        $reader->readFromFile(__DIR__ . '/files/.env');

        $configuration = $reader->collectAll();

        $this->assertIsArray($configuration);

        $this->assertEquals('development', $configuration['APP_ENV']);
        $this->assertEquals('secret', $configuration['APP_SECRET']);
        $this->assertEquals('8080', $configuration['APP_PORT']);
        $this->assertEquals('localhost', $configuration['DB_HOST']);
        $this->assertEquals('1234', $configuration['DB_PORT']);
        $this->assertEquals('exampele', $configuration['DB_NAME']);
        $this->assertEquals('username', $configuration['DB_USER']);
        $this->assertEquals('password', $configuration['DB_PASSWORD']);
        $this->assertEquals('smtp.example.com', $configuration['EMAIL_HOST']);
        $this->assertEquals('587', $configuration['EMAIL_PORT']);
        $this->assertEquals('joseph@example.com', $configuration['EMAIL_USER']);
        $this->assertEquals('password', $configuration['EMAIL_PASSWORD']);
        $this->assertEquals('secret_key', $configuration['API_KEY']);
        $this->assertEquals('https://api.example.com', $configuration['API_URL']);
    }

    public function testGetNonexistentKey(): void
    {
        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('The key "invalid-key" not exists');

        $reader = new Reader(new DotEnv());
        $reader->readFromFile(__DIR__ . '/files/.env');

        $reader->get('invalid-key');
    }

    public function testGetInfo(): void
    {
        $reader = new Reader(new DotEnv());
        $reader->readFromFile(__DIR__ . '/files/.env');

        $appEnv = $reader->get('APP_ENV');

        $this->assertIsString($appEnv);

        $this->assertEquals('development', $appEnv);
    }
}
