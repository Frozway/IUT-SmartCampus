<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109103524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tech_notification ADD room_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tech_notification ADD CONSTRAINT FK_9BF7A4F454177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BF7A4F454177093 ON tech_notification (room_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE tech_notification DROP FOREIGN KEY FK_9BF7A4F454177093');
        $this->addSql('DROP INDEX UNIQ_9BF7A4F454177093 ON tech_notification');
        $this->addSql('ALTER TABLE tech_notification DROP room_id');
    }
}
