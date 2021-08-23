<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210823135901 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE constante_jour (id INT AUTO_INCREMENT NOT NULL, id_constante INT NOT NULL, id_infirmier INT NOT NULL, id_specialite INT NOT NULL, id_patient INT NOT NULL, libelle_cst VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, id_enseigne INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assoc_matiere_niveau ADD CONSTRAINT FK_78B8EBD3F46CD258 FOREIGN KEY (matiere_id) REFERENCES matieres (id)');
        $this->addSql('ALTER TABLE bal ADD CONSTRAINT FK_8CF0B1C910335F61 FOREIGN KEY (expediteur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bal_destinataire ADD CONSTRAINT FK_A431B807A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bal_destinataire ADD CONSTRAINT FK_A431B8077A45358C FOREIGN KEY (groupe_id) REFERENCES bal_groupe (id)');
        $this->addSql('ALTER TABLE bal_destinataire ADD CONSTRAINT FK_A431B807537A1329 FOREIGN KEY (message_id) REFERENCES bal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bal_groupe_user ADD CONSTRAINT FK_B8E327005CB1F1E8 FOREIGN KEY (bal_groupe_id) REFERENCES bal_groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bal_groupe_user ADD CONSTRAINT FK_B8E32700A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC5B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveaux_etudes (id)');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC5D94388BD FOREIGN KEY (serie_id) REFERENCES series (id)');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC53F7FCEF3 FOREIGN KEY (professeur_principale_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE classes ADD CONSTRAINT FK_2ED7EC56C2A0A71 FOREIGN KEY (enseigne_id) REFERENCES enseigne_affiliee (id)');
        $this->addSql('ALTER TABLE classes_user ADD CONSTRAINT FK_E9AF37279E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classes_user ADD CONSTRAINT FK_E9AF3727A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classes_matieres ADD CONSTRAINT FK_C76295CB9E225B24 FOREIGN KEY (classes_id) REFERENCES classes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classes_matieres ADD CONSTRAINT FK_C76295CB82350831 FOREIGN KEY (matieres_id) REFERENCES matieres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE country CHANGE iso3 iso3 VARCHAR(3) NULL, CHANGE numcode numcode INT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CF6B192E FOREIGN KEY (id_classe_id) REFERENCES classes (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C755C5E8E FOREIGN KEY (id_prof_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C51E6528F FOREIGN KEY (id_matiere_id) REFERENCES matieres (id)');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B63F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE enseigne_affiliee ADD CONSTRAINT FK_A81F5BBE1A867E8F FOREIGN KEY (id_entreprise_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ensseigne ADD CONSTRAINT FK_F96899FA44D42E0C FOREIGN KEY (enseigne_type_id) REFERENCES type_ens (id)');
        $this->addSql('ALTER TABLE ensseigne ADD CONSTRAINT FK_F96899FADF1E57AB FOREIGN KEY (quartier_id) REFERENCES quartier (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5758F5EA509 FOREIGN KEY (classe_id) REFERENCES classes (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575ABC1F7FE FOREIGN KEY (prof_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575F46CD258 FOREIGN KEY (matiere_id) REFERENCES matieres (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575FD1A3771 FOREIGN KEY (type_eval_id) REFERENCES type_eval (id)');
        $this->addSql('ALTER TABLE evaluation_note ADD CONSTRAINT FK_82FBB5AC456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE evaluation_note ADD CONSTRAINT FK_82FBB5ACA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C5AB72B27 FOREIGN KEY (id_eleve_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C3FD73900 FOREIGN KEY (pere_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C39DEC40E FOREIGN KEY (mere_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C86EC68D8 FOREIGN KEY (tuteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C8F5EA509 FOREIGN KEY (classe_id) REFERENCES classes (id)');
        $this->addSql('ALTER TABLE medecin ADD CONSTRAINT FK_1BDA53C6F46CD258 FOREIGN KEY (matiere_id) REFERENCES matieres (id)');
        $this->addSql('ALTER TABLE niveaux_etudes ADD CONSTRAINT FK_4D84C3C92733DDE0 FOREIGN KEY (type_enseigne_id) REFERENCES type_ens (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE professeur_matiere ADD CONSTRAINT FK_FBC82ABC755C5E8E FOREIGN KEY (id_prof_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE professeur_matiere_matieres ADD CONSTRAINT FK_E516439350D14415 FOREIGN KEY (professeur_matiere_id) REFERENCES professeur_matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professeur_matiere_matieres ADD CONSTRAINT FK_E516439382350831 FOREIGN KEY (matieres_id) REFERENCES matieres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quartier ADD CONSTRAINT FK_FEE8962DA73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE type_eval ADD CONSTRAINT FK_922D7E52BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_eval (id)');
        $this->addSql('ALTER TABLE user_services ADD CONSTRAINT FK_93BF0569A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_services ADD CONSTRAINT FK_93BF0569AEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_joined_enseigne ADD CONSTRAINT FK_74E0BBD26C2A0A71 FOREIGN KEY (enseigne_id) REFERENCES enseigne_affiliee (id)');
        $this->addSql('ALTER TABLE user_joined_enseigne ADD CONSTRAINT FK_74E0BBD2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ville ADD CONSTRAINT FK_43C3D9C3CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE constante_jour');
        $this->addSql('ALTER TABLE assoc_matiere_niveau DROP FOREIGN KEY FK_78B8EBD3F46CD258');
        $this->addSql('ALTER TABLE bal DROP FOREIGN KEY FK_8CF0B1C910335F61');
        $this->addSql('ALTER TABLE bal_destinataire DROP FOREIGN KEY FK_A431B807A76ED395');
        $this->addSql('ALTER TABLE bal_destinataire DROP FOREIGN KEY FK_A431B8077A45358C');
        $this->addSql('ALTER TABLE bal_destinataire DROP FOREIGN KEY FK_A431B807537A1329');
        $this->addSql('ALTER TABLE bal_groupe_user DROP FOREIGN KEY FK_B8E327005CB1F1E8');
        $this->addSql('ALTER TABLE bal_groupe_user DROP FOREIGN KEY FK_B8E32700A76ED395');
        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC5B3E9C81');
        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC5D94388BD');
        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC53F7FCEF3');
        $this->addSql('ALTER TABLE classes DROP FOREIGN KEY FK_2ED7EC56C2A0A71');
        $this->addSql('ALTER TABLE classes_matieres DROP FOREIGN KEY FK_C76295CB9E225B24');
        $this->addSql('ALTER TABLE classes_matieres DROP FOREIGN KEY FK_C76295CB82350831');
        $this->addSql('ALTER TABLE classes_user DROP FOREIGN KEY FK_E9AF37279E225B24');
        $this->addSql('ALTER TABLE classes_user DROP FOREIGN KEY FK_E9AF3727A76ED395');
        $this->addSql('ALTER TABLE country CHANGE iso3 iso3 VARCHAR(3) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE numcode numcode INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CF6B192E');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C755C5E8E');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C51E6528F');
        $this->addSql('ALTER TABLE departement DROP FOREIGN KEY FK_C1765B63F92F3E70');
        $this->addSql('ALTER TABLE enseigne_affiliee DROP FOREIGN KEY FK_A81F5BBE1A867E8F');
        $this->addSql('ALTER TABLE ensseigne DROP FOREIGN KEY FK_F96899FA44D42E0C');
        $this->addSql('ALTER TABLE ensseigne DROP FOREIGN KEY FK_F96899FADF1E57AB');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5758F5EA509');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575ABC1F7FE');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575F46CD258');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575FD1A3771');
        $this->addSql('ALTER TABLE evaluation_note DROP FOREIGN KEY FK_82FBB5AC456C5646');
        $this->addSql('ALTER TABLE evaluation_note DROP FOREIGN KEY FK_82FBB5ACA6CC7B2');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C5AB72B27');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C3FD73900');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C39DEC40E');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C86EC68D8');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C8F5EA509');
        $this->addSql('ALTER TABLE medecin DROP FOREIGN KEY FK_1BDA53C6F46CD258');
        $this->addSql('ALTER TABLE niveaux_etudes DROP FOREIGN KEY FK_4D84C3C92733DDE0');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA4F84F6E');
        $this->addSql('ALTER TABLE professeur_matiere DROP FOREIGN KEY FK_FBC82ABC755C5E8E');
        $this->addSql('ALTER TABLE professeur_matiere_matieres DROP FOREIGN KEY FK_E516439350D14415');
        $this->addSql('ALTER TABLE professeur_matiere_matieres DROP FOREIGN KEY FK_E516439382350831');
        $this->addSql('ALTER TABLE quartier DROP FOREIGN KEY FK_FEE8962DA73F0036');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE type_eval DROP FOREIGN KEY FK_922D7E52BCF5E72D');
        $this->addSql('ALTER TABLE user_joined_enseigne DROP FOREIGN KEY FK_74E0BBD26C2A0A71');
        $this->addSql('ALTER TABLE user_joined_enseigne DROP FOREIGN KEY FK_74E0BBD2A76ED395');
        $this->addSql('ALTER TABLE user_services DROP FOREIGN KEY FK_93BF0569A76ED395');
        $this->addSql('ALTER TABLE user_services DROP FOREIGN KEY FK_93BF0569AEF5A6C1');
        $this->addSql('ALTER TABLE ville DROP FOREIGN KEY FK_43C3D9C3CCF9E01E');
    }
}
