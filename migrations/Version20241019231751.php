<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241019231751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_article (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(100) NOT NULL, publication_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', creation_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT DEFAULT NULL, keywords JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', slug VARCHAR(255) NOT NULL, cover_picture_ref VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE blog_article');
    }
}
