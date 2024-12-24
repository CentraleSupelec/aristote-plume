<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241224163036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add fields to article entity (to trigger generation).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD generation_task_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD requested_topic VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article ADD requested_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article ADD requested_language_model VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article ADD requested_language VARCHAR(255) NOT NULL');
        $this->addSql('COMMENT ON COLUMN article.generation_task_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E6672EF6AE3 ON article (generation_task_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_23A0E6672EF6AE3');
        $this->addSql('ALTER TABLE article DROP generation_task_id');
        $this->addSql('ALTER TABLE article DROP requested_topic');
        $this->addSql('ALTER TABLE article DROP requested_type');
        $this->addSql('ALTER TABLE article DROP requested_language_model');
        $this->addSql('ALTER TABLE article DROP requested_language');
    }
}
