<?php
namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class MediaService {
    private $uploadedPicsDir;

    public function  __construct(KernelInterface $app) {
        $this->uploadedPicsDir = $app->getProjectDir() . "/public/uploaded_pictures";
        // create the dir if doesnt exist
        if (!is_dir($this->uploadedPicsDir)) {
            mkdir($this->uploadedPicsDir, 0705, true);
        }
    }

    public function createCoverPicture($cover_picture){
        $fileName = uniqid() . '.' . $cover_picture->guessExtension();
        $cover_picture->move($this->uploadedPicsDir, $fileName);
        return $fileName;                
    }

    public function deleteMedia($file_name) {
        $file_url =$this->uploadedPicsDir ."/".$file_name;
        unlink( $file_url );
    }

}