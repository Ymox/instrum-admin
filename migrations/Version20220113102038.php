<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220113102038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET @oldSqlMode = @@sql_mode');
        $this->addSql('SET SESSION sql_mode = \'NO_ENGINE_SUBSTITUTION\'');
        $this->addSql('UPDATE member SET birthday = NULL WHERE birthday = \'0000-00-00\'');
        $this->addSql('UPDATE member m1 LEFT JOIN member m2 ON m1.mobile = m2.mobile SET m1.mobile = NULL WHERE m1.mobile = \'\' OR m1.related = m2.id');
        $this->addSql('UPDATE member SET phone = NULL WHERE phone IN (\'\', \'+____/___.__.__\')');
        $this->addSql('UPDATE member m1 LEFT JOIN member m2 ON m1.email = m2.email SET m1.email = NULL WHERE m1.email = \'\' OR m1.related = m2.id');
        $this->addSql('UPDATE member SET informations = NULL WHERE informations = \'\'');
        $this->addSql('UPDATE member SET email = lower(email) WHERE email <> \'\' AND email IS NOT NULL');
        $this->addSql('SET SESSION sql_mode = @oldSqlMode');
        $this->addSql('DELETE student_level.* FROM student_level LEFT JOIN member ON student_level.member_id = member.id LEFT JOIN `level` ON student_level.`level_id` = `level`.id WHERE member.id IS NULL OR `level`.id IS NULL');
        $this->addSql('UPDATE instrument SET information = NULL WHERE information = \'\'');

        $this->addSql('CREATE TABLE award (member_id INT NOT NULL, award_type_id INT NOT NULL, year YEAR(4), INDEX IDX_8A5B2EE77597D3FE (member_id), INDEX IDX_8A5B2EE73A8C5350 (award_type_id), PRIMARY KEY(member_id, award_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE award_type (id INT AUTO_INCREMENT NOT NULL, status_to_award_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, years SMALLINT NOT NULL, `column` VARCHAR(15) NOT NULL, INDEX IDX_AA675560F4D16990 (status_to_award_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE award_type_status (award_type_id INT NOT NULL, status_id INT NOT NULL, INDEX IDX_5C0205023A8C5350 (award_type_id), INDEX IDX_5C0205026BF700BD (status_id), PRIMARY KEY(award_type_id, status_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE award_type_status ADD CONSTRAINT FK_5C0205023A8C5350 FOREIGN KEY (award_type_id) REFERENCES award_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE award_type_status ADD CONSTRAINT FK_5C0205026BF700BD FOREIGN KEY (status_id) REFERENCES status (id) ON DELETE CASCADE');
        $this->addSql('CREATE TABLE member_relation (relation_id INT NOT NULL, related_id INT NOT NULL, INDEX IDX_9554E4A33256915B (relation_id), INDEX IDX_9554E4A34162C001 (related_id), PRIMARY KEY(relation_id, related_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE member_relation ADD CONSTRAINT FK_9554E4A33256915B FOREIGN KEY (relation_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE member_relation ADD CONSTRAINT FK_9554E4A34162C001 FOREIGN KEY (related_id) REFERENCES member (id)');
        $this->addSql('CREATE TABLE member_status (member_id INT NOT NULL, status_id INT NOT NULL, INDEX IDX_5FD6E72F7597D3FE (member_id), INDEX IDX_5FD6E72F6BF700BD (status_id), PRIMARY KEY(member_id, status_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provision (member_id INT NOT NULL, stock_id INT NOT NULL, details VARCHAR(255) DEFAULT NULL, INDEX IDX_BA9B42907597D3FE (member_id), INDEX IDX_BA9B4290DCD6110 (stock_id), PRIMARY KEY(member_id, stock_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, what VARCHAR(255) NOT NULL, amount SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE award ADD CONSTRAINT FK_8A5B2EE77597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE award ADD CONSTRAINT FK_8A5B2EE73A8C5350 FOREIGN KEY (award_type_id) REFERENCES award_type (id)');
        $this->addSql('ALTER TABLE award_type ADD CONSTRAINT FK_AA675560CF0D6957 FOREIGN KEY (status_to_award_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE member_status ADD CONSTRAINT FK_5FD6E72F7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_status ADD CONSTRAINT FK_5FD6E72F6BF700BD FOREIGN KEY (status_id) REFERENCES status (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE provision ADD CONSTRAINT FK_BA9B42907597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE provision ADD CONSTRAINT FK_BA9B4290DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('DROP INDEX id ON instrument');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY cat_constraint');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY subcat_constraint');
        $this->addSql('ALTER TABLE instrument ADD usable TINYINT(1) NOT NULL AFTER brand');
        $this->addSql('DROP INDEX cat_constraint ON instrument');
        $this->addSql('CREATE INDEX IDX_3CBF69DD9E5E43A8 ON instrument (cat)');
        $this->addSql('DROP INDEX subcat_constraint ON instrument');
        $this->addSql('CREATE INDEX IDX_3CBF69DDFD761441 ON instrument (subcat)');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DD9E5E43A8 FOREIGN KEY (cat) REFERENCES category (id)');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DDFD761441 FOREIGN KEY (subcat) REFERENCES subcategory (id)');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY member_ibfk_1');
        $this->addSql('DROP INDEX status ON member');
        $this->addSql('ALTER TABLE member ADD title VARCHAR(15) NOT NULL AFTER id, ADD band YEAR(4) AFTER scmv');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78E6ADA943 FOREIGN KEY (cat_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_70E4FA78E6ADA943 ON member (cat_id)');
        $this->addSql('ALTER TABLE student_level CHANGE level_id level_id INT NOT NULL, ADD PRIMARY KEY (member_id, level_id)');
        $this->addSql('ALTER TABLE student_level ADD CONSTRAINT FK_12DDB58E7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_level ADD CONSTRAINT FK_12DDB58E5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_12DDB58E7597D3FE ON student_level (member_id)');
        $this->addSql('CREATE INDEX IDX_12DDB58E5FB14BA7 ON student_level (level_id)');
        $this->addSql('ALTER TABLE status ADD root_id INT DEFAULT NULL AFTER id, ADD parent_id INT DEFAULT NULL AFTER root_id, ADD lft INT NOT NULL, ADD rgt INT NOT NULL, ADD lvl INT NOT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX IDX_7B00651C79066886 ON status (root_id)');
        $this->addSql('CREATE INDEX IDX_7B00651C727ACA70 ON status (parent_id)');
        $this->addSql('ALTER TABLE subcategory DROP FOREIGN KEY subcategory_ibfk_2');
        $this->addSql('ALTER TABLE subcategory DROP FOREIGN KEY subcategory_ibfk_1');
        $this->addSql('DROP INDEX cat_id ON subcategory');
        $this->addSql('CREATE INDEX IDX_DDCA448E6ADA943 ON subcategory (cat_id)');
        $this->addSql('ALTER TABLE subcategory ADD CONSTRAINT FK_DDCA448E6ADA943 FOREIGN KEY (cat_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user ADD last_connection DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, ADD reset_token VARCHAR(255) DEFAULT NULL, CHANGE `password` `password` VARCHAR(255) NOT NULL, CHANGE nb_connect nb_connect INT NOT NULL');

        $this->addSql('UPDATE instrument SET usable = if(recipient = \'Inutilisable\' OR information = \'foutu\', false, true)');
        $this->addSql('UPDATE instrument SET recipient = NULL WHERE recipient IN (\'Inutilisable\', \'Libre\')');
        $this->addSql('UPDATE status SET id = id + 30 ORDER BY id DESC');
        $this->addSql('UPDATE member SET status = status + 30');
        $this->addSql('INSERT INTO status (id, root_id, parent_id, name, lft, rgt, lvl) VALUES
            (1, 1, null, \'Membre\', 0, 17, 0),
            (2, 1, 1, \'Musicien\', 1 , 2 , 1),
            (3, 1, 1, \'Comité\', 3, 12, 1),
            (4, 1, 3, \'Vice-président\', 4, 7, 2),
            (5, 1, 4, \'Président\', 5, 6, 3),
            (6, 1, 4, \'Secrétaire\', 8, 9, 2),
            (7, 1, 4, \'Caissier\', 10, 11, 2),
            (8, 1, 1, \'Comission musicale\', 13, 14, 1),
            (9, 1, 1, \'Membre honoraire\', 15, 16, 1),
            (10, 10, null, \'Externe\', 18, 43, 0),
            (11, 10, 10, \'Directeur\', 19, 22, 1),
            (12, 10, 11, \'Sous-directeur\', 20, 21, 2),
            (13, 10, 10, \'Ecole de musique\', 23, 28, 1),
            (14, 10, 13, \'Elève\', 24, 25, 2),
            (15, 10, 11, \'Professeur\', 26, 27, 2),
            (16, 10, 10, \'Parent\', 29, 30, 1),
            (17, 10, 10, \'Donateur\', 31, 32, 1),
            (18, 10, 10, \'Ancien\', 33, 38, 1),
            (19, 10, 18, \'Ancien élève\', 34, 35, 2),
            (20, 10, 18, \'Ancien membre\', 36, 37, 2),
            (21, 10, 10, \'Membre d\\\'honneur\', 39, 40, 1),
            (22, 10, 10, \'Société\', 41, 42, 1)');
        $this->addSql('INSERT INTO member_status SELECT member.id, 1 FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Membre actif\' AND member.cat_id IS NULL');
        $this->addSql('INSERT INTO member_status SELECT member.id, 2 FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Membre actif\' AND member.cat_id IS NOT NULL');
        $this->addSql('INSERT INTO member_status SELECT member.id, ns.id FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Membre honoraire\' INNER JOIN status ns ON os.name = ns.name AND ns.id <> os.id');
        $this->addSql('INSERT INTO member_status SELECT member.id, ns.id FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Professeur\' INNER JOIN status ns ON os.name = ns.name AND ns.id <> os.id');
        $this->addSql('INSERT INTO member_status SELECT member.id, 10 FROM member INNER JOIN status os ON member.status = os.id AND os.name IN (\'Membre passif\')');
        $this->addSql('INSERT INTO member_status SELECT member.id, 14 FROM member WHERE member.student');
        $this->addSql('INSERT INTO member_status SELECT member.id, 19 FROM member INNER JOIN status os ON member.status = os.id AND os.name IN (\'Elève\') WHERE NOT member.student');
        $this->addSql('INSERT INTO member_status SELECT member.id, ns.id FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Externe\' INNER JOIN status ns ON os.name = ns.name AND ns.id <> os.id');
        $this->addSql('INSERT INTO member_status SELECT member.id, ns.id FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Membre d\\\'honneur\' INNER JOIN status ns ON os.name = ns.name AND ns.id <> os.id');
        $this->addSql('INSERT INTO member_status SELECT member.id, ns.id FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Parent\' INNER JOIN status ns ON os.name = ns.name AND ns.id <> os.id');
        $this->addSql('INSERT INTO member_status SELECT member.id, ns.id FROM member INNER JOIN status os ON member.status = os.id AND os.name = \'Société\' INNER JOIN status ns ON os.name = ns.name AND ns.id <> os.id');
        $this->addSQl('DELETE FROM status WHERE id > 30');
        $this->addSQl('ALTER TABLE status AUTO_INCREMENT = 23');

        $this->addSql('INSERT INTO member_relation SELECT id, related FROM member WHERE related IS NOT NULL');

        $this->addSql('INSERT INTO award_type (id, status_to_award_id, name, years, `column`) VALUES
            (1, 9, \'Membre honoraire\', 20, \'band\'),
            (2, NULL, \'Membre d\\\'honneur SCMV\', 20, \'scmv\'),
            (3, NULL, \'Vétéran cantonal\', 25, \'scmv\'),
            (4, NULL, \'Vétéran fédéral\', 35, \'scmv\'),
            (5, NULL, \'Distinction cantonale pour 50 ans de musique\', 50, \'scmv\'),
            (6, NULL, \'Vétéran de la Confédération Internationale des Sociétés Musicales\', 60, \'scmv\'),
            (7, NULL, \'Distinction cantonale pour 65 ans de musique\', 65, \'scmv\')');

        $this->addSql('INSERT INTO award_type_status (award_type_id, status_id) VALUES
            (1,1), (1,2), (1,3), (1,4), (1,5), (1,6), (1,7), (1,8),
            (2,1), (2,2), (2,3), (2,4), (2,5), (2,6), (2,7), (2,8), (2,9),
            (3,1), (3,2), (3,3), (3,4), (3,5), (3,6), (3,7), (3,8), (3,9),
            (4,1), (4,2), (4,3), (4,4), (4,5), (4,6), (4,7), (4,8), (4,9),
            (5,1), (5,2), (5,3), (5,4), (5,5), (5,6), (5,7), (5,8), (5,9),
            (6,1), (6,2), (6,3), (6,4), (6,5), (6,6), (6,7), (6,8), (6,9),
            (7,1), (7,2), (7,3), (7,4), (7,5), (7,6), (7,7), (7,8), (7,9)');

        $this->addSql('INSERT INTO `stock` (`id`, `what`, `amount`) VALUES
            (1 , \'Clé PPG du local\', 20),
            (2 , \'Clé standard du local\', 40),
            (3 , \'Carnet de marches A5\', 0),
            (4 , \'Livret de marche A5 (6 emplacements)\', 0),
            (5 , \'Casier à partitions\', 95),
            (6 , \'Uniforme 2009 : veston\', 0),
            (7 , \'Uniforme 2009 : pantalon\', 0),
            (8 , \'Uniforme 2009 : chemise\', 0),
            (9 , \'Uniforme 2009 : ceinture\', 0),
            (10, \'Uniforme 2009 : cravatte\', 0),
            (11, \'Polo Instrum bleu\', 0),
            (12, \'Polo Instrum rouge\', 0),
            (13, \'Polo école de musique\', 0),
            (14, \'Uniforme 1994 : veston\', 0),
            (15, \'Uniforme 1994 : pantalon\', 0),
            (16, \'Uniforme 1994 : chemise\', 0),
            (17, \'Uniforme 1994 : ceinture\', 0),
            (18, \'Uniforme 1994 : cravatte\', 0),
            (19, \'Uniforme 1994 : casquette\', 0),
            (20, \'Carte de parcage\', 0)');

        $this->addSql('ALTER TABLE member DROP related, DROP status, DROP student');
        $this->addSql('ALTER TABLE instrument CHANGE property property INT DEFAULT NULL, CHANGE recipient recipient INT DEFAULT NULL');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DD6804FB49 FOREIGN KEY (recipient) REFERENCES member (id)');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DD8BF21CDE FOREIGN KEY (property) REFERENCES property (id)');
        $this->addSql('CREATE INDEX IDX_3CBF69DD6804FB49 ON instrument (recipient)');
        $this->addSql('CREATE INDEX IDX_3CBF69DD8BF21CDE ON instrument (property)');
        $this->addSql('ALTER TABLE status ADD CONSTRAINT FK_7B00651C79066886 FOREIGN KEY (root_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE status ADD CONSTRAINT FK_7B00651C727ACA70 FOREIGN KEY (parent_id) REFERENCES status (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA783C7323E0 ON member (mobile)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE award DROP FOREIGN KEY FK_8A5B2EE73A8C5350');
        $this->addSql('ALTER TABLE provision DROP FOREIGN KEY FK_BA9B4290DCD6110');
        $this->addSql('DROP TABLE award');
        $this->addSql('DROP TABLE award_type');
        $this->addSql('DROP TABLE member_status');
        $this->addSql('DROP TABLE provision');
        $this->addSql('DROP TABLE stock');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY FK_3CBF69DD8BF21CDE');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY FK_3CBF69DD6804FB49');
        $this->addSql('DROP INDEX IDX_3CBF69DD8BF21CDE ON instrument');
        $this->addSql('DROP INDEX IDX_3CBF69DD6804FB49 ON instrument');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY FK_3CBF69DD9E5E43A8');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY FK_3CBF69DDFD761441');
        $this->addSql('ALTER TABLE instrument DROP usable, CHANGE property property VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE recipient recipient VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE INDEX id ON instrument (id)');
        $this->addSql('DROP INDEX idx_3cbf69dd9e5e43a8 ON instrument');
        $this->addSql('CREATE INDEX cat_constraint ON instrument (cat)');
        $this->addSql('DROP INDEX idx_3cbf69ddfd761441 ON instrument');
        $this->addSql('CREATE INDEX subcat_constraint ON instrument (subcat)');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DD9E5E43A8 FOREIGN KEY (cat) REFERENCES category (id)');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DDFD761441 FOREIGN KEY (subcat) REFERENCES subcategory (id)');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78E6ADA943');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA7860577090');
        $this->addSql('DROP INDEX UNIQ_70E4FA783C7323E0 ON member');
        $this->addSql('DROP INDEX IDX_70E4FA78E6ADA943 ON member');
        $this->addSql('DROP INDEX IDX_70E4FA7860577090 ON member');
        $this->addSql('ALTER TABLE member ADD status INT NOT NULL, DROP title, DROP band, CHANGE scmv scmv DATE DEFAULT NULL, CHANGE student student TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT member_ibfk_1 FOREIGN KEY (status) REFERENCES status (id)');
        $this->addSql('CREATE INDEX status ON member (status)');
        $this->addSql('ALTER TABLE status DROP FOREIGN KEY FK_7B00651C79066886');
        $this->addSql('ALTER TABLE status DROP FOREIGN KEY FK_7B00651C727ACA70');
        $this->addSql('DROP INDEX IDX_7B00651C79066886 ON status');
        $this->addSql('DROP INDEX IDX_7B00651C727ACA70 ON status');
        $this->addSql('ALTER TABLE status DROP root_id, DROP parent_id, DROP lft, DROP rgt, DROP lvl, CHANGE name name VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE student_level DROP FOREIGN KEY FK_12DDB58E7597D3FE');
        $this->addSql('ALTER TABLE student_level DROP FOREIGN KEY FK_12DDB58E5FB14BA7');
        $this->addSql('DROP INDEX IDX_12DDB58E7597D3FE ON student_level');
        $this->addSql('DROP INDEX IDX_12DDB58E5FB14BA7 ON student_level');
        $this->addSql('ALTER TABLE student_level DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE student_level CHANGE level_id level_id INT DEFAULT 3 NOT NULL');
        $this->addSql('ALTER TABLE subcategory DROP FOREIGN KEY FK_DDCA448E6ADA943');
        $this->addSql('ALTER TABLE subcategory ADD CONSTRAINT subcategory_ibfk_2 FOREIGN KEY (cat_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_ddca448e6ada943 ON subcategory');
        $this->addSql('CREATE INDEX cat_id ON subcategory (cat_id)');
        $this->addSql('ALTER TABLE subcategory ADD CONSTRAINT FK_DDCA448E6ADA943 FOREIGN KEY (cat_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user CHANGE `password` `password` VARCHAR(60) NOT NULL, CHANGE nb_connect nb_connect INT DEFAULT 0 NOT NULL, DROP last_connection, DROP reset_token');
    }
}
