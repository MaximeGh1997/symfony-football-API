<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200505123555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matchs (id INT AUTO_INCREMENT NOT NULL, team1_id INT NOT NULL, team2_id INT NOT NULL, winner_id INT DEFAULT NULL, looser_id INT DEFAULT NULL, stade_id INT NOT NULL, group_name_id INT DEFAULT NULL, stage_id INT DEFAULT NULL, score_t1 DOUBLE PRECISION DEFAULT NULL, score_t2 DOUBLE PRECISION DEFAULT NULL, date DATETIME NOT NULL, draw TINYINT(1) DEFAULT NULL, INDEX IDX_6B1E6041E72BCFA4 (team1_id), INDEX IDX_6B1E6041F59E604A (team2_id), INDEX IDX_6B1E60415DFCD4B8 (winner_id), INDEX IDX_6B1E6041AC391B62 (looser_id), INDEX IDX_6B1E60416538AB43 (stade_id), INDEX IDX_6B1E6041F717C8DA (group_name_id), INDEX IDX_6B1E60412298D193 (stage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stades (id INT AUTO_INCREMENT NOT NULL, resident_id INT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, capacity DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, cover VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2DE4D43D8012C5B0 (resident_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, group_name_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, cover VARCHAR(255) NOT NULL, points DOUBLE PRECISION NOT NULL, INDEX IDX_96C22258F717C8DA (group_name_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E6041E72BCFA4 FOREIGN KEY (team1_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E6041F59E604A FOREIGN KEY (team2_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60415DFCD4B8 FOREIGN KEY (winner_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E6041AC391B62 FOREIGN KEY (looser_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60416538AB43 FOREIGN KEY (stade_id) REFERENCES stades (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E6041F717C8DA FOREIGN KEY (group_name_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60412298D193 FOREIGN KEY (stage_id) REFERENCES stages (id)');
        $this->addSql('ALTER TABLE stades ADD CONSTRAINT FK_2DE4D43D8012C5B0 FOREIGN KEY (resident_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE teams ADD CONSTRAINT FK_96C22258F717C8DA FOREIGN KEY (group_name_id) REFERENCES groups (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E6041F717C8DA');
        $this->addSql('ALTER TABLE teams DROP FOREIGN KEY FK_96C22258F717C8DA');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60416538AB43');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60412298D193');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E6041E72BCFA4');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E6041F59E604A');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60415DFCD4B8');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E6041AC391B62');
        $this->addSql('ALTER TABLE stades DROP FOREIGN KEY FK_2DE4D43D8012C5B0');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE matchs');
        $this->addSql('DROP TABLE stades');
        $this->addSql('DROP TABLE stages');
        $this->addSql('DROP TABLE teams');
    }
}
