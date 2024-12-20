<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220143323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add app users and base articles entities.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article (id UUID NOT NULL, author_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_23A0E66F675F31B ON article (author_id)');
        $this->addSql('COMMENT ON COLUMN article.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN article.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE plume_user (id UUID NOT NULL, email citext NOT NULL, enabled BOOLEAN NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN plume_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN plume_user.last_login_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES plume_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66F675F31B');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE plume_user');
    }
}
