<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241013182542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create `projects` table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE projects (
          id INT AUTO_INCREMENT NOT NULL,
          user_id INT NOT NULL,
          status VARCHAR(255) NOT NULL,
          title VARCHAR(255) NOT NULL,
          description LONGTEXT NOT NULL,
          duration INT NOT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          INDEX IDX_5C93B3A4A76ED395 (user_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE projects
            ADD CONSTRAINT FK_5C93B3A4A76ED395 
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4A76ED395');

        $this->addSql('DROP TABLE projects');
    }
}
