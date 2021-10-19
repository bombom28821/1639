<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018164656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331A545015');
        $this->addSql('DROP INDEX IDX_CBE5A331A545015 ON book');
        $this->addSql('ALTER TABLE book CHANGE id_category_id category_id INT NOT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A33112469DE2 ON book (category_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939879F37AE5');
        $this->addSql('DROP INDEX IDX_F529939879F37AE5 ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE id_user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON `order` (user_id)');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F46C83F1AF1');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F46DD4481AD');
        $this->addSql('DROP INDEX IDX_ED896F46DD4481AD ON order_detail');
        $this->addSql('DROP INDEX UNIQ_ED896F46C83F1AF1 ON order_detail');
        $this->addSql('ALTER TABLE order_detail CHANGE id_order_id order_id INT DEFAULT NULL, CHANGE id_book_id book_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F468D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F4616A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_ED896F468D9F6D38 ON order_detail (order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ED896F4616A2B381 ON order_detail (book_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33112469DE2');
        $this->addSql('DROP INDEX IDX_CBE5A33112469DE2 ON book');
        $this->addSql('ALTER TABLE book CHANGE category_id id_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331A545015 FOREIGN KEY (id_category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A331A545015 ON book (id_category_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP INDEX IDX_F5299398A76ED395 ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE user_id id_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939879F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F529939879F37AE5 ON `order` (id_user_id)');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F468D9F6D38');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F4616A2B381');
        $this->addSql('DROP INDEX IDX_ED896F468D9F6D38 ON order_detail');
        $this->addSql('DROP INDEX UNIQ_ED896F4616A2B381 ON order_detail');
        $this->addSql('ALTER TABLE order_detail CHANGE order_id id_order_id INT DEFAULT NULL, CHANGE book_id id_book_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F46C83F1AF1 FOREIGN KEY (id_book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F46DD4481AD FOREIGN KEY (id_order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_ED896F46DD4481AD ON order_detail (id_order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ED896F46C83F1AF1 ON order_detail (id_book_id)');
    }
}
