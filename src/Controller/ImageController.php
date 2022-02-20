<?php

namespace App\Controller;

use App\Service\ImageResizeHandler;
use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{

    private $imageResizeService;
    protected $params;

    public function __construct(ParameterBagInterface $params, ImageResizeHandler $imageResizeService) {
        $this->params = $params;
        $this->imageResizeService = $imageResizeService;
    }

    /**
     * @Route("/image-resize", name="image")
     */
    public function index(Request $request): Response
    {

        if (!empty($file = $request->files->get("image")) && !empty($size = $request->get("size"))) {
            if($request->get("size") !== "1" && $request->get("size") !== "2") {
                return $this->json([
                    'message' => 'Please select a valid size. 1 = 310x150 // 2 = 1920x1080',
                ]);
            }

            $validExtensions = ["webp"];
            if(!in_array($file->getClientOriginalExtension(), $validExtensions)) {
                return $this->json([
                    'message' => 'Please use a valid image (webp)',
                ]);
            }

            $urlPath = $this->imageResizeService->processImage($file, $size);

            if($urlPath) {
                return $this->json([
                    "status" => "You can see your image at " . $urlPath
                ]);
            }else{
                return $this->json([
                    'message' => 'Something went wrong :( please make sure you are uploading a file with less than 204800 bytes.',
                ]);
            }
        }
        return $this->json([
            'message' => 'Please upload a image and select a size (1 or 2) into this endpoint :)',
        ]);
    }
}
