<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260901134657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_participation_song (group_participation_id INT NOT NULL, song_id INT NOT NULL, INDEX IDX_C0286940957FC29E (group_participation_id), INDEX IDX_C0286940A0BDB2F3 (song_id), PRIMARY KEY (group_participation_id, song_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE group_participation_song ADD CONSTRAINT FK_C0286940957FC29E FOREIGN KEY (group_participation_id) REFERENCES group_participation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_participation_song ADD CONSTRAINT FK_C0286940A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_participation_song DROP FOREIGN KEY FK_C0286940957FC29E');
        $this->addSql('ALTER TABLE group_participation_song DROP FOREIGN KEY FK_C0286940A0BDB2F3');
        $this->addSql('DROP TABLE group_participation_song');
    }
}
