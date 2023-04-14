<?php
namespace App\Controller\Image;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use App\Entity\Image\Image;
use App\Service\FileUploader;
 
#[AsController]
final class ImageUploadController extends AbstractController {
    public function __invoke(Request $request, FileUploader $fileUploader): Image {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedHttpException("Объект пользователя отсутствует, куда вы его дели");
        }

        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('Файл с изображением отсутствует');
        }
 
        $img = new Image();

        $img->setUrl($fileUploader->getUrl($fileUploader->upload($uploadedFile)));
        $img->setPath($fileUploader->getFullFilePath());
        $img->setDescription($request->get('description'));
        $img->setUser($user);

        return $img;
    }
}
