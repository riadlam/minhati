-- =====================
-- ENUM TYPES
-- =====================
CREATE TYPE role_enum AS ENUM ('ts_commune', 'das', 'comite_wilaya', 'antr', 'admin');
CREATE TYPE etat_enum AS ENUM ('accepte', 'en_cours', 'refuse');
CREATE TYPE dossier_enum AS ENUM ('oui', 'non');

-- =====================
-- TABLE antennes
-- =====================
CREATE TABLE antennes (
    code_ar VARCHAR(2) PRIMARY KEY,
    lib_ar_ar VARCHAR(50),
    lib_ar_fr VARCHAR(50)
);

-- =====================
-- TABLE wilaya
-- =====================
CREATE TABLE wilaya (
    code_wil VARCHAR(2) PRIMARY KEY,
    lib_wil_ar VARCHAR(50),
    lib_wil_fr VARCHAR(50),
    code_ar VARCHAR(2),
    CONSTRAINT fk_wilaya_antenne FOREIGN KEY (code_ar) REFERENCES antennes(code_ar)
);


-- =====================
-- TABLE commune
-- =====================
CREATE TABLE commune (
    code_comm VARCHAR(5) PRIMARY KEY,
    lib_comm_ar VARCHAR(50) NOT NULL,
    lib_comm_fr VARCHAR(50) NOT NULL,
    code_wilaya VARCHAR(2) NOT NULL,
    CONSTRAINT fk_commune_wilaya FOREIGN KEY (code_wilaya) REFERENCES wilaya(code_wil)
);

-- =====================
-- TABLE users
-- =====================
CREATE TABLE users (
    code_user VARCHAR(18) PRIMARY KEY,
    nom_user VARCHAR(50),
    prenom_user VARCHAR(50),
    pass VARCHAR(255),
    fonction VARCHAR(50),
    organisme VARCHAR(50),
    statut VARCHAR(1),
    code_comm VARCHAR(5),
    code_wilaya VARCHAR(2),
    role role_enum,
    date_insertion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_commune FOREIGN KEY (code_comm) REFERENCES commune(code_comm),
    CONSTRAINT fk_user_wilaya FOREIGN KEY (code_wilaya) REFERENCES wilaya(code_wil)
);

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_comm_role_user ON users(code_comm, role, code_user);

-- =====================
-- TABLE etablissements
-- =====================
CREATE TABLE etablissements (
    code_etabliss VARCHAR(30) PRIMARY KEY,
    code_direction INT,
    direction VARCHAR(512) NOT NULL,
    nom_etabliss VARCHAR(512) NOT NULL,
    code_commune VARCHAR(5),
    commune VARCHAR(512) NOT NULL,
    niveau_enseignement VARCHAR(512) NOT NULL,
    adresse VARCHAR(512) NOT NULL,
    nature_etablissement VARCHAR(512) NOT NULL,
    CONSTRAINT fk_etab_commune FOREIGN KEY (code_commune) REFERENCES commune(code_comm)
);

CREATE INDEX idx_etab_commune_nature_niveau 
    ON etablissements (code_commune, nature_etablissement, niveau_enseignement);

-- =====================
-- TABLE tuteures
-- =====================
CREATE TABLE tuteures (
    nin VARCHAR(18) PRIMARY KEY,
    nom_ar VARCHAR(50),
    prenom_ar VARCHAR(50),
    nom_fr VARCHAR(50),
    prenom_fr VARCHAR(50),
    date_naiss DATE,
    presume VARCHAR(1) DEFAULT '0',
    commune_naiss VARCHAR(5),
    num_act VARCHAR(5),
    bis VARCHAR(1) DEFAULT '0',
    sexe VARCHAR(4),
    nss VARCHAR(12),
    adresse VARCHAR(80),
    num_cpt VARCHAR(12),
    cle_cpt VARCHAR(2),
    cats VARCHAR(80),
    montant_s FLOAT DEFAULT 0,
    autr_info VARCHAR(80),
    num_cni VARCHAR(10),
    date_cni DATE,
    lieu_cni VARCHAR(5),
    tel VARCHAR(10),
    nbr_enfants_scolarise INT DEFAULT 0,
    code_commune VARCHAR(5),
    date_insertion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tuteur_commune FOREIGN KEY (code_commune) REFERENCES commune(code_comm)
);

CREATE INDEX idx_tuteur_nom_prenom ON tuteures (nom_ar, prenom_ar, nom_fr, prenom_fr);
CREATE INDEX idx_tuteur_code_commune ON tuteures (code_commune);

-- =====================
-- TABLE eleves
-- =====================
CREATE TABLE eleves (
    num_scolaire VARCHAR(16) PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    nom_pere VARCHAR(50),
    prenom_pere VARCHAR(50),
    nom_mere VARCHAR(50),
    prenom_mere VARCHAR(50),
    date_naiss DATE,
    presume VARCHAR(1) DEFAULT '0',
    commune_naiss VARCHAR(5),
    num_act VARCHAR(5),
    bis VARCHAR(1) DEFAULT '0',
    code_etabliss VARCHAR(30),
    niv_scol VARCHAR(30),
    classe_scol VARCHAR(30),
    sexe VARCHAR(4),
    handicap VARCHAR(1) DEFAULT '0',
    orphelin VARCHAR(1) DEFAULT '0',
    relation_tuteur VARCHAR(5),
    code_tuteur VARCHAR(18),
    code_commune VARCHAR(5),
    nin_pere VARCHAR(18),
    nin_mere VARCHAR(18),
    nss_pere VARCHAR(12),
    nss_mere VARCHAR(12),
    etat_das etat_enum DEFAULT 'en_cours',
    etat_final etat_enum DEFAULT 'en_cours',
    dossier_depose dossier_enum DEFAULT 'non',
    date_insertion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_eleve_tuteur FOREIGN KEY (code_tuteur) REFERENCES tuteures(nin),
    CONSTRAINT fk_eleve_etabliss FOREIGN KEY (code_etabliss) REFERENCES etablissements(code_etabliss),
    CONSTRAINT fk_eleve_commune FOREIGN KEY (code_commune) REFERENCES commune(code_comm)
);

CREATE INDEX idx_eleve_nom_prenom ON eleves (nom, prenom);
CREATE INDEX idx_eleve_commune ON eleves (code_commune);
CREATE INDEX idx_eleve_etat ON eleves (etat_das, etat_final);
