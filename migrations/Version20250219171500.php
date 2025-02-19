<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219171500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_token AS SELECT token, created_at FROM user_token');
        $this->addSql('DROP TABLE user_token');
        $this->addSql('CREATE TABLE user_token (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_BDF55A63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_token (token, created_at) SELECT token, created_at FROM __temp__user_token');
        $this->addSql('DROP TABLE __temp__user_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDF55A63A76ED395 ON user_token (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_token AS SELECT created_at, token FROM user_token');
        $this->addSql('DROP TABLE user_token');
        $this->addSql('CREATE TABLE user_token (email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(email))');
        $this->addSql('INSERT INTO user_token (created_at, token) SELECT created_at, token FROM __temp__user_token');
        $this->addSql('DROP TABLE __temp__user_token');
    }
}
