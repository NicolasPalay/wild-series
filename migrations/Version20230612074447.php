<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612074447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DDAA1CDA7E3C61F9 ON episode (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDA7E3C61F9');
        $this->addSql('DROP INDEX IDX_DDAA1CDA7E3C61F9 ON episode');
        $this->addSql('ALTER TABLE episode DROP owner_id');
    }
}
