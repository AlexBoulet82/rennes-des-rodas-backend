<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260804134724 extends AbstractMigration
{
public function up(Schema $schema): void
{
    // 1. Ajouter la colonne en autorisant le NULL temporairement
    $this->addSql('ALTER TABLE group_participation ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');

    // 2. Remplir les anciennes lignes avec la date du jour
    $this->addSql('UPDATE group_participation SET created_at = NOW() WHERE created_at IS NULL');

    // 3. Remettre la colonne en NOT NULL
    $this->addSql('ALTER TABLE group_participation CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
}
}
