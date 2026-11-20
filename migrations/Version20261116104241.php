<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261116104241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des snapshots des informations complémentaires du menu dans la commande.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD minimum_number_of_people_at_order INT NOT NULL, ADD conditions_at_order JSON DEFAULT NULL, ADD diet_name_at_order VARCHAR(255) NOT NULL, ADD theme_name_at_order VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP minimum_number_of_people_at_order, DROP conditions_at_order, DROP diet_name_at_order, DROP theme_name_at_order');
    }
}
