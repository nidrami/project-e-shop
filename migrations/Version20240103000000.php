<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240103000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create order and order_item tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `order` (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            status VARCHAR(255) NOT NULL,
            total DOUBLE PRECISION NOT NULL,
            shipping_address LONGTEXT NOT NULL,
            billing_address LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            payment_method VARCHAR(255) NOT NULL,
            payment_id VARCHAR(255) DEFAULT NULL,
            INDEX IDX_F5299398A76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE order_item (
            id INT AUTO_INCREMENT NOT NULL,
            order_ref_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DOUBLE PRECISION NOT NULL,
            INDEX IDX_52EA1F09E238517C (order_ref_id),
            INDEX IDX_52EA1F094584665A (product_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09E238517C FOREIGN KEY (order_ref_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09E238517C');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
    }
} 