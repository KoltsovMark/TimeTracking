<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210429115145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '1:M users:tasks';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_50586597A76ED395 ON tasks (user_id)');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_1483A5E9E7927C74');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597A76ED395');
        $this->addSql('DROP INDEX IDX_50586597A76ED395 ON tasks');
        $this->addSql('ALTER TABLE tasks DROP user_id');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9e7927c74 TO UNIQ_8D93D649E7927C74');
    }
}
