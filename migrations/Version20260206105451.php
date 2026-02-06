<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206105451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__character AS SELECT id, name, status, species, gender, image FROM character');
        $this->addSql('DROP TABLE character');
        $this->addSql('CREATE TABLE character (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(30) DEFAULT NULL, species VARCHAR(30) DEFAULT NULL, gender VARCHAR(30) DEFAULT NULL, origin VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO character (id, name, status, species, gender, origin) SELECT id, name, status, species, gender, image FROM __temp__character');
        $this->addSql('DROP TABLE __temp__character');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__character AS SELECT id, name, status, species, gender, origin FROM character');
        $this->addSql('DROP TABLE character');
        $this->addSql('CREATE TABLE character (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(30) DEFAULT NULL, species VARCHAR(30) DEFAULT NULL, gender VARCHAR(30) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO character (id, name, status, species, gender, image) SELECT id, name, status, species, gender, origin FROM __temp__character');
        $this->addSql('DROP TABLE __temp__character');
    }
}
