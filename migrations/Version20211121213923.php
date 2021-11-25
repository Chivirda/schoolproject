<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211121213923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE support_request_answer (id INT AUTO_INCREMENT NOT NULL, support_request_id INT DEFAULT NULL, answered_by INT NOT NULL, answered_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_58D0FAAF60CA7C87 (support_request_id), INDEX IDX_58D0FAAF3948559F (answered_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE support_request_answer ADD CONSTRAINT FK_58D0FAAF60CA7C87 FOREIGN KEY (support_request_id) REFERENCES support_request (id)');
        $this->addSql('ALTER TABLE support_request_answer ADD CONSTRAINT FK_58D0FAAF3948559F FOREIGN KEY (answered_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE support_request DROP INDEX UNIQ_86A28763DE12AB56, ADD INDEX IDX_86A28763DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE support_request CHANGE created_by created_by INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE support_request_answer');
        $this->addSql('ALTER TABLE support_request DROP INDEX IDX_86A28763DE12AB56, ADD UNIQUE INDEX UNIQ_86A28763DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE support_request CHANGE created_by created_by INT DEFAULT NULL');
    }
}
