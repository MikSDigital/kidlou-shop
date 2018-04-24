<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180424074355 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gift_coupon_order (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A3ED1CAD8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift_coupon_quote (id INT AUTO_INCREMENT NOT NULL, quote_id INT DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_3DB544C1DB805178 (quote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift_coupon_order ADD CONSTRAINT FK_A3ED1CAD8D9F6D38 FOREIGN KEY (order_id) REFERENCES order_ (id)');
        $this->addSql('ALTER TABLE gift_coupon_quote ADD CONSTRAINT FK_3DB544C1DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('ALTER TABLE gift_coupon DROP FOREIGN KEY FK_38197D568D9F6D38');
        $this->addSql('ALTER TABLE gift_coupon DROP FOREIGN KEY FK_38197D56DB805178');
        $this->addSql('DROP INDEX IDX_38197D56DB805178 ON gift_coupon');
        $this->addSql('DROP INDEX IDX_38197D568D9F6D38 ON gift_coupon');
        $this->addSql('ALTER TABLE gift_coupon DROP quote_id, DROP order_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE gift_coupon_order');
        $this->addSql('DROP TABLE gift_coupon_quote');
        $this->addSql('ALTER TABLE gift_coupon ADD quote_id INT DEFAULT NULL, ADD order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gift_coupon ADD CONSTRAINT FK_38197D568D9F6D38 FOREIGN KEY (order_id) REFERENCES order_ (id)');
        $this->addSql('ALTER TABLE gift_coupon ADD CONSTRAINT FK_38197D56DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('CREATE INDEX IDX_38197D56DB805178 ON gift_coupon (quote_id)');
        $this->addSql('CREATE INDEX IDX_38197D568D9F6D38 ON gift_coupon (order_id)');
    }
}
