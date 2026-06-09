<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260513090732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(180) NOT NULL, zip_code VARCHAR(10) NOT NULL, city VARCHAR(50) NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE dish CHANGE title title VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE menu ADD minimum_days_before_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD delivery_latitude DOUBLE PRECISION DEFAULT NULL, ADD delivery_longitude DOUBLE PRECISION DEFAULT NULL, ADD delivery_address_hash VARCHAR(32) DEFAULT NULL, ADD delivery_distance_km DOUBLE PRECISION DEFAULT NULL, ADD starter_title_at_order VARCHAR(100) DEFAULT NULL, ADD main_course_title_at_order VARCHAR(100) NOT NULL, ADD dessert_title_at_order VARCHAR(100) DEFAULT NULL, ADD allergens_at_order VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE company');
        $this->addSql('ALTER TABLE dish CHANGE title title VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE menu DROP minimum_days_before_order');
        $this->addSql('ALTER TABLE `order` DROP delivery_latitude, DROP delivery_longitude, DROP delivery_address_hash, DROP delivery_distance_km, DROP starter_title_at_order, DROP main_course_title_at_order, DROP dessert_title_at_order, DROP allergens_at_order');
    }
}
