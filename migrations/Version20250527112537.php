<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527112537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE participant_sortie DROP FOREIGN KEY FK_8E436D739D1C3019
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant_sortie DROP FOREIGN KEY FK_8E436D73CC72D953
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE participant_sortie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant ADD roles JSON NOT NULL, ADD pseudo VARCHAR(255) NOT NULL, DROP administrateur, DROP actif, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE telephone telephone VARCHAR(255) NOT NULL, CHANGE mot_passe mot_passe VARCHAR(255) NOT NULL, CHANGE mail email VARCHAR(180) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON participant (email, pseudo)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2D936B2FA
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3C3FD3F2D936B2FA ON sortie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sortie DROP organisateur_id, CHANGE duree duree TIME NOT NULL COMMENT '(DC2Type:time_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE participant_sortie (participant_id INT NOT NULL, sortie_id INT NOT NULL, INDEX IDX_8E436D739D1C3019 (participant_id), INDEX IDX_8E436D73CC72D953 (sortie_id), PRIMARY KEY(participant_id, sortie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant_sortie ADD CONSTRAINT FK_8E436D739D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant_sortie ADD CONSTRAINT FK_8E436D73CC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_IDENTIFIER_EMAIL ON participant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant ADD administrateur TINYINT(1) NOT NULL, ADD actif TINYINT(1) NOT NULL, DROP roles, DROP pseudo, CHANGE mot_passe mot_passe VARCHAR(180) NOT NULL, CHANGE nom nom VARCHAR(180) NOT NULL, CHANGE prenom prenom VARCHAR(180) NOT NULL, CHANGE telephone telephone VARCHAR(10) NOT NULL, CHANGE email mail VARCHAR(180) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sortie ADD organisateur_id INT NOT NULL, CHANGE duree duree TIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D936B2FA FOREIGN KEY (organisateur_id) REFERENCES participant (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3C3FD3F2D936B2FA ON sortie (organisateur_id)
        SQL);
    }
}
