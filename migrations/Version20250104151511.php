<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250104151511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD is_two_factor_enabled TINYINT(1) NOT NULL, ADD two_factor_method VARCHAR(255) DEFAULT NULL, ADD telegram_id VARCHAR(255) DEFAULT NULL, ADD two_factor_code VARCHAR(6) DEFAULT NULL, ADD google_authenticator_secret VARCHAR(255) DEFAULT NULL, ADD two_factor_code_expiry DATETIME DEFAULT NULL, DROP age');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD age INT DEFAULT NULL, DROP is_two_factor_enabled, DROP two_factor_method, DROP telegram_id, DROP two_factor_code, DROP google_authenticator_secret, DROP two_factor_code_expiry');
    }
}
