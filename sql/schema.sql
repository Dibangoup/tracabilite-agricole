-- =============================================
-- Base de données : tracabilite-agricole-db
-- Script de création des tables
-- =============================================

-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `tracabilite-agricole-db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

-- Sélectionner la base de données
USE `tracabilite-agricole-db`;

-- =============================================
-- Table : users
-- Stocke les comptes utilisateurs (producteurs, coopératives, transporteurs, consommateurs)
-- =============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `role` ENUM('producteur', 'cooperative', 'transporteur', 'consommateur') NOT NULL DEFAULT 'consommateur',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table : produits
-- Stocke les produits agricoles enregistrés par les producteurs
-- =============================================
CREATE TABLE IF NOT EXISTS `produits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `origine` VARCHAR(150) NOT NULL,
  `code_unique` VARCHAR(100) NOT NULL UNIQUE,
  `producteur_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`producteur_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table : etapes_tracabilite
-- Stocke les étapes du parcours d'un produit (récolte → transport → stockage → transformation → vente)
-- =============================================
CREATE TABLE IF NOT EXISTS `etapes_tracabilite` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `produit_id` INT NOT NULL,
  `acteur_id` INT NOT NULL,
  `etape` ENUM('recolte', 'transport', 'stockage', 'transformation', 'vente') NOT NULL,
  `description` TEXT,
  `lieu` VARCHAR(150),
  `date_etape` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`produit_id`) REFERENCES `produits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`acteur_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Table : avis
-- Stocke les avis des consommateurs sur les produits
-- =============================================
CREATE TABLE IF NOT EXISTS `avis` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `produit_id` INT NOT NULL,
  `nom_consommateur` VARCHAR(100) NOT NULL,
  `commentaire` TEXT,
  `note` INT NOT NULL CHECK (`note` BETWEEN 1 AND 5),
  `date_avis` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`produit_id`) REFERENCES `produits`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
