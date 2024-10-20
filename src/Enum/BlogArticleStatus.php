<?php
namespace App\Enum;

enum BlogArticleStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case DELETED = 'deleted';
}