<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260429134311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu CHANGE remaining_quantity remaining_quantity INT NOT NULL');
        $this->addSql('ALTER TABLE order_cancellation CHANGE reason reason LONGTEXT DEFAULT NULL, CHANGE contact_method contact_method VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY `FK_471AD77EC20FFD1C`');
        $this->addSql('DROP INDEX IDX_471AD77EC20FFD1C ON order_status_history');
        $this->addSql('ALTER TABLE order_status_history CHANGE changed_by_user_id changed_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_471AD77E828AD0A0 ON order_status_history (changed_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A5E237E06 ON role (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7085E237E06 ON theme (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu CHANGE remaining_quantity remaining_quantity INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_cancellation CHANGE reason reason LONGTEXT NOT NULL, CHANGE contact_method contact_method VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E828AD0A0');
        $this->addSql('DROP INDEX IDX_471AD77E828AD0A0 ON order_status_history');
        $this->addSql('ALTER TABLE order_status_history CHANGE changed_by_id changed_by_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT `FK_471AD77EC20FFD1C` FOREIGN KEY (changed_by_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_471AD77EC20FFD1C ON order_status_history (changed_by_user_id)');
        $this->addSql('DROP INDEX UNIQ_57698A6A5E237E06 ON role');
        $this->addSql('DROP INDEX UNIQ_9775E7085E237E06 ON theme');
    }
}
