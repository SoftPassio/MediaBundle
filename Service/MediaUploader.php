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

    /**
     * @var array
     */
    private $groups;


    /**
     * MediaUploader constructor.
     *
     * @param string   $targetDirectory
     * @param int|null $maxSize
     * @param array    $allowedMimeTypes
     * @param array    $groups
     */
    public function __construct(string $targetDirectory, ?int $maxSize = null, array $allowedMimeTypes = [], array $groups = [])
    {
        $this->targetDirectory = $targetDirectory;
        $this->maxSize = $maxSize;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->groups = $groups;
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
        $allowedMimeTypes = $this->getAllowedMimeTypes($groupName);
        if (!empty($allowedMimeTypes) && !in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new BadRequestHttpException(sprintf("image mime type (%s) is not valid.", $file->getMimeType()));
        }

        $maxSize = $this->getMaxSize($groupName);
        if ($maxSize) {
            if (!($fileSize = $file->getClientSize())) {
                throw new NotFoundHttpException();
            }

            if ($fileSize > $maxSize) {
                throw new BadRequestHttpException("Upload file can not be bigger than " . $maxSize . " bytes");
            }
        }
    }

    /**
     * @param string $groupName
     *
     * @return array|null
     */
    private function getGroup(string $groupName): ?array
    {
        if (array_key_exists($groupName, $this->groups)) {
            return $this->groups[$groupName];
        }

        return null;
    }

    /**
     * @param null|string $groupName
     *
     * @return array
     */
    private function getAllowedMimeTypes(?string $groupName = null): array
    {
        if (null !== $groupName) {
            if ($group = $this->getGroup($groupName)) {
                return $group['allowed_mime_types'];
            }
        }

        return $this->allowedMimeTypes;
    }

    /**
     * @param null|string $groupName
     *
     * @return int|null
     */
    private function getMaxSize(?string $groupName = null): ?int
    {
        if (null !== $groupName) {
            if ($group = $this->getGroup($groupName)) {
                return $group['max_file_size'];
            }
        }

        return $this->maxSize;
    }
}
