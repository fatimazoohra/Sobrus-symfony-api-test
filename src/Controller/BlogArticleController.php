<?php

namespace App\Controller;

use App\Service\BlogArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api", "api_")]
class BlogArticleController extends AbstractController
{
    private $blogArticleService;

    public function __construct(BlogArticleService $blogArticleService) {
        $this->blogArticleService = $blogArticleService;
    }

    #[Route('/blog-articles', name: 'blog_article_list', methods: ["GET"])]
    public function articles(): JsonResponse
    {
        $articles = $this->blogArticleService->getAllArticles();
        return $this->json(
            ["data" => [
                "articles" => $articles
            ]],
            Response::HTTP_OK,
            [],
            [
                'groups' => ['article.show']
            ]
        );
    }

    #[Route('/blog-articles/{article_id}', name: 'blog_article_get', methods: ["GET"])]
    public function getArticle($article_id): JsonResponse
    {
        $article = $this->blogArticleService->getArticle($article_id);
        if(!$article){
            return new JsonResponse([
                'message' => 'No article found with that id.'
            ], Response::HTTP_NOT_FOUND);
        }
        return $this->json(
            ["data" => [
                "article" => $article
            ]],
            Response::HTTP_OK,
            [],
            [
                'groups' => ['article.show']
            ]
        );
    }
    #[Route('/blog-articles/{article_id}', name: 'blog_article_delete', methods: ["DELETE"])]
    public function removeArticle($article_id): JsonResponse
    {
        $response = $this->blogArticleService->deleteArticle($article_id);
        
        if($response == 'not_found'){
            return new JsonResponse([
                'message' => 'No article found with that id.'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse([
            'message' => 'Article deleted successfully.'
        ], Response::HTTP_OK);
    }

    #[Route('/blog-articles', name: 'blog_article_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {

        // Get the request data
        $data = $request->request->all();
        $cover_picture = $request->files->get("coverPicture");
        $returned_data = $this->blogArticleService->createArticle($data, $cover_picture);
        
        if (isset($returned_data["errors"])) {
            return new JsonResponse([
                "errors" => $returned_data["errors"]
            ], Response::HTTP_BAD_REQUEST);
        }
        else if(isset($returned_data["blogArticle"]))
        return $this->json(
            [
                'message' => 'Blog article created successfully.',
                'data' => [
                    "article" => $returned_data["blogArticle"]
                ]
            ], 
            Response::HTTP_CREATED,
            [],
            [
                'groups' => ['article.show']
            ]
        );
    }
}
