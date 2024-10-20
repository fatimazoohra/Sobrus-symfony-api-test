<?php

namespace App\Entity;

use App\Enum\BlogArticleStatus;
use App\Repository\BlogArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BlogArticleRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class BlogArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['article.show'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Author ID cannot be null')]
    #[Groups(['article.show'])]
    private ?int $authorId = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100, maxMessage: 'Title cannot exceed 100 characters')]
    #[Assert\NotBlank(message: 'Title cannot be blank')]
    #[Groups(['article.show'])]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    #[Assert\DateTime(message: 'Invalid datetime format for publication date')]
    #[Groups(['article.show'])]
    private ?\DateTimeImmutable $publicationDate = null;

    #[ORM\Column()]
    #[Groups(['article.show'])]
    private ?\DateTimeImmutable $creationDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['article.show'])]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('array', message: 'Keywords must be an array')]
    #[Groups(['article.show'])]
    private ?array $keywords = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Slug cannot be blank')]
    #[Groups(['article.show'])]
    #[Assert\Length(max: 255, maxMessage: 'Slug cannot exceed 255 characters')]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article.show'])]
    #[Assert\Length(max: 255, maxMessage: 'Cover picture reference cannot exceed 255 characters')]
    private ?string $coverPictureRef = null;

    #[ORM\Column(enumType: BlogArticleStatus::class)]
    #[Assert\NotNull(message: 'Status cannot be null')]
    #[Groups(['article.show'])]
    private ?BlogArticleStatus $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setAuthorId(int $authorId): static
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeImmutable
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeImmutable $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeImmutable $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getKeywords(): ?array
    {
        return $this->keywords;
    }

    public function setKeywords(?array $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCoverPictureRef(): ?string
    {
        return $this->coverPictureRef;
    }

    public function setCoverPictureRef(?string $coverPictureRef): static
    {
        $this->coverPictureRef = $coverPictureRef;

        return $this;
    }

    public function getStatus(): ?BlogArticleStatus
    {
        return $this->status;
    }

    public function setStatus(BlogArticleStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
