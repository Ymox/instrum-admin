<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private string $uploadPath;

    private SluggerInterface $slugger;

    public function __construct(string $importUploadPath, SluggerInterface $slugger)
    {
        $this->uploadPath = $importUploadPath;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->uploadPath, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $this->uploadPath . '/' . $fileName;
    }

    public function getUploadPath(): string
    {
        return $this->uploadPath;
    }
}
