<?php

declare(strict_types=1);

namespace NucleusFramework\Configuration\Adapters\Yaml;

class Node
{
    /** @param array<mixed> $content */
    public function __construct(
        private string $name,
        private array $content,
        private bool $containsChildren
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return array<mixed> */
    public function nodes(): array
    {
        return $this->content;
    }

    public function containsChildren(): bool
    {
        return $this->containsChildren;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $result = [];

        foreach (current($this->nodes()) as $key => $value) {
            $explode = explode('.', $key);

            if (count($explode) === 1) {
                $result[$key] = $value;

                continue;
            }

            $item        = $key . "=>{{VALUE}}";
            $replaceNone = str_replace('.', '":{"', $item);
            $finalBrakts = str_repeat("}", count($explode));
            $mountJson   = '{"' . $replaceNone . '"' . $finalBrakts ;
            $json        = str_replace('=>', '":"', $mountJson);
            $json        = str_replace('{{VALUE}}', $value, $json);
            $jsonToArray = json_decode($json, true);

            $result = array_merge_recursive($result, $jsonToArray);
        }

        return $result;
    }
}
