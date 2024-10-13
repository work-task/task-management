<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241013182633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create `tasks` table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tasks (
          id INT AUTO_INCREMENT NOT NULL,
          project_id INT NOT NULL,
          status VARCHAR(255) NOT NULL,
          title VARCHAR(255) NOT NULL,
          description LONGTEXT NOT NULL,
          duration INT NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          INDEX IDX_50586597166D1F9C (project_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE tasks
            ADD CONSTRAINT FK_50586597166D1F9C 
                FOREIGN KEY (project_id) REFERENCES projects (id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597166D1F9C');

        $this->addSql('DROP TABLE tasks');
    }
}
