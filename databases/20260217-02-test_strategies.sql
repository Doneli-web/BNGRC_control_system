-- Exemple de données pour tester les 3 stratégies de dispatch
-- Table: BNGRC_don

-- Dons de riz (idArticle = 1)
INSERT INTO BNGRC_don (idArticle, quantite, status, date_de_saisie) VALUES
  (1, 50, 'non_utilise', '2026-02-17 08:00:00'),
  (1, 300, 'non_utilise', '2026-02-17 08:20:00'),
  (1, 200, 'non_utilise', '2026-02-17 08:10:00');

-- Table: BNGRC_besoin

-- Besoins de riz dans 3 villes différentes
INSERT INTO BNGRC_besoin (idVille, idArticle, quantite, status, date_de_saisie) VALUES
  (1, 1, 100, 'non_commence', '2026-02-17 09:00:00'),
  (2, 1, 200, 'non_commence', '2026-02-17 09:10:00'),
  (3, 1, 300, 'non_commence', '2026-02-17 09:20:00');

-- Les besoins sont volontairement de quantités différentes pour illustrer :
-- - oldest: priorité à l'ordre de création
-- - smallest: priorité au besoin de 100, puis 200, puis 300
-- - proportional: chaque ville reçoit une part proportionnelle à son besoin
