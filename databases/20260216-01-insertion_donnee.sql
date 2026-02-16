INSERT INTO BNGRC_region (name) VALUES
('Analamanga'),
('Atsinanana'),
('Vakinankaratra');

INSERT INTO BNGRC_ville (name, idRegion) VALUES
('Antananarivo', 1),
('Ambohidratrimo', 1),
('Toamasina', 2),
('Brickaville', 2),
('Antsirabe', 3),
('Betafo', 3);

INSERT INTO BNGRC_besoin (idVille, idArticle, quantite, date_de_saisie) VALUES

-- 🏙️ Antananarivo
(1, 1, 500, '2026-02-10 08:00:00'), -- Riz
(1, 2, 200, '2026-02-10 08:15:00'), -- Huile
(1, 3, 60,  '2026-02-10 08:30:00'), -- Tôle

-- 🏘️ Ambohidratrimo
(2, 1, 300, '2026-02-11 09:00:00'),
(2, 4, 800, '2026-02-11 09:20:00'), -- Clous

-- 🌊 Toamasina
(3, 1, 700, '2026-02-12 07:30:00'),
(3, 3, 120, '2026-02-12 07:45:00'),
(3, 5, 200000, '2026-02-12 08:00:00'), -- Argent

-- 🌴 Brickaville
(4, 2, 250, '2026-02-12 10:00:00'),
(4, 4, 1500, '2026-02-12 10:15:00'),

-- 🌄 Antsirabe
(5, 1, 600, '2026-02-13 08:00:00'),
(5, 3, 90,  '2026-02-13 08:30:00'),

-- 🏞️ Betafo
(6, 1, 350, '2026-02-14 09:00:00'),
(6, 5, 100000, '2026-02-14 09:15:00');

CREATE TABLE BNGRC_dispatch (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idDon INT NOT NULL,
    idBesoin INT NOT NULL,
    quantite_attribuee INT NOT NULL,
    date_dispatch DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_dispatch_don
        FOREIGN KEY (idDon)
        REFERENCES BNGRC_don(id),

    CONSTRAINT fk_dispatch_besoin
        FOREIGN KEY (idBesoin)
        REFERENCES BNGRC_besoin(id)
);

-- 🏙️ Antananarivo
-- Besoin Riz 500
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(1, 1, 500);  -- Don 1 (500 Riz) couvre le besoin

-- Besoin Huile 200
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(6, 2, 200);  -- Don 6 (300 L Huile), reste 100 L dans le don

-- Besoin Tôle 60
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(10, 3, 60); -- Don 10 (200 Tôle), reste 140 Tôle

-- 🏘️ Ambohidratrimo
-- Besoin Riz 300
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(2, 4, 300);  -- Don 2 (1000 Riz), reste 700

-- Besoin Clous 800
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(11, 5, 800); -- Don 11 (1000 Clous), reste 200

-- 🌊 Toamasina
-- Besoin Riz 700
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(2, 6, 700);  -- Don 2 (reste 700 Riz), couvert

-- Besoin Tôle 120
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(10, 7, 120); -- Don 10 (reste 140 Tôle), reste 20

-- Besoin Argent 200000
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(15, 8, 200000); -- Don 15 (2,000,000 Ar), reste 1,800,000

-- 🌴 Brickaville
-- Besoin Huile 250
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(6, 9, 100),  -- Don 6 (reste 100 L)
(7, 9, 150);  -- Don 7 (reste 500 L), besoin couvert

-- Besoin Clous 1500
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(11, 10, 200), -- Don 11 (reste 200)
(12, 10, 1300); -- Don 12 (reste 700)

-- 🌄 Antsirabe
-- Besoin Riz 600
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(2, 11, 0),    -- Don 2 épuisé
(3, 11, 600);  -- Don 3 (750 Riz), reste 150

-- Besoin Tôle 90
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(10, 12, 20),  -- Don 10 (reste 20 Tôle)
(12, 12, 70);  -- Don 12 (reste 630)

-- 🏞️ Betafo
-- Besoin Riz 350
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(3, 13, 150),  -- Don 3 (reste 150)
(4, 13, 200);  -- Don 4 (reste 1800)

-- Besoin Argent 100000
INSERT INTO BNGRC_dispatch (idDon, idBesoin, quantite_attribuee) VALUES
(15, 14, 100000); -- Don 15 (reste 1,700,000)

