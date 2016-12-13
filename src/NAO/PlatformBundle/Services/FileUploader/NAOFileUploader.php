<?php

namespace NAO\PlatformBundle\Services\FileUploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class NAOFileUploader{

    private $photoDir;
    private $pdfDir;

    public function __construct($photoDir, $pdfDir)
    {
        $this->photoDir = $photoDir;
        $this->pdfDir = $pdfDir;
    }

    public function upload(UploadedFile $file, $directory)
    {
        if($directory === 'pdfDirectory'){
            $targetDir = $this->pdfDir;
        }else{
            $targetDir = $this->photoDir;
        }

        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($targetDir, $fileName);

        return $fileName;
    }
}