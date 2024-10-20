<?php

namespace App\Service;

use App\Entity\BlogArticle;
use App\Enum\BlogArticleStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\MediaService;

class BlogArticleService {
    
    private $em, $blogArticleRepo, $validator;
    private $mediaService;
    private $commonService;

    public function __construct( 
        EntityManagerInterface $em, 
        ValidatorInterface $validator, 
        MediaService $mediaService, 
        CommonService $commonService 
    ) {

        $this->em = $em;
        $this->validator = $validator;
        $this->mediaService = $mediaService;
        $this->commonService = $commonService;
        $this->blogArticleRepo = $this->em->getRepository(BlogArticle::class);
        // $this->em->getFilters()->enable('softdeleteable');
    }

    public function getAllArticles(){
        return $this->blogArticleRepo->findAll();
    }

    public function getArticle($article_id){
        $article = $this->blogArticleRepo->findOneBy([
            "id" => $article_id
        ]);

        return $article;
    }

    public function deleteArticle($article_id){
        $article = $this->blogArticleRepo->findOneBy([
            "id" => $article_id
        ]);

        if(!$article){
            return 'not_found';
        }else{
            $this->em->remove($article);
            $this->em->flush();
            return 'deleted';
        }
        
    }

    public function createArticle($data, $cover_picture){
        
        $errorMessages = [];
        $blogArticle = new BlogArticle();
        
        if($data['authorId'])
        $blogArticle->setAuthorId((int)$data['authorId']);
        $blogArticle->setTitle($data['title']);
        $blogArticle->setCreationDate(new \DateTimeImmutable());
        $blogArticle->setContent($data['content']);           

        $blogArticle->setStatus(BlogArticleStatus::DRAFT);
        $blogArticle->setSlug('article-'.str_replace(' ', '-', $data['title']));
        
        if($data['content'] && !empty($data['content'])){
            $keywords = $this->commonService->frequentlyOccuringWords( $data['content'], 3);
            if($keywords['isBanned']){
                $errorMessages[] = [
                    'property' => 'content',
                    'message' => 'the content contains banned words',
                ]; 
            }
            $blogArticle->setKeywords($keywords['keywords']);
        }

        // Validate the entity
        $errors = $this->validator->validate($blogArticle);
        if (count($errors) > 0 ) {
    
            foreach ($errors as $error) {
                $errorMessages[] = [
                    'property' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }
        }
        if(count($errorMessages) > 0 ){
            return [
                "errors" => $errorMessages
            ];
        }
        
        if($cover_picture){
            $fileName = $this->mediaService->createCoverPicture($cover_picture);
            $blogArticle->setCoverPictureRef($fileName);
        }


        $this->em->persist($blogArticle);
        $this->em->flush();
        
        return [
            "blogArticle" => $blogArticle
        ];
    }
}