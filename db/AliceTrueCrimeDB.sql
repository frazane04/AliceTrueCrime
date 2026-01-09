-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Creato il: Gen 09, 2026 alle 01:07
-- Versione del server: 8.0.44
-- Versione PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `AliceTrueCrimeDB`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Articolo`
--

CREATE TABLE `Articolo` (
  `ID_Articolo` int NOT NULL,
  `Titolo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `Data` date NOT NULL,
  `Link` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Caso` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Articolo`
--

INSERT INTO `Articolo` (`ID_Articolo`, `Titolo`, `Data`, `Link`, `Caso`) VALUES
(1, 'Approfondimento su Dahmer', '2001-05-19', 'https://news.it/1', 1),
(2, 'Approfondimento su Gacy', '2003-01-15', 'https://news.it/2', 2),
(3, 'Approfondimento su Bundy', '2002-02-11', 'https://news.it/3', 3),
(4, 'Approfondimento su Chikatilo', '1987-08-06', 'https://news.it/4', 4),
(5, 'Approfondimento su Ramirez', '2015-12-17', 'https://news.it/5', 5),
(6, 'Approfondimento su Berkowitz', '1994-09-07', 'https://news.it/6', 6),
(7, 'Approfondimento su Zodiac', '1982-02-14', 'https://news.it/7', 7),
(8, 'Approfondimento su Ridgway', '1970-05-22', 'https://news.it/8', 8),
(9, 'Approfondimento su Jack', '2022-10-14', 'https://news.it/9', 9),
(10, 'Approfondimento su Gein', '1990-01-21', 'https://news.it/10', 10),
(11, 'Approfondimento su Franzoni', '2019-05-25', 'https://news.it/11', 11),
(12, 'Approfondimento su Misseri', '1972-01-04', 'https://news.it/12', 12),
(13, 'Approfondimento su Bossetti', '1975-02-25', 'https://news.it/13', 13),
(14, 'Approfondimento su Guede', '2006-10-05', 'https://news.it/14', 14),
(15, 'Approfondimento su Pacciani', '1980-02-26', 'https://news.it/15', 15),
(16, 'Approfondimento su Romano', '2014-10-02', 'https://news.it/16', 16),
(17, 'Approfondimento su Stasi', '2012-10-03', 'https://news.it/17', 17),
(18, 'Approfondimento su Parolisi', '2000-03-15', 'https://news.it/18', 18),
(19, 'Approfondimento su De Nardo', '2017-05-07', 'https://news.it/19', 19),
(20, 'Approfondimento su Ciontoli', '1992-03-28', 'https://news.it/20', 20),
(21, 'Approfondimento su Vicious', '1985-02-07', 'https://news.it/21', 21),
(22, 'Approfondimento su Pistorius', '1975-07-04', 'https://news.it/22', 22),
(23, 'Approfondimento su Cantat', '2019-11-01', 'https://news.it/23', 23),
(24, 'Approfondimento su Arias', '1998-02-05', 'https://news.it/24', 24),
(25, 'Approfondimento su Spector', '2017-04-09', 'https://news.it/25', 25),
(26, 'Approfondimento su Simpson', '1970-01-22', 'https://news.it/26', 26),
(27, 'Approfondimento su Prato', '1994-07-05', 'https://news.it/27', 27),
(28, 'Approfondimento su Impagnatiello', '2004-10-03', 'https://news.it/28', 28),
(29, 'Approfondimento su Watts', '1983-11-22', 'https://news.it/29', 29),
(30, 'Approfondimento su Laundrie', '2018-02-09', 'https://news.it/30', 30),
(31, 'Approfondimento su Cunanan', '2007-02-03', 'https://news.it/31', 31),
(32, 'Approfondimento su Manson', '1984-05-12', 'https://news.it/32', 32),
(33, 'Approfondimento su Chapman', '2006-08-03', 'https://news.it/33', 33),
(34, 'Approfondimento su Anderson', '2018-06-18', 'https://news.it/34', 34),
(35, 'Approfondimento su Killer', '1996-11-19', 'https://news.it/35', 35),
(36, 'Approfondimento su Gay Sr.', '1973-10-05', 'https://news.it/36', 36),
(37, 'Approfondimento su Saldivar', '2015-09-03', 'https://news.it/37', 37),
(38, 'Approfondimento su Gale', '2006-02-26', 'https://news.it/38', 38),
(39, 'Approfondimento su Loibl', '1977-03-27', 'https://news.it/39', 39),
(40, 'Approfondimento su Ceraulo', '2011-09-13', 'https://news.it/40', 40),
(41, 'Approfondimento su Gay', '2003-01-15', 'https://news.it/3', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `Caso`
--

CREATE TABLE `Caso` (
  `N_Caso` int NOT NULL,
  `Titolo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Data` date NOT NULL,
  `Luogo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Descrizione` text COLLATE utf8mb4_general_ci NOT NULL,
  `Storia` text COLLATE utf8mb4_general_ci NOT NULL,
  `Tipologia` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Immagine` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Approvato` tinyint(1) DEFAULT '0',
  `Visualizzazioni` int DEFAULT '0',
  `Data_Inserimento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Autore` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Caso`
--

INSERT INTO `Caso` (`N_Caso`, `Titolo`, `Slug`, `Data`, `Luogo`, `Descrizione`, `Storia`, `Tipologia`, `Immagine`, `Approvato`, `Visualizzazioni`, `Data_Inserimento`, `Autore`) VALUES
(1, 'Il mostro di Milwaukee', 'il-mostro-di-milwaukee', '2001-05-19', 'Milwaukee, USA', 'Jeffrey Dahmer ha ucciso e smembrato 17 uomini tra il 1978 e il 1991.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(2, 'Il Clown Killer', 'il-clown-killer', '2003-01-15', 'Chicago, USA', 'John Wayne Gacy, vestito da clown, ha ucciso 33 adolescenti.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(3, 'Ted Bundy', 'ted-bundy', '2002-02-11', 'Florida, USA', 'Uno dei serial killer più famosi, ha confessato 30 omicidi.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(4, 'Il Macellaio di Rostov', 'il-macellaio-di-rostov', '1987-08-06', 'Rostov, Russia', 'Andrei Chikatilo ha mutilato e ucciso oltre 50 donne e bambini.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(5, 'Night Stalker', 'night-stalker', '2015-12-17', 'Los Angeles, USA', 'Richard Ramirez terrorizzò LA con omicidi satanici.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(6, 'Il figlio di Sam', 'il-figlio-di-sam', '1994-09-07', 'New York, USA', 'David Berkowitz sparava alle coppe appartate in auto.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(7, 'Zodiac Killer', 'zodiac-killer', '1982-02-14', 'California, USA', 'Il killer mai identificato che mandava codici ai giornali.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(8, 'Green River Killer', 'green-river-killer', '1970-05-22', 'Washington, USA', 'Gary Ridgway ha ucciso 49 donne confermate.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(9, 'Jack lo Squartatore', 'jack-lo-squartatore', '2022-10-14', 'Londra, UK', 'Il primo serial killer mediatico della storia a Whitechapel.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(10, 'Ed Gein', 'ed-gein', '1990-01-21', 'Wisconsin, USA', 'Il killer che ispirò Psycho, creava oggetti con pelle umana.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(11, 'Delitto di Cogne', 'delitto-di-cogne', '2019-05-25', 'Cogne', 'La madre Annamaria Franzoni accusata dell\'omicidio del figlio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(12, 'Delitto di Avetrana', 'delitto-di-avetrana', '1972-01-04', 'Avetrana', 'L\'omicidio della giovane Sarah Scazzi e il coinvolgimento della famiglia.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(13, 'Omicidio di Yara', 'omicidio-di-yara', '1975-02-25', 'Brembate', 'Il caso risolto grazie al DNA di Ignoto 1.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(14, 'Delitto di Perugia', 'delitto-di-perugia', '2006-10-05', 'Perugia', 'L\'omicidio di Meredith Kercher che coinvolse studenti internazionali.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(15, 'Il Mostro di Firenze', 'il-mostro-di-firenze', '1980-02-26', 'Firenze', 'Serie di duplici omicidi avvenuti tra il 1968 e il 1985.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(16, 'Strage di Erba', 'strage-di-erba', '2014-10-02', 'Erba', 'Olindo e Rosa uccidono 4 persone in un condominio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(17, 'Delitto di Garlasco', 'delitto-di-garlasco', '2012-10-03', 'Garlasco', 'L\'omicidio di Chiara Poggi nella sua villetta.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(18, 'Caso Melania Rea', 'caso-melania-rea', '2000-03-15', 'Teramo', 'Salvatore Parolisi uccide la moglie in un bosco.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(19, 'Erika e Omar', 'erika-e-omar', '2017-05-07', 'Novi Ligure', 'Due fidanzatini uccidono madre e fratellino di lei.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(20, 'Marco Vannini', 'marco-vannini', '1992-03-28', 'Ladispoli', 'La morte del giovane Marco in casa della fidanzata Ciontoli.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 1, 0, '2026-01-08 10:38:13', ''),
(21, 'Sid e Nancy', 'sid-e-nancy', '1985-02-07', 'New York', 'Il bassista dei Sex Pistols uccide la fidanzata.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(22, 'Il caso Pistorius', 'il-caso-pistorius', '1975-07-04', 'Pretoria', 'L\'atleta paralimpico spara alla fidanzata credendola un ladro.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(23, 'Bertrand Cantat', 'bertrand-cantat', '2019-11-01', 'Vilnius', 'Il cantante dei Noir Desir picchia a morte l\'attrice Marie Trintignant.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(24, 'Jodi Arias', 'jodi-arias', '1998-02-05', 'Arizona, USA', 'Uccide l\'ex fidanzato Travis Alexander per gelosia morbosa.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(25, 'Phil Spector', 'phil-spector', '2017-04-09', 'California', 'Il produttore musicale uccide l\'attrice Lana Clarkson.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(26, 'Caso O.J. Simpson', 'caso-o.j.-simpson', '1970-01-22', 'Los Angeles', 'L\'ex campione accusato di aver ucciso l\'ex moglie.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(27, 'Luca Varani', 'luca-varani', '1994-07-05', 'Roma', 'Ucciso durante un festino a base di droghe e follia.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(28, 'Femminicidio Tramontano', 'femminicidio-tramontano', '2004-10-03', 'Milano', 'Giulia uccisa al settimo mese di gravidanza dal compagno.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(29, 'Shannan Watts', 'shannan-watts', '1983-11-22', 'Colorado', 'Chris Watts uccide moglie incinta e figlie per l\'amante.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(30, 'Gabby Petito', 'gabby-petito', '2018-02-09', 'Wyoming', 'Strangolata dal fidanzato durante un viaggio in van.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 1, 0, '2026-01-08 10:38:13', ''),
(31, 'Gianni Versace', 'gianni-versace', '2007-02-03', 'Miami', 'Lo stilista ucciso sugli scalini di casa.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(32, 'Sharon Tate', 'sharon-tate', '1984-05-12', 'Los Angeles', 'L\'attrice uccisa dalla Manson Family.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(33, 'John Lennon', 'john-lennon', '2006-08-03', 'New York', 'L\'ex Beatle ucciso da un fan ossessionato.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', '');
INSERT INTO `Caso` (`N_Caso`, `Titolo`, `Slug`, `Data`, `Luogo`, `Descrizione`, `Storia`, `Tipologia`, `Immagine`, `Approvato`, `Visualizzazioni`, `Data_Inserimento`, `Autore`) VALUES
(34, 'Tupac Shakur', 'tupac-shakur', '2018-06-18', 'Las Vegas', 'Il rapper ucciso in una sparatoria tra gang.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(35, 'Notorious B.I.G.', 'notorious-b.i.g.', '1996-11-19', 'Los Angeles', 'Ucciso pochi mesi dopo Tupac.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(36, 'Marvin Gaye', 'marvin-gaye', '1973-10-05', 'Los Angeles', 'Il cantante ucciso dal proprio padre dopo una lite.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(37, 'Selena', 'selena', '2015-09-03', 'Texas', 'La regina della musica tejano uccisa dalla presidente del fan club.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(38, 'Dimebag Darrell', 'dimebag-darrell', '2006-02-26', 'Ohio', 'Il chitarrista dei Pantera ucciso sul palco.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(39, 'Christina Grimmie', 'christina-grimmie', '1977-03-27', 'Orlando', 'La cantante di The Voice uccisa mentre firmava autografi.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(40, 'Maurizio Gucci', 'maurizio-gucci', '2011-09-13', 'Milano', 'L\'erede della moda fatto uccidere dall\'ex moglie.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 1, 0, '2026-01-08 10:38:13', ''),
(41, 'Morte nel Vicolo', 'morte-nel-vicolo', '2025-12-17', 'Torino', 'Un caso irrisolto degli anni 90 riaperto oggi.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 0, 0, '2026-01-08 10:38:13', ''),
(42, 'Il veleno del caffè', 'il-veleno-del-caffe', '2025-12-17', 'Napoli', 'Avvelenamento seriale in una casa di cura.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 1, 0, '2026-01-08 10:38:13', ''),
(43, 'Scomparsa sul Lago', 'scomparsa-sul-lago', '2025-12-17', 'Como', 'Una turista svanita nel nulla, si sospetta il marito.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 0, 0, '2026-01-08 10:38:13', ''),
(44, 'Il segreto del prete', 'il-segreto-del-prete', '2025-12-17', 'Potenza', 'Ritrovamento osseo in una chiesa antica.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 0, 0, '2026-01-08 10:38:13', ''),
(45, 'Gelosia fatale', 'gelosia-fatale', '2025-12-17', 'Bologna', 'Due amanti, un coltello e una notte di follia.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 0, 0, '2026-01-08 10:38:13', ''),
(46, 'Tradimento online', 'tradimento-online', '2025-12-17', 'Milano', 'Adesca la moglie su internet per ucciderla.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Amore tossico', '', 0, 0, '2026-01-08 10:38:13', ''),
(47, 'Lo stalker della TV', 'lo-stalker-della-tv', '2025-12-17', 'Roma', 'Conduttore perseguitato da una fan.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 0, 0, '2026-01-08 10:38:13', ''),
(48, 'Rapimento Lampo', 'rapimento-lampo', '2025-12-17', 'Venezia', 'Influencer rapita per riscatto finito male.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Celebrity', '', 0, 0, '2026-01-08 10:38:13', ''),
(49, 'Il mostro del Po', 'il-mostro-del-po', '2025-12-17', 'Rovigo', 'Pescatore trova resti umani.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Serial killer', '', 0, 0, '2026-01-08 10:38:13', ''),
(50, 'Delitto di Pasqua', 'delitto-di-pasqua', '2025-12-17', 'Bari', 'Lite in famiglia finisce in tragedia.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'Casi mediatici italiani', '', 0, 0, '2026-01-08 10:38:13', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `Colpa`
--

CREATE TABLE `Colpa` (
  `Colpevole` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `Caso` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Colpa`
--

INSERT INTO `Colpa` (`Colpevole`, `Caso`) VALUES
('CLP001DAHX', 1),
('CLP002GACX', 2),
('CLP003BUNX', 3),
('CLP004CHIX', 4),
('CLP005RAMX', 5),
('CLP006BERX', 6),
('CLP007ZODX', 7),
('CLP008RIDX', 8),
('CLP009JACX', 9),
('CLP010GEIX', 10),
('CLP011FRAX', 11),
('CLP012MISX', 12),
('CLP013BOSX', 13),
('CLP014GUEX', 14),
('CLP015PACX', 15),
('CLP016ROMX', 16),
('CLP017STAX', 17),
('CLP018PARX', 18),
('CLP019DE X', 19),
('CLP020CIOX', 20),
('CLP021VICX', 21),
('CLP022PISX', 22),
('CLP023CANX', 23),
('CLP024ARIX', 24),
('CLP025SPEX', 25),
('CLP026SIMX', 26),
('CLP027PRAX', 27),
('CLP028IMPX', 28),
('CLP029WATX', 29),
('CLP030LAUX', 30),
('CLP031CUNX', 31),
('CLP032MANX', 32),
('CLP033CHAX', 33),
('CLP034ANDX', 34),
('CLP035KILX', 35),
('CLP036GAYX', 36),
('CLP037SALX', 37),
('CLP038GALX', 38),
('CLP039LOIX', 39),
('CLP040CERX', 40),
('PEN041XXX', 41),
('PEN042XXX', 42),
('PEN043XXX', 43),
('PEN044XXX', 44),
('PEN045XXX', 45),
('PEN046XXX', 46),
('PEN047XXX', 47),
('PEN048XXX', 48),
('PEN049XXX', 49),
('PEN050XXX', 50);

-- --------------------------------------------------------

--
-- Struttura della tabella `Colpevole`
--

CREATE TABLE `Colpevole` (
  `CF_Colpevole` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Cognome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `LuogoNascita` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `DataNascita` date NOT NULL,
  `Immagine` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Colpevole`
--

INSERT INTO `Colpevole` (`CF_Colpevole`, `Nome`, `Cognome`, `LuogoNascita`, `DataNascita`, `Immagine`) VALUES
('CLP001DAHX', 'Jeffrey', 'Dahmer', 'Ignoto', '1970-01-01', ''),
('CLP002GACX', 'John', 'Gacy', 'Ignoto', '1970-01-01', ''),
('CLP003BUNX', 'Ted', 'Bundy', 'Ignoto', '1970-01-01', ''),
('CLP004CHIX', 'Andrei', 'Chikatilo', 'Ignoto', '1970-01-01', ''),
('CLP005RAMX', 'Richard', 'Ramirez', 'Ignoto', '1970-01-01', ''),
('CLP006BERX', 'David', 'Berkowitz', 'Ignoto', '1970-01-01', ''),
('CLP007ZODX', 'Ignoto', 'Zodiac', 'Ignoto', '1970-01-01', ''),
('CLP008RIDX', 'Gary', 'Ridgway', 'Ignoto', '1970-01-01', ''),
('CLP009JACX', 'Ignoto', 'Jack', 'Ignoto', '1970-01-01', ''),
('CLP010GEIX', 'Ed', 'Gein', 'Ignoto', '1970-01-01', ''),
('CLP011FRAX', 'Annamaria', 'Franzoni', 'Ignoto', '1970-01-01', ''),
('CLP012MISX', 'Sabrina', 'Misseri', 'Ignoto', '1970-01-01', ''),
('CLP013BOSX', 'Massimo', 'Bossetti', 'Ignoto', '1970-01-01', ''),
('CLP014GUEX', 'Rudy', 'Guede', 'Ignoto', '1970-01-01', ''),
('CLP015PACX', 'Pietro', 'Pacciani', 'Ignoto', '1970-01-01', ''),
('CLP016ROMX', 'Olindo', 'Romano', 'Ignoto', '1970-01-01', ''),
('CLP017STAX', 'Alberto', 'Stasi', 'Ignoto', '1970-01-01', ''),
('CLP018PARX', 'Salvatore', 'Parolisi', 'Ignoto', '1970-01-01', ''),
('CLP019DE X', 'Erika', 'De Nardo', 'Ignoto', '1970-01-01', ''),
('CLP020CIOX', 'Antonio', 'Ciontoli', 'Ignoto', '1970-01-01', ''),
('CLP021VICX', 'Sid', 'Vicious', 'Ignoto', '1970-01-01', ''),
('CLP022PISX', 'Oscar', 'Pistorius', 'Ignoto', '1970-01-01', ''),
('CLP023CANX', 'Bertrand', 'Cantat', 'Ignoto', '1970-01-01', ''),
('CLP024ARIX', 'Jodi', 'Arias', 'Ignoto', '1970-01-01', ''),
('CLP025SPEX', 'Phil', 'Spector', 'Ignoto', '1970-01-01', ''),
('CLP026SIMX', 'O.J.', 'Simpson', 'Ignoto', '1970-01-01', ''),
('CLP027PRAX', 'Marco', 'Prato', 'Ignoto', '1970-01-01', ''),
('CLP028IMPX', 'Alessandro', 'Impagnatiello', 'Ignoto', '1970-01-01', ''),
('CLP029WATX', 'Chris', 'Watts', 'Ignoto', '1970-01-01', ''),
('CLP030LAUX', 'Brian', 'Laundrie', 'Ignoto', '1970-01-01', ''),
('CLP031CUNX', 'Andrew', 'Cunanan', 'Ignoto', '1970-01-01', ''),
('CLP032MANX', 'Charles', 'Manson', 'Ignoto', '1970-01-01', ''),
('CLP033CHAX', 'Mark', 'Chapman', 'Ignoto', '1970-01-01', ''),
('CLP034ANDX', 'Orlando', 'Anderson', 'Ignoto', '1970-01-01', ''),
('CLP035KILX', 'Ignoto', 'Killer', 'Ignoto', '1970-01-01', ''),
('CLP036GAYX', 'Marvin', 'Gay Sr.', 'Ignoto', '1970-01-01', ''),
('CLP037SALX', 'Yolanda', 'Saldivar', 'Ignoto', '1970-01-01', ''),
('CLP038GALX', 'Nathan', 'Gale', 'Ignoto', '1970-01-01', ''),
('CLP039LOIX', 'Kevin', 'Loibl', 'Ignoto', '1970-01-01', ''),
('CLP040CERX', 'Benedetto', 'Ceraulo', 'Ignoto', '1970-01-01', ''),
('PEN041XXX', 'Ignoto', 'X', 'N/A', '1990-01-01', ''),
('PEN042XXX', 'Luisa', 'Bianchi', 'N/A', '1990-01-01', ''),
('PEN043XXX', 'Hans', 'Muller', 'N/A', '1990-01-01', ''),
('PEN044XXX', 'Don', 'Giulio', 'N/A', '1990-01-01', ''),
('PEN045XXX', 'Luca', 'Neri', 'N/A', '1990-01-01', ''),
('PEN046XXX', 'Marco', 'Viola', 'N/A', '1990-01-01', ''),
('PEN047XXX', 'Carla', 'Bruni', 'N/A', '1990-01-01', ''),
('PEN048XXX', 'Bandito', 'Uno', 'N/A', '1990-01-01', ''),
('PEN049XXX', 'Ignoto', 'Y', 'N/A', '1990-01-01', ''),
('PEN050XXX', 'Zio', 'Peppe', 'N/A', '1990-01-01', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `Commento`
--

CREATE TABLE `Commento` (
  `ID_Commento` int NOT NULL,
  `Commento` text COLLATE utf8mb4_general_ci NOT NULL,
  `Email_Utente` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `ID_Caso` int NOT NULL,
  `Data` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Commento`
--

INSERT INTO `Commento` (`ID_Commento`, `Commento`, `Email_Utente`, `ID_Caso`, `Data`) VALUES
(2, 'Napoleone Suca', 'lore.grolla04@gmail.com', 9, '2026-01-09 01:55:05');

-- --------------------------------------------------------

--
-- Struttura della tabella `Utente`
--

CREATE TABLE `Utente` (
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Is_Admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Utente`
--

INSERT INTO `Utente` (`Email`, `Username`, `Password`, `Is_Admin`) VALUES
('admin@test.it', 'Admin', '$2y$10$TctKk6xDjIzLPDGo.Cky6.h3yrev5Qh9qY9mY1JXKGI3DFWs.KPVK', 1),
('lore.grolla04@gmail.com', 'Lorenzo', '$2y$10$RPrVyIOXQguBlR6kRDz2h.KqskfkwyFmKGXGwlbxIil3V06DCMSFm', 0),
('paolo@gmail.com', 'Paolo', '$2y$10$aW49yojpjRL7JsBVv9TlZ.a7119SoKRteJ8XA8.U9rNC/3Xlj8V7i', 0),
('user@test.it', 'User', '$2y$10$9dk6FVWLhA3i.6JtCcUERuz0cW.v7GI3fyuztVmq/5YfB7G/IkEg2', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `Vittima`
--

CREATE TABLE `Vittima` (
  `CF_Vittima` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Cognome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `LuogoNascita` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `DataNascita` date NOT NULL,
  `DataDecesso` date DEFAULT NULL,
  `Caso` int NOT NULL,
  `Immagine` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Vittima`
--

INSERT INTO `Vittima` (`CF_Vittima`, `Nome`, `Cognome`, `LuogoNascita`, `DataNascita`, `DataDecesso`, `Caso`, `Immagine`) VALUES
('PEN041YYY', 'Mario', 'Rossi', 'N/A', '1995-01-01', '2025-12-17', 41, ''),
('PEN042YYY', 'Anna', 'Verdi', 'N/A', '1995-01-01', '2025-12-17', 42, ''),
('PEN042ZZZ', 'Anna', 'Violetta', 'N/A', '1995-01-01', '2025-12-17', 42, ''),
('PEN043YYY', 'Greta', 'Schmidt', 'N/A', '1995-01-01', '2025-12-17', 43, ''),
('PEN044YYY', 'Elisa', 'Claps', 'N/A', '1995-01-01', '2025-12-17', 44, ''),
('PEN045YYY', 'Sara', 'Gialli', 'N/A', '1995-01-01', '2025-12-17', 45, ''),
('PEN046YYY', 'Lisa', 'Rosa', 'N/A', '1995-01-01', '2025-12-17', 46, ''),
('PEN047YYY', 'Pippo', 'Baudo', 'N/A', '1995-01-01', '2025-12-17', 47, ''),
('PEN048YYY', 'Chiara', 'Nasti', 'N/A', '1995-01-01', '2025-12-17', 48, ''),
('PEN049YYY', 'Luigi', 'B.', 'N/A', '1995-01-01', '2025-12-17', 49, ''),
('PEN050YYY', 'Nipote', 'Franco', 'N/A', '1995-01-01', '2025-12-17', 50, ''),
('VIT001HICX', 'Steven', 'Hicks', 'Milwaukee, USA', '1980-01-01', '2001-05-19', 1, ''),
('VIT002MCCX', 'Timothy', 'McCoy', 'Chicago, USA', '1980-01-01', '2003-01-15', 2, ''),
('VIT003LEAX', 'Kimberly', 'Leach', 'Florida, USA', '1980-01-01', '2002-02-11', 3, ''),
('VIT004ZAKX', 'Yelena', 'Zakotnova', 'Rostov, Russia', '1980-01-01', '1987-08-06', 4, ''),
('VIT005LEUX', 'Mei', 'Leung', 'Los Angeles, USA', '1980-01-01', '2015-12-17', 5, ''),
('VIT006LAUX', 'Donna', 'Lauria', 'New York, USA', '1980-01-01', '1994-09-07', 6, ''),
('VIT007FARX', 'David', 'Faraday', 'California, USA', '1980-01-01', '1982-02-14', 7, ''),
('VIT008COFX', 'Wendy', 'Coffield', 'Washington, USA', '1980-01-01', '1970-05-22', 8, ''),
('VIT009NICX', 'Mary', 'Nichols', 'Londra, UK', '1980-01-01', '2022-10-14', 9, ''),
('VIT010WORX', 'Bernice', 'Worden', 'Wisconsin, USA', '1980-01-01', '1990-01-21', 10, ''),
('VIT011LORX', 'Samuele', 'Lorenzi', 'Cogne', '1980-01-01', '2019-05-25', 11, ''),
('VIT012SCAX', 'Sarah', 'Scazzi', 'Avetrana', '1980-01-01', '1972-01-04', 12, ''),
('VIT013GAMX', 'Yara', 'Gambirasio', 'Brembate', '1980-01-01', '1975-02-25', 13, ''),
('VIT014KERX', 'Meredith', 'Kercher', 'Perugia', '1980-01-01', '2006-10-05', 14, ''),
('VIT015PETX', 'Stefania', 'Pettini', 'Firenze', '1980-01-01', '1980-02-26', 15, ''),
('VIT016CASX', 'Raffaella', 'Castagna', 'Erba', '1980-01-01', '2014-10-02', 16, ''),
('VIT017POGX', 'Chiara', 'Poggi', 'Garlasco', '1980-01-01', '2012-10-03', 17, ''),
('VIT018REAX', 'Melania', 'Rea', 'Teramo', '1980-01-01', '2000-03-15', 18, ''),
('VIT019CASX', 'Susy', 'Cassini', 'Novi Ligure', '1980-01-01', '2017-05-07', 19, ''),
('VIT020VANX', 'Marco', 'Vannini', 'Ladispoli', '1980-01-01', '1992-03-28', 20, ''),
('VIT021SPUX', 'Nancy', 'Spungen', 'New York', '1980-01-01', '1985-02-07', 21, ''),
('VIT022STEX', 'Reeva', 'Steenkamp', 'Pretoria', '1980-01-01', '1975-07-04', 22, ''),
('VIT023TRIX', 'Marie', 'Trintignant', 'Vilnius', '1980-01-01', '2019-11-01', 23, ''),
('VIT024ALEX', 'Travis', 'Alexander', 'Arizona, USA', '1980-01-01', '1998-02-05', 24, ''),
('VIT025CLAX', 'Lana', 'Clarkson', 'California', '1980-01-01', '2017-04-09', 25, ''),
('VIT026BROX', 'Nicole', 'Brown', 'Los Angeles', '1980-01-01', '1970-01-22', 26, ''),
('VIT027VARX', 'Luca', 'Varani', 'Roma', '1980-01-01', '1994-07-05', 27, ''),
('VIT028TRAX', 'Giulia', 'Tramontano', 'Milano', '1980-01-01', '2004-10-03', 28, ''),
('VIT029WATX', 'Shannan', 'Watts', 'Colorado', '1980-01-01', '1983-11-22', 29, ''),
('VIT030PETX', 'Gabby', 'Petito', 'Wyoming', '1980-01-01', '2018-02-09', 30, ''),
('VIT031VERX', 'Gianni', 'Versace', 'Miami', '1980-01-01', '2007-02-03', 31, ''),
('VIT032TATX', 'Sharon', 'Tate', 'Los Angeles', '1980-01-01', '1984-05-12', 32, ''),
('VIT033LENX', 'John', 'Lennon', 'New York', '1980-01-01', '2006-08-03', 33, ''),
('VIT034SHAX', 'Tupac', 'Shakur', 'Las Vegas', '1980-01-01', '2018-06-18', 34, ''),
('VIT035WALX', 'C.', 'Wallace', 'Los Angeles', '1980-01-01', '1996-11-19', 35, ''),
('VIT036GAYX', 'Marvin', 'Gaye', 'Los Angeles', '1980-01-01', '1973-10-05', 36, ''),
('VIT037QUIX', 'Selena', 'Quintanilla', 'Texas', '1980-01-01', '2015-09-03', 37, ''),
('VIT038ABBX', 'Darrell', 'Abbott', 'Ohio', '1980-01-01', '2006-02-26', 38, ''),
('VIT039GRIX', 'Christina', 'Grimmie', 'Orlando', '1980-01-01', '1977-03-27', 39, ''),
('VIT040GUCX', 'Maurizio', 'Gucci', 'Milano', '1980-01-01', '2011-09-13', 40, '');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Articolo`
--
ALTER TABLE `Articolo`
  ADD PRIMARY KEY (`ID_Articolo`),
  ADD KEY `Caso` (`Caso`);

--
-- Indici per le tabelle `Caso`
--
ALTER TABLE `Caso`
  ADD PRIMARY KEY (`N_Caso`),
  ADD UNIQUE KEY `Slug` (`Slug`),
  ADD KEY `idx_visualizzazioni` (`Visualizzazioni` DESC),
  ADD KEY `idx_approvato` (`Approvato`),
  ADD KEY `idx_n_caso_desc` (`N_Caso` DESC);

--
-- Indici per le tabelle `Colpa`
--
ALTER TABLE `Colpa`
  ADD PRIMARY KEY (`Colpevole`,`Caso`),
  ADD KEY `Caso` (`Caso`);

--
-- Indici per le tabelle `Colpevole`
--
ALTER TABLE `Colpevole`
  ADD PRIMARY KEY (`CF_Colpevole`);

--
-- Indici per le tabelle `Commento`
--
ALTER TABLE `Commento`
  ADD PRIMARY KEY (`ID_Commento`),
  ADD KEY `Email_Utente` (`Email_Utente`),
  ADD KEY `ID_Caso` (`ID_Caso`);

--
-- Indici per le tabelle `Utente`
--
ALTER TABLE `Utente`
  ADD PRIMARY KEY (`Email`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indici per le tabelle `Vittima`
--
ALTER TABLE `Vittima`
  ADD PRIMARY KEY (`CF_Vittima`),
  ADD KEY `Caso` (`Caso`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Articolo`
--
ALTER TABLE `Articolo`
  MODIFY `ID_Articolo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT per la tabella `Caso`
--
ALTER TABLE `Caso`
  MODIFY `N_Caso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT per la tabella `Commento`
--
ALTER TABLE `Commento`
  MODIFY `ID_Commento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Articolo`
--
ALTER TABLE `Articolo`
  ADD CONSTRAINT `Articolo_ibfk_1` FOREIGN KEY (`Caso`) REFERENCES `Caso` (`N_Caso`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `Colpa`
--
ALTER TABLE `Colpa`
  ADD CONSTRAINT `Colpa_ibfk_1` FOREIGN KEY (`Colpevole`) REFERENCES `Colpevole` (`CF_Colpevole`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Colpa_ibfk_2` FOREIGN KEY (`Caso`) REFERENCES `Caso` (`N_Caso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Commento`
--
ALTER TABLE `Commento`
  ADD CONSTRAINT `Commento_ibfk_1` FOREIGN KEY (`Email_Utente`) REFERENCES `Utente` (`Email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Commento_ibfk_2` FOREIGN KEY (`ID_Caso`) REFERENCES `Caso` (`N_Caso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Vittima`
--
ALTER TABLE `Vittima`
  ADD CONSTRAINT `Vittima_ibfk_1` FOREIGN KEY (`Caso`) REFERENCES `Caso` (`N_Caso`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
