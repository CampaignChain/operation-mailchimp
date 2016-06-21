<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160621000007 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE campaignchain_operation_mailchimp_newsletter (id INT AUTO_INCREMENT NOT NULL, operation_id INT DEFAULT NULL, campaignId VARCHAR(255) NOT NULL, webId INT NOT NULL, listId VARCHAR(255) NOT NULL, folderId INT NOT NULL, templateId INT NOT NULL, contentType VARCHAR(8) NOT NULL, content LONGTEXT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, type VARCHAR(10) NOT NULL, createTime DATETIME NOT NULL, sendTime DATETIME DEFAULT NULL, contentUpdatedTime DATETIME DEFAULT NULL, status VARCHAR(8) NOT NULL, fromName VARCHAR(255) NOT NULL, fromEmail VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, archiveUrl VARCHAR(255) DEFAULT NULL, archiveUrlLong VARCHAR(255) DEFAULT NULL, trackingHtmlClicks TINYINT(1) NOT NULL, trackingTextClicks TINYINT(1) NOT NULL, trackingOpens TINYINT(1) NOT NULL, createdDate DATETIME NOT NULL, modifiedDate DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7E632AAAED9296FD (campaignId), UNIQUE INDEX UNIQ_7E632AAAA11F82B0 (webId), UNIQUE INDEX UNIQ_7E632AAA44AC3583 (operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaignchain_operation_mailchimp_newsletter ADD CONSTRAINT FK_7E632AAA44AC3583 FOREIGN KEY (operation_id) REFERENCES campaignchain_operation (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE campaignchain_operation_mailchimp_newsletter');
    }
}
