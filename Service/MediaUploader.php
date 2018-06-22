<?php

namespace AppVerk\MediaBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaUploader
{
    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var array
     */
    private $allowedExtensions;

    /**
     * @var int
     */
    private $maxSize;

    public function __construct(string $targetDirectory, int $maxSize = null, array $allowedExtensions = [])
    {
        $this->targetDirectory = $targetDirectory;
        $this->maxSize = $maxSize;
        $this->allowedExtensions = $allowedExtensions;
    }

    public function upload(UploadedFile $file)
    {
        $this->validate($file);

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($this->targetDirectory, $fileName);

        return $fileName;
    }

    private function validate(UploadedFile $file)
    {
        if (!empty($this->allowedExtensions) && !in_array($file->guessExtension(), $this->allowedExtensions)) {
            throw new BadRequestHttpException("image extension is not valid.");
        }

        if ($this->maxSize) {
            if (!($fileSize = $file->getClientSize())) {
                throw new NotFoundHttpException();
            }

            if ($fileSize > $this->maxSize) {
                throw new BadRequestHttpException("Upload file can not be bigger than " . $this->maxSize . " bytes");
            }
        }
    }
}
