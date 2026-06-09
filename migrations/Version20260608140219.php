<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260608140219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu ADD requires_equipment_loan TINYINT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD delivery_lat_at_order DOUBLE PRECISION DEFAULT NULL, ADD delivery_lng_at_order DOUBLE PRECISION DEFAULT NULL, ADD company_lat_at_order DOUBLE PRECISION DEFAULT NULL, ADD company_lng_at_order DOUBLE PRECISION DEFAULT NULL, ADD delivery_distance_at_order DOUBLE PRECISION DEFAULT NULL, DROP delivery_latitude, DROP delivery_longitude, DROP delivery_address_hash, DROP delivery_distance_km, CHANGE equipment_loan requires_equipment_loan_at_order TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP requires_equipment_loan');
        $this->addSql('ALTER TABLE `order` ADD delivery_latitude DOUBLE PRECISION DEFAULT NULL, ADD delivery_longitude DOUBLE PRECISION DEFAULT NULL, ADD delivery_address_hash VARCHAR(32) DEFAULT NULL, ADD delivery_distance_km DOUBLE PRECISION DEFAULT NULL, DROP delivery_lat_at_order, DROP delivery_lng_at_order, DROP company_lat_at_order, DROP company_lng_at_order, DROP delivery_distance_at_order, CHANGE requires_equipment_loan_at_order equipment_loan TINYINT NOT NULL');
    }
}
