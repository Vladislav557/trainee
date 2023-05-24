<?php

namespace App\Service\ParserService;

class Parser
{
    public function __construct(private string $url = '')
    {}

    public function setUrl(string $url): void
    {
        $this->url = $this->prepareUrl($url);
    }

    private function prepareUrl(string $url): string
    {
        $primer = '/uc?export=download&id=';

        [
            'scheme' => $scheme,
            'host' => $host,
            'path' => $path
        ] = parse_url($url);

        $id = explode('/', $path)[3];

        return $scheme . '://' . $host . $primer . $id;
    }

    private function getRawText(): string
    {
        try {
            return file_get_contents($this->url);
        } catch (\Throwable $th) {
            throw new \Exception('Ошибка при чтении файла: ' . $th->getMessage());
        }
    }

    private function toPrepareText(): array
    {
        $rawText = $this->getRawText();
        $rawRows = preg_split('/(\d{6}\|\D)/', $rawText, -1, PREG_SPLIT_DELIM_CAPTURE);

        return array_slice($rawRows, 1);
    } 

    private function getDataInArray(): array
    {
        return array_map(fn($chunk) => $chunk[0] . $chunk[1], array_chunk($this->toPrepareText(), 2));
    }

    private function normalize(string $row): string
    {
        $withoutQuotes = str_replace('"', '', $row);
        $withoutSpace = trim($withoutQuotes);
        $withoutTags = strip_tags($withoutSpace);
        
        return str_replace("\n", '', $withoutTags);
    }

    private function getId(string $row): string
    {
        return explode('|', $row)[0];
    }

    private function getName(string $row): string
    {
        return $this->normalize(explode('|', $row)[1]);
    }

    private function getPrice(string $row): string
    {
        return $this->normalize(explode('|', $row)[2]);
    }

    private function getDescription(string $row): string
    {
        return $this->normalize(explode('|', $row)[3]);
    }

    private function getCategories(string $row): ?array
    {
        $categories = array_slice(explode('|', $row), 4);
        $arrayOfCategories = array_filter($categories, fn($tag) => strlen(trim($tag)));

        $mapped = [];
        foreach ($arrayOfCategories as $index => $value) {
            $name = 'level_' . $index + 1;
            $mapped[$name] = str_replace("\n", '', $value);
        }

        return $mapped;
    }

    private function getInfo(string $row): array
    {
        return [
            'product_sku' => $this->getId($row),
            'name' => $this->getName($row),
            'price' => $this->getPrice($row),
            'detail_text' => $this->getDescription($row),
            ...$this->getCategories($row)
        ];
    }

    public function getData()
    {
        $data = $this->getDataInArray();
        $result = [];

        foreach($data as $product) {
            $result[] = $this->getInfo($product);
        } 

        return $result;
    }

}