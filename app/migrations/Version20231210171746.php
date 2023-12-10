<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210171746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "Organization" (id UUID NOT NULL, created_by_id UUID NOT NULL, name VARCHAR(100) NOT NULL, address TEXT NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(10) NOT NULL, activity VARCHAR(100) NOT NULL, siret VARCHAR(14) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D9DFB88426E94372 ON "Organization" (siret)');
        $this->addSql('CREATE INDEX IDX_D9DFB884B03A8386 ON "Organization" (created_by_id)');
        $this->addSql('COMMENT ON COLUMN "Organization".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "Organization".created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "Organization".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "Organization".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE organization_organization (organization_id UUID NOT NULL, PRIMARY KEY(organization_id))');
        $this->addSql('COMMENT ON COLUMN organization_organization.organization_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "client" (id UUID NOT NULL, organization_id UUID NOT NULL, name VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, address TEXT NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, activity VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C744045532C8A3DE ON "client" (organization_id)');
        $this->addSql('COMMENT ON COLUMN "client".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "client".organization_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "client".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "client".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "commande" (id UUID NOT NULL, service_id UUID NOT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEAA67DED5CA9E6 ON "commande" (service_id)');
        $this->addSql('COMMENT ON COLUMN "commande".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "commande".service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "commande".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "commande".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "devis" (id UUID NOT NULL, organization_id UUID NOT NULL, client_id UUID NOT NULL, num_devis TEXT NOT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, is_signed BOOLEAN NOT NULL, total_ht NUMERIC(10, 2) NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, discount NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8B27C52B32C8A3DE ON "devis" (organization_id)');
        $this->addSql('CREATE INDEX IDX_8B27C52B19EB6921 ON "devis" (client_id)');
        $this->addSql('COMMENT ON COLUMN "devis".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "devis".organization_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "devis".client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "devis".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "devis".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "facture" (id UUID NOT NULL, organization_id UUID NOT NULL, client_id UUID NOT NULL, devis_id UUID NOT NULL, num_facture TEXT NOT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, is_signed BOOLEAN NOT NULL, total_ht NUMERIC(10, 2) NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE86641032C8A3DE ON "facture" (organization_id)');
        $this->addSql('CREATE INDEX IDX_FE86641019EB6921 ON "facture" (client_id)');
        $this->addSql('CREATE INDEX IDX_FE86641041DEFADA ON "facture" (devis_id)');
        $this->addSql('COMMENT ON COLUMN "facture".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "facture".organization_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "facture".client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "facture".devis_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "facture".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "facture".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE forget_password (id UUID NOT NULL, user_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C816EDE2A76ED395 ON forget_password (user_id)');
        $this->addSql('COMMENT ON COLUMN forget_password.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forget_password.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forget_password.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "role" (id UUID NOT NULL, organization_id UUID NOT NULL, name VARCHAR(100) NOT NULL, manage_org BOOLEAN NOT NULL, manage_user BOOLEAN NOT NULL, manage_client BOOLEAN NOT NULL, write_devis BOOLEAN NOT NULL, write_factures BOOLEAN NOT NULL, manage_service BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_57698A6A32C8A3DE ON "role" (organization_id)');
        $this->addSql('COMMENT ON COLUMN "role".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "role".organization_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "role".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "role".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "service" (id UUID NOT NULL, organization_id UUID NOT NULL, devis_id UUID NOT NULL, name VARCHAR(100) NOT NULL, description TEXT NOT NULL, total_ht NUMERIC(10, 2) NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, discount NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD232C8A3DE ON "service" (organization_id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD241DEFADA ON "service" (devis_id)');
        $this->addSql('COMMENT ON COLUMN "service".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "service".organization_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "service".devis_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "service".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "service".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, first_name VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(320) NOT NULL, password TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_role (user_id UUID NOT NULL, role_id UUID NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
        $this->addSql('COMMENT ON COLUMN user_role.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_role.role_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_organization (user_id UUID NOT NULL, organization_id UUID NOT NULL, PRIMARY KEY(user_id, organization_id))');
        $this->addSql('CREATE INDEX IDX_41221F7EA76ED395 ON user_organization (user_id)');
        $this->addSql('CREATE INDEX IDX_41221F7E32C8A3DE ON user_organization (organization_id)');
        $this->addSql('COMMENT ON COLUMN user_organization.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_organization.organization_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_register (id UUID NOT NULL, first_name VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(320) NOT NULL, password TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN user_register.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_register.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE "Organization" ADD CONSTRAINT FK_D9DFB884B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization_organization ADD CONSTRAINT FK_609976C32C8A3DE FOREIGN KEY (organization_id) REFERENCES "Organization" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "client" ADD CONSTRAINT FK_C744045532C8A3DE FOREIGN KEY (organization_id) REFERENCES "Organization" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "commande" ADD CONSTRAINT FK_6EEAA67DED5CA9E6 FOREIGN KEY (service_id) REFERENCES "service" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "devis" ADD CONSTRAINT FK_8B27C52B32C8A3DE FOREIGN KEY (organization_id) REFERENCES "Organization" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "devis" ADD CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES "client" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "facture" ADD CONSTRAINT FK_FE86641032C8A3DE FOREIGN KEY (organization_id) REFERENCES "Organization" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "facture" ADD CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES "client" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "facture" ADD CONSTRAINT FK_FE86641041DEFADA FOREIGN KEY (devis_id) REFERENCES "devis" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forget_password ADD CONSTRAINT FK_C816EDE2A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "role" ADD CONSTRAINT FK_57698A6A32C8A3DE FOREIGN KEY (organization_id) REFERENCES "Organization" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "service" ADD CONSTRAINT FK_E19D9AD232C8A3DE FOREIGN KEY (organization_id) REFERENCES "Organization" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "service" ADD CONSTRAINT FK_E19D9AD241DEFADA FOREIGN KEY (devis_id) REFERENCES "devis" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES "role" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_organization ADD CONSTRAINT FK_41221F7EA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_organization ADD CONSTRAINT FK_41221F7E32C8A3DE FOREIGN KEY (organization_id) REFERENCES "Organization" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "Organization" DROP CONSTRAINT FK_D9DFB884B03A8386');
        $this->addSql('ALTER TABLE organization_organization DROP CONSTRAINT FK_609976C32C8A3DE');
        $this->addSql('ALTER TABLE "client" DROP CONSTRAINT FK_C744045532C8A3DE');
        $this->addSql('ALTER TABLE "commande" DROP CONSTRAINT FK_6EEAA67DED5CA9E6');
        $this->addSql('ALTER TABLE "devis" DROP CONSTRAINT FK_8B27C52B32C8A3DE');
        $this->addSql('ALTER TABLE "devis" DROP CONSTRAINT FK_8B27C52B19EB6921');
        $this->addSql('ALTER TABLE "facture" DROP CONSTRAINT FK_FE86641032C8A3DE');
        $this->addSql('ALTER TABLE "facture" DROP CONSTRAINT FK_FE86641019EB6921');
        $this->addSql('ALTER TABLE "facture" DROP CONSTRAINT FK_FE86641041DEFADA');
        $this->addSql('ALTER TABLE forget_password DROP CONSTRAINT FK_C816EDE2A76ED395');
        $this->addSql('ALTER TABLE "role" DROP CONSTRAINT FK_57698A6A32C8A3DE');
        $this->addSql('ALTER TABLE "service" DROP CONSTRAINT FK_E19D9AD232C8A3DE');
        $this->addSql('ALTER TABLE "service" DROP CONSTRAINT FK_E19D9AD241DEFADA');
        $this->addSql('ALTER TABLE user_role DROP CONSTRAINT FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP CONSTRAINT FK_2DE8C6A3D60322AC');
        $this->addSql('ALTER TABLE user_organization DROP CONSTRAINT FK_41221F7EA76ED395');
        $this->addSql('ALTER TABLE user_organization DROP CONSTRAINT FK_41221F7E32C8A3DE');
        $this->addSql('DROP TABLE "Organization"');
        $this->addSql('DROP TABLE organization_organization');
        $this->addSql('DROP TABLE "client"');
        $this->addSql('DROP TABLE "commande"');
        $this->addSql('DROP TABLE "devis"');
        $this->addSql('DROP TABLE "facture"');
        $this->addSql('DROP TABLE forget_password');
        $this->addSql('DROP TABLE "role"');
        $this->addSql('DROP TABLE "service"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_organization');
        $this->addSql('DROP TABLE user_register');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
