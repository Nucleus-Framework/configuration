<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Adapters;

use NucleusFramework\Configuration\Adapters\Adapter;
use RuntimeException;

class DotEnv implements Adapter
{
    private string $fileContent;

    /** @var array<mixed> */
    private array $parsedData = [];

    public function readFromFile(string $fullPath): void
    {
        if (file_exists($fullPath) === false) {
            throw new RuntimeException('File not exists');
        }

        $fileContent = file_get_contents($fullPath);

        if ($fileContent === false) {
            throw new RuntimeException('Could not read file');
        }

        $this->fileContent = $fileContent;
    }

    /** @return array<string,string> */
    public function parse(): array
    {
        return $this->separateIntoBlocks($this->fileContent);
    }

    /** @return array<string,string> */
    private function separateIntoBlocks(string $dotEnv): array
    {
        $lines = explode(PHP_EOL, trim($dotEnv));

        foreach ($lines as $line) {
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $explode = explode('=', $line);

            $key   = $explode[0];
            $value = $explode[1];

            $this->parsedData[$key] = $value;
        }

        return $this->parsedData;
    }
}
