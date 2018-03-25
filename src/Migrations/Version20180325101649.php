<?php declare(strict_types = 1);

namespace App\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180325101649 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP locked, DROP expired, DROP expires_at, DROP credentials_expired, DROP credentials_expire_at, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C05FB297 ON user (confirmation_token)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_8D93D649C05FB297 ON user');
        $this->addSql('ALTER TABLE user ADD locked TINYINT(1) DEFAULT \'0\' NOT NULL, ADD expired TINYINT(1) DEFAULT \'0\' NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD credentials_expired TINYINT(1) DEFAULT \'0\' NOT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci');
    }
}
