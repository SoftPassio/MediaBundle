<?php

namespace AppVerk\MediaBundle\Controller;

use AppVerk\MediaBundle\Doctrine\MediaManager;
use AppVerk\MediaBundle\Service\MediaUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/media")
 */
class MediaController extends AbstractController
{
    /**
     * @Route("/upload", name="upload_media", methods={"POST"})
     */
    public function uploadAction(Request $request, MediaUploader $mediaUploader, MediaManager $mediaManager)
    {
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            $fileName = $mediaUploader->upload($file);
            $media = $mediaManager->createMedia($file, $fileName);

            $output['fileName'] = $media->getFileName();
            $output['id'] = $media->getId();

            return new JsonResponse($output);
        }

        return new Response('', Response::HTTP_BAD_REQUEST);
    }
}
