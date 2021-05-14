<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210512074301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add combined index on tasks (user_id, date)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IDX_50586597A76ED395AA9E377A ON tasks (user_id, date)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_50586597A76ED395AA9E377A ON tasks');
    }
}
