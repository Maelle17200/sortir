<?php

declare(strict_types=1);

namespace migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527221155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE participant ADD campus_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D79F6B11AF5D55E1 ON participant (campus_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11AF5D55E1
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D79F6B11AF5D55E1 ON participant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant DROP campus_id
        SQL);
    }
}
