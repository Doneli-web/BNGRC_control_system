CREATE TABLE BNGRC_achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDon INT NOT NULL,
    idBesoin INT NOT NULL,
    montant_utilise DECIMAL(12,2) NOT NULL,
    frais_pourcentage DECIMAL(5,2) NOT NULL,
    frais_montant DECIMAL(12,2) NOT NULL,
    montant_total DECIMAL(12,2) NOT NULL,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idDon) REFERENCES BNGRC_don(id),
    FOREIGN KEY (idBesoin) REFERENCES BNGRC_besoin(id)
);



CREATE TABLE BNGRC_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) UNIQUE NOT NULL,
    valeur VARCHAR(100) NOT NULL,
    description TEXT
);

INSERT INTO BNGRC_config (cle, valeur, description) VALUES 
('frais_achat', '10', 'Pourcentage de frais sur les achats via dons en argent');