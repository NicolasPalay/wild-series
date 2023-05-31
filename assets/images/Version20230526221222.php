<?php

declare(strict_types=1);


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230526221222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode ADD number INT NOT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE synopsis synopsis LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE season CHANGE description description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP number, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE synopsis synopsis LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE description description LONGTEXT DEFAULT NULL');
    }
}
