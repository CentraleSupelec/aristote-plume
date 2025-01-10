<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250109142318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add timestamp fields on article entity for traceability.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD article_generated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT LOCALTIMESTAMP(0)');
        $this->addSql('ALTER TABLE article ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT LOCALTIMESTAMP(0)');
        $this->addSql('COMMENT ON COLUMN article.article_generated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE article ALTER COLUMN created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE article ALTER COLUMN updated_at DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP article_generated_at');
        $this->addSql('ALTER TABLE article DROP created_at');
        $this->addSql('ALTER TABLE article DROP updated_at');
    }
}
