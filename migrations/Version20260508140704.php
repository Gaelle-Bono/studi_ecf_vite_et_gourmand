<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260508140704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu CHANGE title title VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD service_address_complement VARCHAR(180) DEFAULT NULL, ADD service_zip_code VARCHAR(10) NOT NULL, ADD service_city VARCHAR(50) NOT NULL, ADD customer_last_name_at_order VARCHAR(50) NOT NULL, ADD customer_first_name_at_order VARCHAR(50) NOT NULL, ADD customer_email_at_order VARCHAR(180) NOT NULL, ADD customer_phone_at_order VARCHAR(20) NOT NULL, CHANGE requested_delivery_at requested_delivery_at DATETIME NOT NULL, CHANGE service_address service_address LONGTEXT NOT NULL, CHANGE menu_title_at_order menu_title_at_order VARCHAR(100) NOT NULL, CHANGE menu_description_at_order menu_description_at_order LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE user ADD address_complement VARCHAR(180) DEFAULT NULL, DROP country, CHANGE address address VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu CHANGE title title VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP service_address_complement, DROP service_zip_code, DROP service_city, DROP customer_last_name_at_order, DROP customer_first_name_at_order, DROP customer_email_at_order, DROP customer_phone_at_order, CHANGE requested_delivery_at requested_delivery_at DATETIME DEFAULT NULL, CHANGE service_address service_address VARCHAR(100) DEFAULT NULL, CHANGE menu_title_at_order menu_title_at_order VARCHAR(50) DEFAULT NULL, CHANGE menu_description_at_order menu_description_at_order LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD country VARCHAR(50) NOT NULL, DROP address_complement, CHANGE address address LONGTEXT NOT NULL');
    }
}
