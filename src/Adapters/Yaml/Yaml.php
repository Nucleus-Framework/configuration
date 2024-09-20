<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Adapters\Yaml;

use NucleusFramework\Configuration\Adapters\Adapter;
use NucleusFramework\Configuration\Adapters\Yaml\Node;
use RuntimeException;

class Yaml implements Adapter
{
    private string $fileContent;

    /** @var array<mixed> */
    private array $parsedData = [];

    /** @var array<mixed> */
    private array $contentBlocks = [];

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

    public function parse(): array
    {
        $blocks = $this->separateIntoBlocks($this->fileContent);

        foreach ($blocks as $node => $block) {
            $node = $this->parserBlock($node, $block);

            $convert = $node->toArray();

            $this->parsedData = array_merge($this->parsedData, $convert);
        }

        return $this->parsedData;
    }

    /** @param array<mixed> $block */
    private function parserBlock(string $node, array $block): Node
    {
        $previousNode = '';
        $isChildren   = false;
        $parsedBlock  = [];

        foreach ($block as $line) {
            $content = $this->parserBlockLine($line);

            $this->addIfSimpleNode($node, $content, $parsedBlock, $isChildren);

            $this->addIfFatherNode($content, $previousNode, $isChildren);

            $this->addIfChildToFatherNode(
                $node,
                $content,
                $parsedBlock,
                $previousNode,
                $isChildren
            );
        }

        return new Node($node, $parsedBlock, $isChildren);
    }

    /**
     * @param array<string,mixed> $content
     * @param array<string,string> $parsedBlock
     */
    private function addIfSimpleNode(
        string $node,
        array $content,
        array &$parsedBlock,
        bool &$isChildren
    ): void {
        $isSimpleNode = $content['isChildren'] === false && $isChildren === false;

        if ($isSimpleNode) {
            $parsedBlock[$node][$content['key']] = $content['value'];
        }
    }

    /** @param array<string,mixed> $content */
    private function addIfFatherNode(
        array $content,
        string &$previousNode,
        bool &$isChildren
    ): void {
        $isFatherNode = $content['isChildren'] === true && $content['value'] === "";

        if ($isFatherNode) {
            $indice = "{$previousNode}.{$content['key']}";

            $previousNode = $indice;

            $isChildren = true;
        }
    }

    /**
     * @param array<string,mixed> $content
     * @param array<string,mixed> $parsedBlock
     */
    private function addIfChildToFatherNode(
        string &$node,
        array $content,
        array &$parsedBlock,
        string &$previousNode,
        bool &$isChildren
    ): void {
        $isChildToFatherNode = $content['isChildren'] === false
            && $content['value'] !== ""
            && $isChildren === true;

        if ($isChildToFatherNode) {
            $indice = "{$previousNode}.{$content['key']}";

            $indice = str_starts_with($indice, '.')
                ? substr($indice, 1, mb_strlen($indice))
                : $indice;

            $parsedBlock[$node][$indice] = $content['value'];
        }
    }

    /** @return array<mixed> */
    private function parserBlockLine(string $line): array
    {
        $explode = explode(':', $line);
        $key     = trim($explode[0]);
        $value   = null;
        if (array_key_exists(0, $explode) === true) {
            $value = trim($explode[1]);
        }

        return [
            'key'        => $key,
            'value'      => $value,
            'isChildren' => $value === ""
        ];
    }

    /** @return array<mixed> */
    private function separateIntoBlocks(string $yaml): array
    {
        $lines    = explode(PHP_EOL, trim($yaml));
        $baseNode = '';
        foreach ($lines as $line) {
            $indent = mb_strlen($line) - mb_strlen(ltrim($line));

            if (trim($line) === '' || str_contains($line, '#')) {
                continue;
            }

            $explode = explode(':', $line, 2);
            $key     = trim($explode[0]);

            if ($indent === 0) {
                $baseNode = $key;

                $this->contentBlocks[$baseNode][] = $line;

                continue;
            }

            $this->contentBlocks[$baseNode][] = $line;
        }

        return $this->contentBlocks;
    }
}
