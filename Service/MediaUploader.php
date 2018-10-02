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
    private $allowedMimeTypes;

    /**
     * @var int
     */
    private $maxSize;

    public function __construct(string $targetDirectory, int $maxSize = null, array $allowedMimeTypes = [])
    {
        $this->targetDirectory = $targetDirectory;
        $this->maxSize = $maxSize;
        $this->allowedMimeTypes = $allowedMimeTypes;
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
        if (!empty($this->allowedMimeTypes) && !in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new BadRequestHttpException(sprintf("image mime type (%s) is not valid.", $file->getMimeType()));
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
