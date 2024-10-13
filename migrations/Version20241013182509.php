<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241013182509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create `users` table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
          id INT AUTO_INCREMENT NOT NULL,
          username VARCHAR(180) NOT NULL,
          roles JSON NOT NULL COMMENT \'(DC2Type:json)\',
          api_key VARCHAR(255) NOT NULL,
          UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
