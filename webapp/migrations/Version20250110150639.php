<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250110150639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add content column to articles to store results.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD content TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP content');
    }
}
