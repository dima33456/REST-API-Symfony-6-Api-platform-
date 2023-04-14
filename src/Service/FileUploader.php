<?php
 
namespace App\Service;
 
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
 
class FileUploader {
    private $uploadPath;
    private $slugger;
    private $urlHelper;
    private $relativeUploadsDir;
    private $imagine;
    private $fullFilePath;
 
    public function __construct($publicPath, $uploadPath, SluggerInterface $slugger, UrlHelper $urlHelper) {
        $this->uploadPath = $uploadPath;
        $this->slugger = $slugger;
        $this->urlHelper = $urlHelper;
        $this->relativeUploadsDir = str_replace($publicPath, '', $this->uploadPath).'/';
        $this->imagine = new Imagine();
    }
 
    public function upload(UploadedFile $file) {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
 
        try {
            $mediaObject = $file->move($this->getUploadPath(), $fileName);

            $this->fullFilePath = $mediaObject->getPathname();

            $this->resizeImg($mediaObject, 0.8);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
 
        return $fileName;
    }
 
    public function getUploadPath() {
        return $this->uploadPath;
    }

    public function getFullFilePath() {
        return $this->fullFilePath;
    }
 
    public function getUrl(?string $fileName, bool $absolute = true) {
        if (empty($fileName)) return null;
 
        if ($absolute) {
            return $this->urlHelper->getAbsoluteUrl($this->relativeUploadsDir.$fileName);
        }
 
        return $this->urlHelper->getRelativePath($this->relativeUploadsDir.$fileName);
    }

    private function resizeImg($file, $size) {
        list($iwidth, $iheight) = getimagesize($file);

        $width = $iwidth * $size;
        $height = $iheight * $size;

        $path = $this->getFullFilePath();
        
        $img = $this->imagine->open($path);

        $img->resize(new Box($width, $height))->save();
    }
}
