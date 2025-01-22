<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118104812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE external_rate (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, value DOUBLE PRECISION NOT NULL, counterparty_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user DROP is_two_factor_enabled, DROP two_factor_method, DROP telegram_id, DROP two_factor_code, DROP two_factor_code_expiry, DROP email_auth_code');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE external_rate');
        $this->addSql('DROP TABLE unit');
        $this->addSql('ALTER TABLE user ADD is_two_factor_enabled TINYINT(1) NOT NULL, ADD two_factor_method VARCHAR(255) DEFAULT NULL, ADD telegram_id VARCHAR(255) DEFAULT NULL, ADD two_factor_code VARCHAR(6) DEFAULT NULL, ADD two_factor_code_expiry DATETIME DEFAULT NULL, ADD email_auth_code VARCHAR(255) DEFAULT NULL');
    }
}
