<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220624131337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency DROP FOREIGN KEY FK_6956883F4D16C4DD');
        $this->addSql('ALTER TABLE currency ADD CONSTRAINT FK_6956883F4D16C4DD FOREIGN KEY (shop_id) REFERENCES `shop` (shop_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shop DROP roles');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency DROP FOREIGN KEY FK_6956883F4D16C4DD');
        $this->addSql('ALTER TABLE currency ADD CONSTRAINT FK_6956883F4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (shop_id)');
        $this->addSql('ALTER TABLE `shop` ADD roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }
}
