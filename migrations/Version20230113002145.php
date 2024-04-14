<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230113002145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add notes to replace member informations field';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, type_id INT DEFAULT NULL, content LONGTEXT NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', redactor VARCHAR(255) NOT NULL, INDEX IDX_CFBDFA147597D3FE (member_id), INDEX IDX_CFBDFA14C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA147597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14C54C8C93 FOREIGN KEY (type_id) REFERENCES note_type (id)');
        $this->addSql('INSERT INTO note SELECT NULL, id, NULL, informations, CURRENT_TIMESTAMP, \'Automatique\' FROM member');
        $this->addSql('ALTER TABLE member DROP informations');
        $this->addSql('ALTER TABLE member ADD scmv_correction SMALLINT NOT NULL DEFAULT 0 AFTER scmv');
    }
    
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA147597D3FE');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14C54C8C93');
        $this->addSql('ALTER TABLE member ADD informations VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE member INNER JOIN note ON member.id = note.member_id LEFT JOIN note_type ON note.type_id = note_type.id SET informations = group_concat(concat(date_format(updated_at, \'%d.%m.%Y\'),if(type, concat(\' (\', type, \')\'),\' : \'), content, \' — \', redactor))');
        $this->addSql('ALTER TABLE member DROP scmv_correction');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE note_type');
    }
}

