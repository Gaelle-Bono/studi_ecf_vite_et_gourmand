<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260817102518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY `FK_F5299398D7707B45`');
        $this->addSql('DROP INDEX IDX_F5299398D7707B45 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD order_status VARCHAR(255) NOT NULL, DROP order_status_id');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY `FK_471AD77ED7707B45`');
        $this->addSql('DROP INDEX IDX_471AD77ED7707B45 ON order_status_history');
        $this->addSql('ALTER TABLE order_status_history ADD order_status VARCHAR(255) NOT NULL, DROP order_status_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD order_status_id INT NOT NULL, DROP order_status');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT `FK_F5299398D7707B45` FOREIGN KEY (order_status_id) REFERENCES order_status (id)');
        $this->addSql('CREATE INDEX IDX_F5299398D7707B45 ON `order` (order_status_id)');
        $this->addSql('ALTER TABLE order_status_history ADD order_status_id INT NOT NULL, DROP order_status');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT `FK_471AD77ED7707B45` FOREIGN KEY (order_status_id) REFERENCES order_status (id)');
        $this->addSql('CREATE INDEX IDX_471AD77ED7707B45 ON order_status_history (order_status_id)');
    }
}
