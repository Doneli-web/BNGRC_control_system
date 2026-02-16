DROP DATABASE IF EXISTS BNGRC;
CREATE DATABASE IF NOT EXISTS BNGRC;
USE BNGRC;

CREATE TABLE BNGRC_region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE BNGRC_ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    idRegion INT NOT NULL,

    CONSTRAINT fk_ville_region
        FOREIGN KEY (idRegion)
        REFERENCES BNGRC_region(id)
);

CREATE TABLE BNGRC_typeDon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE BNGRC_article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    idType INT NOT NULL,
    prix_unitaire DECIMAL(12,2) NOT NULL,

    CONSTRAINT fk_article_type
        FOREIGN KEY (idType)
        REFERENCES BNGRC_typeDon(id)
        
);
CREATE TABLE BNGRC_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idVille INT NOT NULL,
    idArticle INT NOT NULL,
    quantite INT NOT NULL,
    date_de_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_besoin_ville
        FOREIGN KEY (idVille)
        REFERENCES BNGRC_ville(id),

    CONSTRAINT fk_besoin_article
        FOREIGN KEY (idArticle)
        REFERENCES BNGRC_article(id)
);

CREATE TABLE BNGRC_don (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idArticle INT NOT NULL,
    quantite INT NOT NULL,
    date_de_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_don_article
        FOREIGN KEY (idArticle)
        REFERENCES BNGRC_article(id)
);


INSERT INTO BNGRC_typeDon(name) VALUES
('Nature'),
('Matériaux'),
('Argent');

INSERT INTO BNGRC_article(name, idType, prix_unitaire) VALUES
('Riz', 1, 3000),
('Huile', 1, 5000),
('Tôle', 2, 20000),
('Clou', 2, 100),
('Argent', 3, 1);




-- Dons en Nature (idType = 1)
INSERT INTO BNGRC_don (idArticle, quantite, date_de_saisie) VALUES
(1, 500, '2026-01-05 09:30:00'),  -- 500 kg de riz
(1, 1000, '2026-01-12 14:15:00'), -- 1000 kg de riz
(1, 750, '2026-01-20 11:00:00'),  -- 750 kg de riz
(1, 2000, '2026-02-01 10:00:00'), -- 2000 kg de riz (urgence cyclone)
(1, 1500, '2026-02-05 16:30:00'), -- 1500 kg de riz
(2, 300, '2026-01-08 13:20:00'),  -- 300 L d'huile
(2, 500, '2026-01-18 09:45:00'),  -- 500 L d'huile
(2, 800, '2026-02-03 11:30:00'),  -- 800 L d'huile
(2, 400, '2026-02-10 15:00:00');  -- 400 L d'huile

-- Dons en Matériaux (idType = 2)
INSERT INTO BNGRC_don (idArticle, quantite, date_de_saisie) VALUES
(3, 200, '2026-01-06 10:00:00'),  -- 200 tôles
(3, 350, '2026-01-15 14:30:00'),  -- 350 tôles
(3, 500, '2026-02-02 09:00:00'),  -- 500 tôles (urgence)
(3, 150, '2026-02-08 11:45:00'),  -- 150 tôles
(4, 1000, '2026-01-10 08:20:00'), -- 1000 kg de clous
(4, 2000, '2026-01-22 16:00:00'), -- 2000 kg de clous
(4, 3000, '2026-02-04 13:15:00'), -- 3000 kg de clous
(4, 1500, '2026-02-09 10:30:00'); -- 1500 kg de clous

-- Dons en Argent (idType = 3)
INSERT INTO BNGRC_don (idArticle, quantite, date_de_saisie) VALUES
(5, 2000000, '2026-01-07 11:00:00'),  -- 2,000,000 Ar
(5, 5000000, '2026-01-17 15:30:00'),  -- 5,000,000 Ar
(5, 3000000, '2026-01-25 09:45:00'),  -- 3,000,000 Ar
(5, 10000000, '2026-02-01 14:00:00'), -- 10,000,000 Ar
(5, 7500000, '2026-02-06 11:30:00'),  -- 7,500,000 Ar
(5, 12000000, '2026-02-11 16:15:00'); -- 12,000,000 Ar