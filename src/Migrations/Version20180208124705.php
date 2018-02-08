<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180208124705 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE api_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, api_key VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_AC64A0BAF85E0677 (username), UNIQUE INDEX UNIQ_AC64A0BAC912ED9D (api_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE galerie');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE galerie (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(200) NOT NULL COLLATE utf8mb4_unicode_ci, name VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE api_user');
    }
}
