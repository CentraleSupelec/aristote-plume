<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241219135312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration, setting up administrator entity.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "citext"');
        $this->addSql('CREATE TABLE administrator (id UUID NOT NULL, email citext NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_58DF0651E7927C74 ON administrator (email)');
        $this->addSql('COMMENT ON COLUMN administrator.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN administrator.last_login_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE administrator');
    }
}
