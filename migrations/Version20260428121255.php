<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428121255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, order_number VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, number_of_people INT NOT NULL, requested_delivery_at DATETIME DEFAULT NULL, delivery_at DATETIME DEFAULT NULL, service_address VARCHAR(100) DEFAULT NULL, service_price_at_order NUMERIC(10, 2) NOT NULL, delivery_price_at_order NUMERIC(10, 2) NOT NULL, total_price_at_order NUMERIC(10, 2) NOT NULL, equipment_loan TINYINT NOT NULL, menu_title_at_order VARCHAR(50) DEFAULT NULL, menu_description_at_order LONGTEXT DEFAULT NULL, price_per_person_at_order NUMERIC(5, 2) NOT NULL, order_status_id INT NOT NULL, user_id INT NOT NULL, menu_id INT NOT NULL, UNIQUE INDEX UNIQ_F5299398551F0F81 (order_number), INDEX IDX_F5299398D7707B45 (order_status_id), INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F5299398CCD7E912 (menu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE order_cancellation (id INT AUTO_INCREMENT NOT NULL, reason LONGTEXT NOT NULL, cancelled_at DATETIME NOT NULL, contact_method VARCHAR(255) NOT NULL, order_id INT NOT NULL, cancelled_by_id INT NOT NULL, UNIQUE INDEX UNIQ_FF0EC9668D9F6D38 (order_id), INDEX IDX_FF0EC966187B2D12 (cancelled_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE order_status (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, label VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_B88F75C977153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('CREATE TABLE order_status_history (id INT AUTO_INCREMENT NOT NULL, changed_at DATETIME NOT NULL, changed_by_user_id INT NOT NULL, order_id INT NOT NULL, order_status_id INT NOT NULL, INDEX IDX_471AD77EC20FFD1C (changed_by_user_id), INDEX IDX_471AD77E8D9F6D38 (order_id), INDEX IDX_471AD77ED7707B45 (order_status_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398D7707B45 FOREIGN KEY (order_status_id) REFERENCES order_status (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');

        $this->addSql('ALTER TABLE order_cancellation ADD CONSTRAINT FK_FF0EC9668D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_cancellation ADD CONSTRAINT FK_FF0EC966187B2D12 FOREIGN KEY (cancelled_by_id) REFERENCES user (id)');

        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77EC20FFD1C FOREIGN KEY (changed_by_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77ED7707B45 FOREIGN KEY (order_status_id) REFERENCES order_status (id)');

        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY `FK_957D8CB855FB9605`');
        $this->addSql('DROP INDEX IDX_957D8CB855FB9605 ON dish');

        $this->addSql('ALTER TABLE dish ADD dish_type VARCHAR(255) NOT NULL, DROP dish_type_id');

        $this->addSql('DROP TABLE dish_type');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_25BF08CE5E237E06 ON allergen (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9DE465205E237E06 ON diet (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dish_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398D7707B45');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398CCD7E912');
        $this->addSql('ALTER TABLE order_cancellation DROP FOREIGN KEY FK_FF0EC9668D9F6D38');
        $this->addSql('ALTER TABLE order_cancellation DROP FOREIGN KEY FK_FF0EC966187B2D12');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77EC20FFD1C');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E8D9F6D38');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77ED7707B45');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_cancellation');
        $this->addSql('DROP TABLE order_status');
        $this->addSql('DROP TABLE order_status_history');
        $this->addSql('DROP INDEX UNIQ_25BF08CE5E237E06 ON allergen');
        $this->addSql('DROP INDEX UNIQ_9DE465205E237E06 ON diet');
        $this->addSql('ALTER TABLE dish ADD dish_type_id INT NOT NULL, DROP dish_type');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT `FK_957D8CB855FB9605` FOREIGN KEY (dish_type_id) REFERENCES dish_type (id)');
        $this->addSql('CREATE INDEX IDX_957D8CB855FB9605 ON dish (dish_type_id)');
    }
}
