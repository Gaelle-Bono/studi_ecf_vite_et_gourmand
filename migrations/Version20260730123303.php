<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260730123303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD starter_allergens_at_order VARCHAR(180) DEFAULT NULL, ADD main_course_allergens_at_order VARCHAR(180) DEFAULT NULL, ADD dessert_allergens_at_order VARCHAR(180) DEFAULT NULL, DROP allergens_at_order');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD allergens_at_order VARCHAR(255) DEFAULT NULL, DROP starter_allergens_at_order, DROP main_course_allergens_at_order, DROP dessert_allergens_at_order');
    }
}
