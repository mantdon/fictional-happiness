<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180510193133 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, make VARCHAR(20) NOT NULL, model VARCHAR(30) NOT NULL, year_of_production INT NOT NULL, plate_number VARCHAR(7) NOT NULL, INDEX IDX_1B80E486A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_progress_line (id INT AUTO_INCREMENT NOT NULL, progress_id INT DEFAULT NULL, service_id INT DEFAULT NULL, is_done TINYINT(1) NOT NULL, completed_on DATETIME DEFAULT NULL, INDEX IDX_7CFED3ED43DB87C9 (progress_id), INDEX IDX_7CFED3EDED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_progress (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, is_done TINYINT(1) NOT NULL, completion_date DATETIME DEFAULT NULL, number_of_services_completed INT NOT NULL, UNIQUE INDEX UNIQ_C3F910F78D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, vehicle_id INT DEFAULT NULL, user_id INT DEFAULT NULL, progress_id INT DEFAULT NULL, cost NUMERIC(10, 2) NOT NULL, visit_date DATETIME DEFAULT NULL, status INT NOT NULL COMMENT \'(DC2Type:orderstatus)\', INDEX IDX_F5299398545317D1 (vehicle_id), INDEX IDX_F5299398A76ED395 (user_id), UNIQUE INDEX UNIQ_F529939843DB87C9 (progress_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_service (order_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_17E733998D9F6D38 (order_id), INDEX IDX_17E73399ED5CA9E6 (service_id), PRIMARY KEY(order_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_meta_data (id INT AUTO_INCREMENT NOT NULL, recipient_id INT DEFAULT NULL, message_id INT DEFAULT NULL, sender VARCHAR(100) NOT NULL, date_sent DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, is_deleted_by_user TINYINT(1) NOT NULL, INDEX IDX_54EC8616E92F8F78 (recipient_id), INDEX IDX_54EC8616537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, first_name VARCHAR(64) DEFAULT NULL, last_name VARCHAR(64) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, city VARCHAR(64) DEFAULT NULL, address VARCHAR(254) DEFAULT NULL, role VARCHAR(64) NOT NULL, registration_date DATETIME DEFAULT NULL, is_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64935C246D5 (password), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E486A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_progress_line ADD CONSTRAINT FK_7CFED3ED43DB87C9 FOREIGN KEY (progress_id) REFERENCES order_progress (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_progress_line ADD CONSTRAINT FK_7CFED3EDED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE order_progress ADD CONSTRAINT FK_C3F910F78D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939843DB87C9 FOREIGN KEY (progress_id) REFERENCES order_progress (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E733998D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E73399ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_meta_data ADD CONSTRAINT FK_54EC8616E92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_meta_data ADD CONSTRAINT FK_54EC8616537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398545317D1');
        $this->addSql('ALTER TABLE order_progress_line DROP FOREIGN KEY FK_7CFED3ED43DB87C9');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939843DB87C9');
        $this->addSql('ALTER TABLE order_progress DROP FOREIGN KEY FK_C3F910F78D9F6D38');
        $this->addSql('ALTER TABLE order_service DROP FOREIGN KEY FK_17E733998D9F6D38');
        $this->addSql('ALTER TABLE order_progress_line DROP FOREIGN KEY FK_7CFED3EDED5CA9E6');
        $this->addSql('ALTER TABLE order_service DROP FOREIGN KEY FK_17E73399ED5CA9E6');
        $this->addSql('ALTER TABLE message_meta_data DROP FOREIGN KEY FK_54EC8616537A1329');
        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E486A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE message_meta_data DROP FOREIGN KEY FK_54EC8616E92F8F78');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP TABLE order_progress_line');
        $this->addSql('DROP TABLE order_progress');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_service');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_meta_data');
        $this->addSql('DROP TABLE user');
    }
}
