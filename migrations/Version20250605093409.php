<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250605093409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE lieu CHANGE latitude latitude DOUBLE PRECISION NOT NULL, CHANGE longitude longitude DOUBLE PRECISION NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_IDENTIFIER_EMAIL ON participant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant CHANGE pseudo pseudo VARCHAR(180) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D79F6B11E7927C74 ON participant (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D79F6B1186CC499D ON participant (pseudo)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sortie ADD motif_annulation VARCHAR(255) NOT NULL, CHANGE duree duree INT NOT NULL, CHANGE nb_inscription_max nb_inscription_max INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3C3FD3F26C6E55B5AF5D55E1D709040A ON sortie (nom, campus_id, date_heure_debut)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_3C3FD3F26C6E55B5AF5D55E1D709040A ON sortie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sortie DROP motif_annulation, CHANGE duree duree TIME NOT NULL COMMENT '(DC2Type:time_immutable)', CHANGE nb_inscription_max nb_inscription_max INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D79F6B11E7927C74 ON participant
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D79F6B1186CC499D ON participant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant CHANGE pseudo pseudo VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON participant (email, pseudo)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lieu CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL
        SQL);
    }
}
