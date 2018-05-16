<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416151051 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_meta_data (id INT AUTO_INCREMENT NOT NULL, recipient_id INT DEFAULT NULL, message_id INT DEFAULT NULL, sender VARCHAR(100) NOT NULL, date_sent DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, is_deleted_by_user TINYINT(1) NOT NULL, INDEX IDX_54EC8616E92F8F78 (recipient_id), INDEX IDX_54EC8616537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, vehicle_id INT DEFAULT NULL, cost NUMERIC(10, 0) NOT NULL, visit_date DATETIME DEFAULT NULL, INDEX IDX_F5299398545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_service (order_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_17E733998D9F6D38 (order_id), INDEX IDX_17E73399ED5CA9E6 (service_id), PRIMARY KEY(order_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_meta_data ADD CONSTRAINT FK_54EC8616E92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_meta_data ADD CONSTRAINT FK_54EC8616537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E733998D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E73399ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE message_meta_data DROP FOREIGN KEY FK_54EC8616537A1329');
        $this->addSql('ALTER TABLE order_service DROP FOREIGN KEY FK_17E733998D9F6D38');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_meta_data');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_service');
    }
}
