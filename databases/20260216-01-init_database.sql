CREATE DATABASE IF NOT EXISTS BNGRC;
USE BNGRC;

CREATE TABLE BNGRC_region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE BNGRC_ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    idRegion INT NOT NULL,

    CONSTRAINT fk_ville_region
        FOREIGN KEY (idRegion)
        REFERENCES BNGRC_region(id)
);

CREATE TABLE BNGRC_typeDon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE BNGRC_article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
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

