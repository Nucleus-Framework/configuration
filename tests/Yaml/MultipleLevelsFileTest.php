<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Tests\Yaml;

use NucleusFramework\Configuration\Adapters\Yaml\Yaml;
use NucleusFramework\Configuration\Reader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MultipleLevelsFileTest extends TestCase
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
        $reader = new Reader(new Yaml());
        $reader->readFromFile(__DIR__ . '/files/multiple-levels.yaml');

        $configurationList = $reader->collectAll();

        $this->assertTrue($configurationList !== []);
    }

    public function testReadAppInfo(): void
    {
        $reader = new Reader(new Yaml());
        $reader->readFromFile(__DIR__ . '/files/multiple-levels.yaml');

        $configurationList = $reader->collectAll();

        $this->assertArrayHasKey('app', $configurationList);

        $appInfo = $configurationList['app'];
        $this->assertEquals('MyAwesomeApp', $appInfo['name']);
        $this->assertEquals('1.0.0', $appInfo['version']);
        $this->assertEquals(
            'This is a sample configuration file for MyAwesomeApp.',
            $appInfo['description']
        );
        $this->assertEquals('MIT', $appInfo['license']);
    }

    public function testReadDatabaseInfo(): void
    {
        $reader = new Reader(new Yaml());
        $reader->readFromFile(__DIR__ . '/files/multiple-levels.yaml');

        $configurationList = $reader->collectAll();

        $databaseInfo = $configurationList['database'];
        $this->assertArrayHasKey('database', $configurationList);
        $this->assertEquals('postgres', $databaseInfo['type']);
        $this->assertEquals('localhost', $databaseInfo['host']);
        $this->assertEquals('5432', $databaseInfo['port']);
        $this->assertEquals('admin', $databaseInfo['username']);
        $this->assertEquals('password', $databaseInfo['password']);
        $this->assertEquals('my_database', $databaseInfo['name']);
    }

    public function testReadServerInfo(): void
    {
        $reader = new Reader(new Yaml());
        $reader->readFromFile(__DIR__ . '/files/multiple-levels.yaml');

        $configurationList = $reader->collectAll();

        $this->assertArrayHasKey('server', $configurationList);

        $serverInfo = $configurationList['server'];
        $this->assertEquals('localhost', $serverInfo['host']);
        $this->assertEquals('8080', $serverInfo['port']);
        $this->assertEquals('true', $serverInfo['debug']);

        $this->assertArrayHasKey('logging', $serverInfo);

        $loggingInfo = $serverInfo['logging'];
        $this->assertEquals('info', $loggingInfo['level']);
        $this->assertEquals('/var/log/myawesomeapp.log', $loggingInfo['file']);

        $this->assertArrayHasKey('rotate', $loggingInfo);

        $rotate = $loggingInfo['rotate'];
        $this->assertEquals('10MB', $rotate['max_size']);
        $this->assertEquals('5', $rotate['max_files']);
    }
}
