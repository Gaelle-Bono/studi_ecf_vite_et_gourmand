<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260513101836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY `FK_7D053A93627EA78A`');
        $this->addSql('DROP INDEX IDX_7D053A93627EA78A ON menu');
        $this->addSql('ALTER TABLE menu CHANGE main_id main_course_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9379AA6E30 FOREIGN KEY (main_course_id) REFERENCES dish (id)');
        $this->addSql('CREATE INDEX IDX_7D053A9379AA6E30 ON menu (main_course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9379AA6E30');
        $this->addSql('DROP INDEX IDX_7D053A9379AA6E30 ON menu');
        $this->addSql('ALTER TABLE menu CHANGE main_course_id main_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT `FK_7D053A93627EA78A` FOREIGN KEY (main_id) REFERENCES dish (id)');
        $this->addSql('CREATE INDEX IDX_7D053A93627EA78A ON menu (main_id)');
    }
}
