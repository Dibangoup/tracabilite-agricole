-- =============================================
-- Mise à jour du schéma — Traçabilité Agricole
-- =============================================
USE `tracabilite-agricole-db`;

-- 1. Rôles manquants
ALTER TABLE `users`
  MODIFY COLUMN `role` ENUM('producteur','cooperative','transporteur','transformateur','distributeur','consommateur') NOT NULL DEFAULT 'consommateur';

-- 2. Colonnes produits
ALTER TABLE `produits`
  ADD COLUMN `categorie` VARCHAR(100) DEFAULT 'autre' AFTER `type`,
  ADD COLUMN `date_peremption` DATE DEFAULT NULL AFTER `code_unique`,
  ADD COLUMN `quantite` VARCHAR(50) DEFAULT NULL AFTER `categorie`,
  ADD COLUMN `description` TEXT DEFAULT NULL AFTER `quantite`;

-- 3. Date péremption dans étapes
ALTER TABLE `etapes_tracabilite`
  ADD COLUMN `date_peremption` DATE DEFAULT NULL AFTER `date_etape`;

-- 4. Étape distribution
ALTER TABLE `etapes_tracabilite`
  MODIFY COLUMN `etape` ENUM('recolte','transport','stockage','transformation','distribution','vente') NOT NULL;

-- 5. Historique de recherche
CREATE TABLE IF NOT EXISTS `historique_recherche` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `produit_id` INT NOT NULL,
  `code_recherche` VARCHAR(100) NOT NULL,
  `date_recherche` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produit_id`) REFERENCES `produits`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
