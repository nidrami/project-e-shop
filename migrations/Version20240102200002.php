<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240102200002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial database schema';
    }

    public function up(Schema $schema): void
    {
        // Drop existing tables to start fresh
        $this->addSql('DROP TABLE IF EXISTS `user`');
        
        // Create user table with all required fields
        $this->addSql('CREATE TABLE `user` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `email` VARCHAR(180) NOT NULL,
            `roles` LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\',
            `password` VARCHAR(255) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE INDEX `UNIQ_8D93D649E7927C74` (`email`),
            PRIMARY KEY(`id`)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `user`');
    }
} 