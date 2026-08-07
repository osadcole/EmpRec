<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109191720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee ADD COLUMN is_active BOOLEAN NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__employee AS SELECT id, name, surname, email, phone, web, room, description, photo FROM employee');
        $this->addSql('DROP TABLE employee');
        $this->addSql('CREATE TABLE employee (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, web VARCHAR(255) DEFAULT NULL, room VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO employee (id, name, surname, email, phone, web, room, description, photo) SELECT id, name, surname, email, phone, web, room, description, photo FROM __temp__employee');
        $this->addSql('DROP TABLE __temp__employee');
    }
}
