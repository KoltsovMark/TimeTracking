<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210514123734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE tasks_reports (
                id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
                user_id INT UNSIGNED DEFAULT NULL, 
                storage SMALLINT UNSIGNED NOT NULL, 
                storage_type SMALLINT UNSIGNED NOT NULL, 
                storage_name VARCHAR(255) NOT NULL, 
                storage_full_path VARCHAR(255) NOT NULL, 
                report_options JSON DEFAULT NULL, 
                created_at DATETIME NOT NULL, 
                updated_at DATETIME NOT NULL, 
                UNIQUE INDEX UNIQ_A152DDC3570EB513 (storage_name), 
                UNIQUE INDEX UNIQ_A152DDC366A889C2 (storage_full_path), 
                INDEX IDX_A152DDC3A76ED395 (user_id), 
                PRIMARY KEY(id)
            ) 
            DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci` 
            ENGINE = InnoDB'
        );
        $this->addSql('
            ALTER TABLE tasks_reports 
            ADD CONSTRAINT FK_A152DDC3A76ED395 
            FOREIGN KEY (user_id) 
            REFERENCES users (id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tasks_reports');
    }
}
