<?php

namespace SoftPassio\MediaBundle\Doctrine;

use SoftPassio\MediaBundle\Entity\Media;
use SoftPassio\Components\Doctrine\AbstractManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager extends AbstractManager implements MediaManagerInterface
{
    public function createMedia(UploadedFile $uploadedFile, string $fileName, string $filePath)
    {
        /** @var Media $media */
        $media = new $this->className();
        $media->setName($uploadedFile->getClientOriginalName());
        $media->setFileName($fileName);
        $media->setSize(filesize($filePath.'/'.$fileName));
        $media->setMimeType($uploadedFile->getClientMimeType());

        $this->persistAndFlush($media);

        return $media;
    }
}
