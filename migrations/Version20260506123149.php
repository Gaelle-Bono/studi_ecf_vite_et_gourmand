<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506123149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_dish DROP FOREIGN KEY `FK_5D327CF6148EB0CB`');
        $this->addSql('ALTER TABLE menu_dish DROP FOREIGN KEY `FK_5D327CF6CCD7E912`');
        $this->addSql('DROP TABLE menu_dish');
        $this->addSql('ALTER TABLE dish DROP dish_type');
        $this->addSql('ALTER TABLE menu ADD starter_id INT DEFAULT NULL, ADD main_id INT NOT NULL, ADD dessert_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93AD5A66CC FOREIGN KEY (starter_id) REFERENCES dish (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93627EA78A FOREIGN KEY (main_id) REFERENCES dish (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93745B52FD FOREIGN KEY (dessert_id) REFERENCES dish (id)');
        $this->addSql('CREATE INDEX IDX_7D053A93AD5A66CC ON menu (starter_id)');
        $this->addSql('CREATE INDEX IDX_7D053A93627EA78A ON menu (main_id)');
        $this->addSql('CREATE INDEX IDX_7D053A93745B52FD ON menu (dessert_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_dish (menu_id INT NOT NULL, dish_id INT NOT NULL, INDEX IDX_5D327CF6CCD7E912 (menu_id), INDEX IDX_5D327CF6148EB0CB (dish_id), PRIMARY KEY (menu_id, dish_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE menu_dish ADD CONSTRAINT `FK_5D327CF6148EB0CB` FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_dish ADD CONSTRAINT `FK_5D327CF6CCD7E912` FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish ADD dish_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93AD5A66CC');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93627EA78A');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93745B52FD');
        $this->addSql('DROP INDEX IDX_7D053A93AD5A66CC ON menu');
        $this->addSql('DROP INDEX IDX_7D053A93627EA78A ON menu');
        $this->addSql('DROP INDEX IDX_7D053A93745B52FD ON menu');
        $this->addSql('ALTER TABLE menu DROP starter_id, DROP main_id, DROP dessert_id');
    }
}
