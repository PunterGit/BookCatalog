<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190908124541 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE authors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_authors (id_author_id INT NOT NULL, id_book_id INT NOT NULL, INDEX IDX_1D2C02C776404F3C (id_author_id), INDEX IDX_1D2C02C7C83F1AF1 (id_book_id), PRIMARY KEY(id_author_id, id_book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_genres (id_genre_id INT NOT NULL, id_book_id INT NOT NULL, INDEX IDX_813CEE9B124D3F8A (id_genre_id), INDEX IDX_813CEE9BC83F1AF1 (id_book_id), PRIMARY KEY(id_genre_id, id_book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE books (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, pub_date VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genres (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_authors ADD CONSTRAINT FK_1D2C02C776404F3C FOREIGN KEY (id_author_id) REFERENCES authors (id)');
        $this->addSql('ALTER TABLE book_authors ADD CONSTRAINT FK_1D2C02C7C83F1AF1 FOREIGN KEY (id_book_id) REFERENCES books (id)');
        $this->addSql('ALTER TABLE book_genres ADD CONSTRAINT FK_813CEE9B124D3F8A FOREIGN KEY (id_genre_id) REFERENCES genres (id)');
        $this->addSql('ALTER TABLE book_genres ADD CONSTRAINT FK_813CEE9BC83F1AF1 FOREIGN KEY (id_book_id) REFERENCES books (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_authors DROP FOREIGN KEY FK_1D2C02C776404F3C');
        $this->addSql('ALTER TABLE book_authors DROP FOREIGN KEY FK_1D2C02C7C83F1AF1');
        $this->addSql('ALTER TABLE book_genres DROP FOREIGN KEY FK_813CEE9BC83F1AF1');
        $this->addSql('ALTER TABLE book_genres DROP FOREIGN KEY FK_813CEE9B124D3F8A');
        $this->addSql('DROP TABLE authors');
        $this->addSql('DROP TABLE book_authors');
        $this->addSql('DROP TABLE book_genres');
        $this->addSql('DROP TABLE books');
        $this->addSql('DROP TABLE genres');
    }
}
