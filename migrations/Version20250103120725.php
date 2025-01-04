<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103120725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD totp_secret VARCHAR(255) DEFAULT NULL, ADD is2fa_enabled TINYINT(1) NOT NULL, DROP confirmation_code, DROP confirmation_code_created_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD confirmation_code VARCHAR(6) DEFAULT NULL, ADD confirmation_code_created_at DATETIME DEFAULT NULL, DROP totp_secret, DROP is2fa_enabled');
    }
}
