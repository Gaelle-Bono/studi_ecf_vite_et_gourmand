<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260603210751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE opening_hours (id INT AUTO_INCREMENT NOT NULL, day_of_week INT NOT NULL, is_closed TINYINT NOT NULL, morning_start TIME DEFAULT NULL, morning_end TIME DEFAULT NULL, evening_start TIME DEFAULT NULL, evening_end TIME DEFAULT NULL, UNIQUE INDEX UNIQ_2640C10B6A79171 (day_of_week), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE opening_hours_exception (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, is_closed TINYINT NOT NULL, morning_start TIME DEFAULT NULL, morning_end TIME DEFAULT NULL, evening_start TIME DEFAULT NULL, evening_end TIME DEFAULT NULL, reason VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE opening_hours');
        $this->addSql('DROP TABLE opening_hours_exception');
    }
}
