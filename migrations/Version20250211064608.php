<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211064608 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
    $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, category_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, price INTEGER NOT NULL, description CLOB NOT NULL, image VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_D34A04AD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
    $this->addSql('CREATE INDEX IDX_D34A04AD7E3C61F9 ON product (owner_id)');
    $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, avatar_url VARCHAR(255) DEFAULT NULL, email_verified BOOLEAN DEFAULT 0 NOT NULL)');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
    $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
    $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
    $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE category');
    $this->addSql('DROP TABLE product');
    $this->addSql('DROP TABLE user');
    $this->addSql('DROP TABLE messenger_messages');
  }
}
