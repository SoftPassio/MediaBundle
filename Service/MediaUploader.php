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
     * @var MediaValidation
     */
    private $mediaValidation;


    /**
     * MediaUploader constructor.
     *
     * @param string          $targetDirectory
     * @param MediaValidation $mediaValidation
     */
    public function __construct(string $targetDirectory, MediaValidation $mediaValidation)
    {
        $this->targetDirectory = $targetDirectory;
        $this->mediaValidation = $mediaValidation;
    }

    /**
     * @param UploadedFile $file
     * @param null|string  $groupName
     *
     * @return string
     */
    public function upload(UploadedFile $file, ?string $groupName = null)
    {
        $this->validate($file, $groupName);

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($this->targetDirectory, $fileName);

        return $fileName;
    }

    /**
     * @param UploadedFile $file
     * @param null|string  $groupName
     */
    private function validate(UploadedFile $file, ?string $groupName = null): void
    {
        $allowedMimeTypes = $this->mediaValidation->getAllowedMimeTypes($groupName);
        if (!empty($allowedMimeTypes) && !in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new BadRequestHttpException(sprintf("image mime type (%s) is not valid.", $file->getMimeType()));
        }

        $maxSize = $this->mediaValidation->getMaxSize($groupName);
        if ($maxSize) {
            if (!($fileSize = $file->getClientSize())) {
                throw new NotFoundHttpException();
            }

            if ($fileSize > $maxSize) {
                throw new BadRequestHttpException("Upload file can not be bigger than " . $maxSize . " bytes");
            }
        }
    }
}
