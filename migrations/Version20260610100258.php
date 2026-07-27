<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610100258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE loan_equipment_description_at_order included_equipment_description_at_order VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_9775E70877153098 ON theme');
        $this->addSql('ALTER TABLE theme DROP code');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE included_equipment_description_at_order loan_equipment_description_at_order VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD code VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E70877153098 ON theme (code)');
    }
}
