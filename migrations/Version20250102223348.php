<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102223348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment CHANGE apartment_number apartment_number VARCHAR(10) NOT NULL, CHANGE owner_name owner_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD confirmation_code VARCHAR(6) DEFAULT NULL, ADD confirmation_code_created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment CHANGE owner_name owner_name VARCHAR(255) NOT NULL, CHANGE apartment_number apartment_number INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP confirmation_code, DROP confirmation_code_created_at');
    }
}
