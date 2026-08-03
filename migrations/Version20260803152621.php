<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260803152621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, is_current TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE group_participation (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(50) NOT NULL, repertoire VARCHAR(255) DEFAULT NULL, technical_needs LONGTEXT DEFAULT NULL, special_requests LONGTEXT DEFAULT NULL, edition_id INT NOT NULL, group_user_id INT NOT NULL, INDEX IDX_F4BC2C0D74281A5E (edition_id), INDEX IDX_F4BC2C0D216E8799 (group_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE group_participation ADD CONSTRAINT FK_F4BC2C0D74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE group_participation ADD CONSTRAINT FK_F4BC2C0D216E8799 FOREIGN KEY (group_user_id) REFERENCES `group` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_participation DROP FOREIGN KEY FK_F4BC2C0D74281A5E');
        $this->addSql('ALTER TABLE group_participation DROP FOREIGN KEY FK_F4BC2C0D216E8799');
        $this->addSql('DROP TABLE edition');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE group_participation');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
