<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250811142801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sortie_user (sortie_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8A67684ACC72D953 (sortie_id), INDEX IDX_8A67684AA76ED395 (user_id), PRIMARY KEY(sortie_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sortie_user ADD CONSTRAINT FK_8A67684ACC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sortie_user ADD CONSTRAINT FK_8A67684AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lieu ADD id_ville_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59F7E4ECA3 FOREIGN KEY (id_ville_id) REFERENCES ville (id)');
        $this->addSql('CREATE INDEX IDX_2F577D59F7E4ECA3 ON lieu (id_ville_id)');
        $this->addSql('ALTER TABLE sortie ADD id_site_id INT DEFAULT NULL, ADD id_organisateur_id INT DEFAULT NULL, ADD id_etat_id INT DEFAULT NULL, ADD id_lieu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F22820BF36 FOREIGN KEY (id_site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F230687172 FOREIGN KEY (id_organisateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D3C32F8F FOREIGN KEY (id_etat_id) REFERENCES etat (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2B42FBABC FOREIGN KEY (id_lieu_id) REFERENCES lieu (id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F22820BF36 ON sortie (id_site_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F230687172 ON sortie (id_organisateur_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D3C32F8F ON sortie (id_etat_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2B42FBABC ON sortie (id_lieu_id)');
        $this->addSql('ALTER TABLE user ADD id_site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492820BF36 FOREIGN KEY (id_site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492820BF36 ON user (id_site_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie_user DROP FOREIGN KEY FK_8A67684ACC72D953');
        $this->addSql('ALTER TABLE sortie_user DROP FOREIGN KEY FK_8A67684AA76ED395');
        $this->addSql('DROP TABLE sortie_user');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492820BF36');
        $this->addSql('DROP INDEX IDX_8D93D6492820BF36 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP id_site_id');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59F7E4ECA3');
        $this->addSql('DROP INDEX IDX_2F577D59F7E4ECA3 ON lieu');
        $this->addSql('ALTER TABLE lieu DROP id_ville_id');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F22820BF36');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F230687172');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2D3C32F8F');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2B42FBABC');
        $this->addSql('DROP INDEX IDX_3C3FD3F22820BF36 ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F230687172 ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F2D3C32F8F ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F2B42FBABC ON sortie');
        $this->addSql('ALTER TABLE sortie DROP id_site_id, DROP id_organisateur_id, DROP id_etat_id, DROP id_lieu_id');
    }
}
