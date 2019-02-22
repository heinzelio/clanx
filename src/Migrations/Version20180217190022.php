<?php declare(strict_types = 1);

namespace App\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180217190022 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function preUp(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $dbNameCol = "dbName";
        $em = $this->container->get('doctrine.orm.entity_manager');
        $query = "SELECT DATABASE() AS ".$dbNameCol;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(); // we know that we get exactly one row.
        $dbName = $row[$dbNameCol];

        $this->addSql('ALTER DATABASE '.$dbName.' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        $this->addSql('ALTER TABLE `migration_versions` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci');
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // First, fix the encoding of the database and the table migration_versions
        // $this->addSql('ALTER DATABASE clanx CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        // It does not work with the dynamic database name.
        // Make sure you fix your database manually

        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, commitment_id INT DEFAULT NULL, answer VARCHAR(1000) DEFAULT NULL, INDEX question_key (question_id), INDEX commitment_key (commitment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commitment (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, event_id INT DEFAULT NULL, user_id INT DEFAULT NULL, remark VARCHAR(1000) DEFAULT NULL, possible_start VARCHAR(200) DEFAULT NULL, shirt_size VARCHAR(10) DEFAULT NULL, need_train_ticket TINYINT(1) NOT NULL, INDEX user_key (user_id), INDEX event_key (event_id), INDEX department_key (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE companion (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, name VARCHAR(200) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, is_regular TINYINT(1) NOT NULL, remark VARCHAR(1000) DEFAULT NULL, INDEX department_key (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, deputy_user_id INT DEFAULT NULL, chief_user_id INT DEFAULT NULL, name VARCHAR(200) NOT NULL, requirement VARCHAR(200) DEFAULT NULL, locked TINYINT(1) NOT NULL, INDEX chief_user_key (chief_user_id), INDEX deputy_user_key (deputy_user_id), INDEX event_key (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE duty (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, user_id INT DEFAULT NULL, shift_id INT DEFAULT NULL, INDEX shift_key (shift_id), INDEX user_key (user_id), INDEX event_key (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, date DATE NOT NULL, sticky TINYINT(1) NOT NULL, description VARCHAR(2000) DEFAULT NULL, locked TINYINT(1) NOT NULL, is_for_association_members TINYINT(1) NOT NULL, is_visible TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE legacy_user (id INT AUTO_INCREMENT NOT NULL, forename VARCHAR(200) DEFAULT NULL, surname VARCHAR(200) DEFAULT NULL, address VARCHAR(200) DEFAULT NULL, zip VARCHAR(10) DEFAULT NULL, city VARCHAR(200) DEFAULT NULL, country VARCHAR(200) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, mail VARCHAR(200) NOT NULL, dateOfBirth DATE DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, occupation VARCHAR(200) DEFAULT NULL, lastDepartment VARCHAR(200) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, text VARCHAR(1000) NOT NULL, hint VARCHAR(1000) DEFAULT NULL, type VARCHAR(1) NOT NULL, data VARCHAR(2000) DEFAULT NULL, optional TINYINT(1) NOT NULL, aggregate TINYINT(1) NOT NULL, INDEX event_key (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, can_register TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shift (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, start DATETIME NOT NULL, end DATETIME DEFAULT NULL, mandatory_size INT NOT NULL, maximum_size INT NOT NULL, INDEX department_key (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', forename VARCHAR(200) DEFAULT NULL, surname VARCHAR(200) DEFAULT NULL, gender VARCHAR(1) NOT NULL, date_of_birth DATE DEFAULT NULL, street VARCHAR(200) DEFAULT NULL, zip VARCHAR(10) DEFAULT NULL, city VARCHAR(200) DEFAULT NULL, country VARCHAR(200) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, occupation VARCHAR(200) DEFAULT NULL, is_regular TINYINT(1) NOT NULL, is_association_member TINYINT(1) NOT NULL, is_protected TINYINT(1) NOT NULL, locked tinyint(1) NOT NULL, expired tinyint(1) NOT NULL, expires_at datetime DEFAULT NULL, credentials_expired tinyint(1) NOT NULL, credentials_expire_at datetime DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25680FAE08 FOREIGN KEY (commitment_id) REFERENCES commitment (id)');
        $this->addSql('ALTER TABLE commitment ADD CONSTRAINT FK_F3E0CCBBAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE commitment ADD CONSTRAINT FK_F3E0CCBB71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE commitment ADD CONSTRAINT FK_F3E0CCBBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE companion ADD CONSTRAINT FK_1BAD2E69AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18AE98FD210 FOREIGN KEY (deputy_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A173998C2 FOREIGN KEY (chief_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE duty ADD CONSTRAINT FK_A5B0609971F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE duty ADD CONSTRAINT FK_A5B06099A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE duty ADD CONSTRAINT FK_A5B06099BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');

        $this->addSql('INSERT INTO `setting` (`can_register`) VALUES (1)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25680FAE08');
        $this->addSql('ALTER TABLE commitment DROP FOREIGN KEY FK_F3E0CCBBAE80F5DF');
        $this->addSql('ALTER TABLE companion DROP FOREIGN KEY FK_1BAD2E69AE80F5DF');
        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45AE80F5DF');
        $this->addSql('ALTER TABLE commitment DROP FOREIGN KEY FK_F3E0CCBB71F7E88B');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A71F7E88B');
        $this->addSql('ALTER TABLE duty DROP FOREIGN KEY FK_A5B0609971F7E88B');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E71F7E88B');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE duty DROP FOREIGN KEY FK_A5B06099BB70BC0E');
        $this->addSql('ALTER TABLE commitment DROP FOREIGN KEY FK_F3E0CCBBA76ED395');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18AE98FD210');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A173998C2');
        $this->addSql('ALTER TABLE duty DROP FOREIGN KEY FK_A5B06099A76ED395');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE commitment');
        $this->addSql('DROP TABLE companion');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE duty');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE legacy_user');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE shift');
        $this->addSql('DROP TABLE user');
    }
}
