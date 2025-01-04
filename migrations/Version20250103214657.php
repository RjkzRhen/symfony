<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103214657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD google_authenticator_secret VARCHAR(255) DEFAULT NULL, ADD is_two_factor_enabled TINYINT(1) NOT NULL, DROP secret_question, DROP email, DROP secret_answer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD email VARCHAR(255) DEFAULT NULL, ADD secret_answer VARCHAR(255) DEFAULT NULL, DROP is_two_factor_enabled, CHANGE google_authenticator_secret secret_question VARCHAR(255) DEFAULT NULL');
    }
}
