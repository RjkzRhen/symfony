<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118210757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arrival_journal ADD created_by_id INT NOT NULL, ADD deleted_by_id INT DEFAULT NULL, DROP created_by, DROP deleted_by');
        $this->addSql('ALTER TABLE arrival_journal ADD CONSTRAINT FK_A6C2945B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE arrival_journal ADD CONSTRAINT FK_A6C2945C76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A6C2945B03A8386 ON arrival_journal (created_by_id)');
        $this->addSql('CREATE INDEX IDX_A6C2945C76F1F52 ON arrival_journal (deleted_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arrival_journal DROP FOREIGN KEY FK_A6C2945B03A8386');
        $this->addSql('ALTER TABLE arrival_journal DROP FOREIGN KEY FK_A6C2945C76F1F52');
        $this->addSql('DROP INDEX IDX_A6C2945B03A8386 ON arrival_journal');
        $this->addSql('DROP INDEX IDX_A6C2945C76F1F52 ON arrival_journal');
        $this->addSql('ALTER TABLE arrival_journal ADD created_by VARCHAR(255) NOT NULL, ADD deleted_by VARCHAR(255) NOT NULL, DROP created_by_id, DROP deleted_by_id');
    }
}
