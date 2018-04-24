<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180424101806 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gift_coupon_counter (id INT AUTO_INCREMENT NOT NULL, quote_id INT DEFAULT NULL, order_id INT DEFAULT NULL, coupon_id INT DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX UNIQ_1E149EBBDB805178 (quote_id), UNIQUE INDEX UNIQ_1E149EBB8D9F6D38 (order_id), INDEX IDX_1E149EBB66C5951B (coupon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift_coupon_counter ADD CONSTRAINT FK_1E149EBBDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('ALTER TABLE gift_coupon_counter ADD CONSTRAINT FK_1E149EBB8D9F6D38 FOREIGN KEY (order_id) REFERENCES order_ (id)');
        $this->addSql('ALTER TABLE gift_coupon_counter ADD CONSTRAINT FK_1E149EBB66C5951B FOREIGN KEY (coupon_id) REFERENCES gift_coupon (id)');
        $this->addSql('DROP TABLE gift_coupon_order');
        $this->addSql('DROP TABLE gift_coupon_quote');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gift_coupon_order (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX UNIQ_A3ED1CAD8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift_coupon_quote (id INT AUTO_INCREMENT NOT NULL, quote_id INT DEFAULT NULL, coupon_id INT DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX UNIQ_3DB544C1DB805178 (quote_id), INDEX IDX_3DB544C166C5951B (coupon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift_coupon_order ADD CONSTRAINT FK_A3ED1CAD8D9F6D38 FOREIGN KEY (order_id) REFERENCES order_ (id)');
        $this->addSql('ALTER TABLE gift_coupon_quote ADD CONSTRAINT FK_3DB544C166C5951B FOREIGN KEY (coupon_id) REFERENCES gift_coupon (id)');
        $this->addSql('ALTER TABLE gift_coupon_quote ADD CONSTRAINT FK_3DB544C1DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('DROP TABLE gift_coupon_counter');
    }
}
