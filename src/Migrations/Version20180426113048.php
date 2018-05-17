<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180426113048 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_progress (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, is_done TINYINT(1) NOT NULL, completion_date DATETIME DEFAULT NULL, number_of_services_completed INT NOT NULL, UNIQUE INDEX UNIQ_C3F910F78D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_progress_line (id INT AUTO_INCREMENT NOT NULL, progress_id INT DEFAULT NULL, service_id INT DEFAULT NULL, is_done TINYINT(1) NOT NULL, completed_on DATETIME DEFAULT NULL, INDEX IDX_7CFED3ED43DB87C9 (progress_id), INDEX IDX_7CFED3EDED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_progress ADD CONSTRAINT FK_C3F910F78D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_progress_line ADD CONSTRAINT FK_7CFED3ED43DB87C9 FOREIGN KEY (progress_id) REFERENCES order_progress (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_progress_line ADD CONSTRAINT FK_7CFED3EDED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE `order` ADD user_id INT DEFAULT NULL, ADD progress_id INT DEFAULT NULL, ADD status INT NOT NULL COMMENT \'(DC2Type:orderstatus)\', CHANGE cost cost NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939843DB87C9 FOREIGN KEY (progress_id) REFERENCES order_progress (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON `order` (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F529939843DB87C9 ON `order` (progress_id)');
        $this->addSql('ALTER TABLE user ADD registration_date DATETIME DEFAULT NULL, ADD is_enabled TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939843DB87C9');
        $this->addSql('ALTER TABLE order_progress_line DROP FOREIGN KEY FK_7CFED3ED43DB87C9');
        $this->addSql('DROP TABLE order_progress');
        $this->addSql('DROP TABLE order_progress_line');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP INDEX IDX_F5299398A76ED395 ON `order`');
        $this->addSql('DROP INDEX UNIQ_F529939843DB87C9 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP user_id, DROP progress_id, DROP status, CHANGE cost cost NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE user DROP registration_date, DROP is_enabled');
    }
}
