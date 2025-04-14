<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250414151920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change topic column type to TEXT to avoid restrictions.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ALTER requested_topic TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ALTER requested_topic TYPE VARCHAR(255)');
    }
}
