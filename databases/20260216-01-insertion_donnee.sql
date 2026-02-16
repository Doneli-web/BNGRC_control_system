INSERT INTO BNGRC_region (name) VALUES
('Analamanga'),
('Atsinanana'),
('Vakinankaratra'),
('Itasy'),
('Bongolava'),
('Alaotra-Mangoro'),
('Sava'),
('Diana'),
('Sofia'),
('Boeny'),
('Melaky'),
('Menabe'),
('Atsimo-Andrefana'),
('Androy'),
('Anosy'),
('Atsimo-Atsinanana');

INSERT INTO BNGRC_ville (name, idRegion) VALUES
('Antananarivo', 1),
('Ambohidratrimo', 1),
('Toamasina', 2),
('Brickaville', 2),
('Antsirabe', 3),
('Betafo', 3),
('Miarinarivo', 4),
('Arivonimamo', 4),
('Tsiroanomandidy', 5),
('Bongolava Ville', 5),
('Ambatondrazaka', 6),
('Moramanga', 6),
('Sambava', 7),
('Andapa', 7),
('Antsiranana', 8),
('Ambilobe', 8),
('Antsohihy', 9),
('Befandriana-Nord', 9),
('Mahajanga', 10),
('Boeny Ville', 10),
('Maintirano', 11),
('Melaky Ville', 11),
('Morondava', 12),
('Manja', 12),
('Toliara', 13),
('Ampanihy', 13),
('Ambovombe-Androy', 14),
('Androy Ville', 14),
('Tolagnaro', 15),
('Atsimo-Atsinanana Ville', 15);

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
