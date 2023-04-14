<?php
namespace App\Controller\Image;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

use App\Entity\Image\Image;
 
#[AsController]
final class ImageDeleteController extends AbstractController {
    public function __invoke(Image $img): Image {
        unlink($img->getPath()); // это реально работает 😦
        return $img;
    }
}