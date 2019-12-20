-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 19 déc. 2019 à 07:54
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `oc_projets_n5`
--
CREATE DATABASE IF NOT EXISTS `oc_projets_n5` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `oc_projets_n5`;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category` varchar(155) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_post` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  `title` varchar(155) NOT NULL,
  `content` text NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 en attente - 1 valide - 2 archive - 3 suppression',
  PRIMARY KEY (`id`),
  KEY `state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(155) NOT NULL,
  `kicker` tinytext NOT NULL,
  `author` int(10) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` date NOT NULL,
  `modified_at` datetime NOT NULL,
  `id_category` int(10) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 en attente - 1 valide - 2 archive - 3 suppression',
  PRIMARY KEY (`id`),
  KEY `title` (`title`,`kicker`(255)),
  KEY `modified_at` (`modified_at`),
  KEY `id_category` (`id_category`),
  KEY `created_at` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(155) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1 - admin 2 - user',
  `email` varchar(155) NOT NULL,
  `password` varchar(45) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 en attente - 1 valide - 2 suppression',
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `pseudo` (`pseudo`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;