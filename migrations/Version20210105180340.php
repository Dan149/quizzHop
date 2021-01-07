<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105180340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE quizz ADD creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quizz ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quizz ADD is_private BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE quizz ADD note INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D61220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7C77973D61220EA6 ON quizz (creator_id)');
        $this->addSql('CREATE INDEX IDX_7C77973D12469DE2 ON quizz (category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quizz DROP CONSTRAINT FK_7C77973D12469DE2');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE quizz DROP CONSTRAINT FK_7C77973D61220EA6');
        $this->addSql('DROP INDEX IDX_7C77973D61220EA6');
        $this->addSql('DROP INDEX IDX_7C77973D12469DE2');
        $this->addSql('ALTER TABLE quizz DROP creator_id');
        $this->addSql('ALTER TABLE quizz DROP category_id');
        $this->addSql('ALTER TABLE quizz DROP is_private');
        $this->addSql('ALTER TABLE quizz DROP note');
    }
}
