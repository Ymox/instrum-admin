<?php

namespace App\Service;

class CsvMapper
{
    private \SplFileObject $file;
    
    private array $mappings;

    private ?string $entity = null;

    public function __construct(string $filePath, array $mappings, ?string $entity = null)
    {
        $this->file = new \SplFileObject($filePath);
        $this->mappings = $mappings;
        $this->entity = $entity;
    }
    
    public function get(int $row)
    {
        for ($i = 0; !$this->file->eof() && $i <= $row; $i++) {
            $data = $this->file->fgetcsv(';');
        }

        $result = [];
        foreach ($this->mappings as $i => $mapping) {
            $path = explode('.', $mapping);
            $pointer = &$result;
            foreach ($path as $depth) {
                if (!isset($pointer[$depth])) {
                    $pointer[$depth] = [];
                }
                $pointer = &$pointer[$depth];
            }
            $pointer = $data[$i];
        }

        return $result;
    }
}