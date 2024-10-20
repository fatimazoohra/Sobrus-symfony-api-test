<?php

namespace App\Controller;

use App\Entity\BlogArticle;
use App\Service\BlogArticleService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route("/api", "api_")]
class BlogArticleController extends AbstractController
{
    private $blogArticleService;

    public function __construct(BlogArticleService $blogArticleService) {
        $this->blogArticleService = $blogArticleService;
    }

    #[OA\Get(
        summary: 'Get list of articles',
        description: 'Retrieve blog articles.',
        operationId: 'getArticles',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Page number for pagination',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                required: false,
                description: 'Number of items per page',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'articles',
                                    type: 'array',
                                    items: new OA\Items(ref: new Model(type: BlogArticle::class, groups: ['article.show'], serializationContext: ["useJms" => false]))
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request parameters'
            )
        ]
    )]
    #[Route('/blog-articles', name: 'blog_article_list', methods: ["GET"])]
    public function articles(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $articles = $this->blogArticleService->getAllArticles($page, $limit);
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

    #[OA\Get(
        summary: 'Get a single article by ID',
        description: 'Retrieve the details of a specific blog article by its ID.',
        operationId: 'getArticle',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'The ID of the article to retrieve',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(
                    ref: new Model(type: BlogArticle::class, groups: ['article.show'])
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Article not found'
            )
        ]
    )]
    #[Route('/blog-articles/{id}', name: 'blog_article_get', methods: ["GET"])]
    public function getArticle($id): JsonResponse
    {
        $article = $this->blogArticleService->getArticle($id);
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

    #[OA\Delete(
        summary: 'Delete an article by ID',
        description: 'Soft delete a specific blog article by its ID.',
        operationId: 'deleteArticle',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'The ID of the article to delete',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Article successfully deleted'
            ),
            new OA\Response(
                response: 404,
                description: 'Article not found'
            )
        ]
    )]
    #[Route('/blog-articles/{id}', name: 'blog_article_delete', methods: ["DELETE"])]
    public function removeArticle($id): JsonResponse
    {
        $response = $this->blogArticleService->deleteArticle($id);
        
        if($response == 'not_found'){
            return new JsonResponse([
                'message' => 'No article found with that id.'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse([
            'message' => 'Article deleted successfully.'
        ], Response::HTTP_OK);
    }

    #[OA\Post(
        summary: 'Create a new blog article',
        description: 'Creates a new blog article with the provided data, including a cover picture upload.',
        tags: ['Articles'],
        requestBody: new OA\RequestBody(
            description: 'Blog article data',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'authorId', type: 'integer', example: 1),
                    new OA\Property(property: 'title', type: 'string', example: 'New Blog Post'),
                    new OA\Property(property: 'content', type: 'string', example: 'This is the content of the blog article.'),
                    new OA\Property(property: 'coverPicture', type: 'string', format: 'binary', description: 'Upload cover picture')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Blog article created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Blog article created successfully.'),
                        new OA\Property(property: 'data', properties: [
                            new OA\Property(property: 'article', ref: new Model(type: BlogArticle::class, groups: ['article.show']))
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request data',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            )
        ]
    )]
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

    #[OA\Patch(
        summary: 'Update an existing blog article',
        description: 'Updates the specified fields of an existing blog article.',
        tags: ['Articles'],
        requestBody: new OA\RequestBody(
            description: 'Blog article data to update',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'authorId', type: 'integer', example: 1),
                    new OA\Property(property: 'title', type: 'string', example: 'Updated Blog Post Title'),
                    new OA\Property(property: 'content', type: 'string', example: 'This is the updated content of the blog article.'),
                    new OA\Property(property: 'coverPicture', type: 'string', format: 'binary', description: 'Upload new cover picture (optional)'),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'published', 'deleted'], example: 'published')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Blog article updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Blog article updated successfully.'),
                        new OA\Property(property: 'data', properties: [
                            new OA\Property(property: 'article', ref: new Model(type: BlogArticle::class, groups: ['article.show']))
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request data',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Blog article not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Blog article not found.')
                    ]
                )
            )
        ]
    )]    
    #[Route('/blog-articles/{id}', name: 'blog_article_update', methods: ['PATCH','POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $article = $this->blogArticleService->getArticle($id);
        if(!$article){
            return new JsonResponse([
                'message' => 'No article found with that id.'
            ], Response::HTTP_NOT_FOUND);
        }
        // Get the request data
        $data = $request->request->all();
        $cover_picture = $request->files->get("coverPicture");

        // Update the blog article
        $returned_data = $this->blogArticleService->updateArticle($article, $data, $cover_picture);

        if (isset($returned_data["errors"])) {
            return new JsonResponse([
                "errors" => $returned_data["errors"]
            ], Response::HTTP_BAD_REQUEST);
        } else if (isset($returned_data["blogArticle"])) {
            return $this->json(
                [
                    'message' => 'Blog article updated successfully.',
                    'data' => [
                        "article" => $returned_data["blogArticle"]
                    ]
                ],
                Response::HTTP_OK,
                [],
                [
                    'groups' => ['article.show']
                ]
            );
        }
    }

}
