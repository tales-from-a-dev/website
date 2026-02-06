<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205150146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page_view (id UUID NOT NULL, url VARCHAR(255) NOT NULL, method VARCHAR(10) NOT NULL, server VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, user_agent VARCHAR(255) NOT NULL, referer VARCHAR(255) DEFAULT NULL, visited_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX url_idx ON page_view (url)');
        $this->addSql('CREATE INDEX visited_at_idx ON page_view (visited_at)');
        $this->addSql('CREATE INDEX created_at_idx ON page_view (created_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE page_view');
    }
}
