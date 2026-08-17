<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260817120735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE song (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, artist VARCHAR(255) DEFAULT NULL, ton VARCHAR(50) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, grupo_id INT DEFAULT NULL, INDEX IDX_33EDEEA19C833003 (grupo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA19C833003 FOREIGN KEY (grupo_id) REFERENCES `group` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA19C833003');
        $this->addSql('DROP TABLE song');
    }
}
