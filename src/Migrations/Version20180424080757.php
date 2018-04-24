<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180424080757 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gift_coupon_order ADD is_active TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE gift_coupon_quote ADD coupon_id INT DEFAULT NULL, ADD is_active TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE gift_coupon_quote ADD CONSTRAINT FK_3DB544C166C5951B FOREIGN KEY (coupon_id) REFERENCES gift_coupon (id)');
        $this->addSql('CREATE INDEX IDX_3DB544C166C5951B ON gift_coupon_quote (coupon_id)');
        $this->addSql('ALTER TABLE gift_coupon DROP counter');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gift_coupon ADD counter INT NOT NULL');
        $this->addSql('ALTER TABLE gift_coupon_order DROP is_active');
        $this->addSql('ALTER TABLE gift_coupon_quote DROP FOREIGN KEY FK_3DB544C166C5951B');
        $this->addSql('DROP INDEX IDX_3DB544C166C5951B ON gift_coupon_quote');
        $this->addSql('ALTER TABLE gift_coupon_quote DROP coupon_id, DROP is_active');
    }
}
