<?php

namespace NAO\PlatformBundle\Services\FileUploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class NAOFileUploader{

    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->targetDir, $fileName);

        return $fileName;
    }
}