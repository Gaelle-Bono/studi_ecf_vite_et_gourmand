<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105142404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE delivery_lat_at_order delivery_lat_at_order DOUBLE PRECISION NOT NULL, CHANGE delivery_lng_at_order delivery_lng_at_order DOUBLE PRECISION NOT NULL, CHANGE company_lat_at_order company_lat_at_order DOUBLE PRECISION NOT NULL, CHANGE company_lng_at_order company_lng_at_order DOUBLE PRECISION NOT NULL, CHANGE delivery_distance_at_order delivery_distance_at_order DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE delivery_lat_at_order delivery_lat_at_order DOUBLE PRECISION DEFAULT NULL, CHANGE delivery_lng_at_order delivery_lng_at_order DOUBLE PRECISION DEFAULT NULL, CHANGE company_lat_at_order company_lat_at_order DOUBLE PRECISION DEFAULT NULL, CHANGE company_lng_at_order company_lng_at_order DOUBLE PRECISION DEFAULT NULL, CHANGE delivery_distance_at_order delivery_distance_at_order DOUBLE PRECISION DEFAULT NULL');
    }
}
