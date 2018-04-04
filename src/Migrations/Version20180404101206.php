<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180404101206 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_paypal ADD client_id VARCHAR(200) DEFAULT NULL, ADD secret_key VARCHAR(200) DEFAULT NULL, DROP api_username, DROP api_password, DROP api_signature');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_paypal ADD api_username VARCHAR(200) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD api_password VARCHAR(200) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD api_signature VARCHAR(200) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP client_id, DROP secret_key');
    }
}
