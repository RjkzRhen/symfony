<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118214210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE arrival_journal_detail (id INT AUTO_INCREMENT NOT NULL, arrival_journal_id INT NOT NULL, employee_id INT NOT NULL, external_rate_id INT NOT NULL, counterparty_id INT NOT NULL, unit_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_D2DAEC1810523889 (arrival_journal_id), INDEX IDX_D2DAEC188C03F15C (employee_id), INDEX IDX_D2DAEC18653B46DC (external_rate_id), INDEX IDX_D2DAEC18DB1FAD05 (counterparty_id), INDEX IDX_D2DAEC18F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE counterparty (id INT AUTO_INCREMENT NOT NULL, тname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE arrival_journal_detail ADD CONSTRAINT FK_D2DAEC1810523889 FOREIGN KEY (arrival_journal_id) REFERENCES arrival_journal (id)');
        $this->addSql('ALTER TABLE arrival_journal_detail ADD CONSTRAINT FK_D2DAEC188C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE arrival_journal_detail ADD CONSTRAINT FK_D2DAEC18653B46DC FOREIGN KEY (external_rate_id) REFERENCES external_rate (id)');
        $this->addSql('ALTER TABLE arrival_journal_detail ADD CONSTRAINT FK_D2DAEC18DB1FAD05 FOREIGN KEY (counterparty_id) REFERENCES counterparty (id)');
        $this->addSql('ALTER TABLE arrival_journal_detail ADD CONSTRAINT FK_D2DAEC18F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arrival_journal_detail DROP FOREIGN KEY FK_D2DAEC1810523889');
        $this->addSql('ALTER TABLE arrival_journal_detail DROP FOREIGN KEY FK_D2DAEC188C03F15C');
        $this->addSql('ALTER TABLE arrival_journal_detail DROP FOREIGN KEY FK_D2DAEC18653B46DC');
        $this->addSql('ALTER TABLE arrival_journal_detail DROP FOREIGN KEY FK_D2DAEC18DB1FAD05');
        $this->addSql('ALTER TABLE arrival_journal_detail DROP FOREIGN KEY FK_D2DAEC18F8BD700D');
        $this->addSql('DROP TABLE arrival_journal_detail');
        $this->addSql('DROP TABLE counterparty');
    }
}
