<?php

namespace App\Service;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\S3Control\Exception\S3ControlException;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Imagick\Imagine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ImageResizeHandler
{
    private $awsBucketService;
    private $container;

    private $fullPath;

    public function __construct(ContainerInterface $container, AwsBucketHandler $awsBucketService)
    {
        $this->container = $container;
        $this->awsBucketService = $awsBucketService;
    }

    public function processImage($imageFile, $size)
    {
        $path = $this->container->getParameter('kernel.project_dir') . "/var/uploads/" . uniqid() . "/";
        $this->fullPath = $path . $imageFile->getClientOriginalName();
        $imageFile->move($path, $imageFile->getClientOriginalName());

        if(filesize($this->fullPath) > 204800) {
            return false;
        }

        $resizedImage = $this->resizeImage($size);

        try {
            return $this->awsBucketService->putObjectS3("3cket-intro/", $resizedImage);
        }catch (S3Exception $exception) {
            return false;
        }


    }

    public function resizeImage($size) {
        $sizesSupported = [
            "1" => [310, 150],
            "2" => [1920, 1080]
        ];

        $imagine = new Imagine();
        $image = $imagine->open($this->fullPath);

        $width = $sizesSupported[$size][0];
        $height = $sizesSupported[$size][1];

        try {
            $size = new Box($width, $height);
        }catch (\InvalidArgumentException $exception) {
            return new JsonResponse([
                "message" => "You can't insert a negative number or a letter in the size :)"
            ], 400);
        }

        $resizedImagePath = pathinfo($this->fullPath, PATHINFO_DIRNAME) . "/". pathinfo($this->fullPath, PATHINFO_FILENAME) .
        "_resized." . pathinfo($this->fullPath, PATHINFO_EXTENSION);

        $image->resize($size)
              ->save($resizedImagePath);

        return $resizedImagePath;
    }



}
