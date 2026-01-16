-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Creato il: Gen 16, 2026 alle 09:57
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
-- Struttura della tabella `articolo`
--

CREATE TABLE `articolo` (
  `ID_Articolo` int NOT NULL,
  `Titolo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `Data` date NOT NULL,
  `Link` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Caso` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `articolo`
--

INSERT INTO `articolo` (`ID_Articolo`, `Titolo`, `Data`, `Link`, `Caso`) VALUES
(1, 'Jeffrey Dahmer, storia del serial killer cannibale di Milwaukee', '2022-10-15', 'https://www.ilpost.it/2022/10/15/jeffrey-dahmer-serial-killer/', 1),
(2, 'Chi era Jeffrey Dahmer, il mostro di Milwaukee', '2022-09-28', 'https://www.corriere.it/esteri/22_settembre_28/chi-era-jeffrey-dahmer-mostro-milwaukee', 1),
(3, 'John Wayne Gacy, il clown killer che uccise 33 ragazzi', '2021-05-10', 'https://www.fanpage.it/cultura/john-wayne-gacy-il-clown-killer/', 2),
(4, 'La storia di John Wayne Gacy, il killer clown', '2019-04-05', 'https://www.ilpost.it/2019/04/05/john-wayne-gacy/', 2),
(5, 'Ted Bundy, il serial killer delle studentesse', '2019-01-26', 'https://www.corriere.it/esteri/19_gennaio_26/ted-bundy-serial-killer-studentesse', 3),
(6, 'Chi era Ted Bundy, il serial killer carismatico', '2019-05-03', 'https://www.ilpost.it/2019/05/03/ted-bundy/', 3),
(7, 'Andrej Chikatilo, il macellaio di Rostov', '2020-02-14', 'https://www.fanpage.it/cultura/andrej-chikatilo-macellaio-rostov/', 4),
(8, 'Richard Ramirez, la storia del Night Stalker', '2021-01-14', 'https://www.ilpost.it/2021/01/14/richard-ramirez-night-stalker/', 5),
(9, 'Il Mostro di Firenze, un caso ancora irrisolto', '2023-06-15', 'https://www.corriere.it/cronache/mostro-di-firenze/', 6),
(10, 'Mostro di Firenze: storia, vittime e misteri', '2022-09-08', 'https://www.repubblica.it/cronaca/mostro-firenze/', 6),
(11, 'Pacciani e i compagni di merende', '2018-02-21', 'https://www.ilpost.it/2018/02/21/mostro-firenze-pacciani/', 6),
(12, 'Donato Bilancia, il serial killer dei treni', '2020-12-17', 'https://www.repubblica.it/cronaca/donato-bilancia-morto/', 7),
(13, 'Morto Donato Bilancia, il serial killer più prolifico d\'Italia', '2020-12-17', 'https://www.corriere.it/cronache/bilancia-morto/', 7),
(14, 'Jack lo Squartatore, il mistero irrisolto di Whitechapel', '2018-11-09', 'https://www.ilpost.it/2018/11/09/jack-lo-squartatore/', 8),
(15, 'Zodiac Killer, decifrato il codice dopo 51 anni', '2020-12-12', 'https://www.ansa.it/sito/notizie/mondo/2020/12/12/zodiac-killer-decifrato-codice/', 9),
(16, 'Chi era lo Zodiac Killer', '2021-10-06', 'https://www.ilpost.it/2021/10/06/zodiac-killer/', 9),
(17, 'Ed Gein, il killer che ispirò Psycho', '2019-08-26', 'https://www.fanpage.it/cultura/ed-gein-killer-psycho/', 10),
(18, 'Delitto di Cogne, la storia del caso Franzoni', '2018-05-26', 'https://www.corriere.it/cronache/cogne-franzoni/', 11),
(19, 'Annamaria Franzoni torna libera dopo 16 anni', '2018-03-21', 'https://www.repubblica.it/cronaca/franzoni-libera/', 11),
(20, 'Delitto di Avetrana, la storia di Sarah Scazzi', '2017-02-21', 'https://www.corriere.it/cronache/avetrana-sarah-scazzi/', 12),
(21, 'Sabrina e Cosima condannate: ergastolo definitivo', '2017-02-21', 'https://www.repubblica.it/cronaca/avetrana-ergastolo/', 12),
(22, 'Omicidio Yara, Bossetti condannato all\'ergastolo', '2016-07-01', 'https://www.corriere.it/cronache/yara-bossetti-ergastolo/', 13),
(23, 'Il DNA di Ignoto 1 e la caccia a Bossetti', '2018-10-12', 'https://www.ilpost.it/2018/10/12/yara-gambirasio-bossetti/', 13),
(24, 'Omicidio Meredith Kercher, storia del delitto di Perugia', '2015-03-27', 'https://www.corriere.it/cronache/meredith-kercher-perugia/', 14),
(25, 'Amanda Knox e Raffaele Sollecito assolti in via definitiva', '2015-03-27', 'https://www.repubblica.it/cronaca/amanda-knox-assolta/', 14),
(26, 'Strage di Erba, Olindo e Rosa condannati all\'ergastolo', '2011-04-26', 'https://www.corriere.it/cronache/erba-ergastolo/', 15),
(27, 'La richiesta di revisione per la strage di Erba', '2024-04-10', 'https://www.ilpost.it/2024/04/10/strage-erba-revisione/', 15),
(28, 'È morto O.J. Simpson, assolto nel processo del secolo', '2024-04-11', 'https://www.corriere.it/esteri/oj-simpson-morto/', 16),
(29, 'O.J. Simpson, storia del processo che divise l\'America', '2024-04-11', 'https://www.ilpost.it/2024/04/11/oj-simpson/', 16),
(30, 'L\'omicidio di Gianni Versace, 25 anni dopo', '2022-07-15', 'https://www.corriere.it/moda/versace-omicidio-anniversario/', 17),
(31, 'Andrew Cunanan, il killer di Gianni Versace', '2018-01-17', 'https://www.ilpost.it/2018/01/17/andrew-cunanan-versace/', 17),
(32, 'L\'omicidio di John Lennon, 8 dicembre 1980', '2020-12-08', 'https://www.corriere.it/cultura/lennon-omicidio/', 18),
(33, 'Mark David Chapman, l\'assassino di John Lennon', '2022-08-31', 'https://www.ilpost.it/2022/08/31/mark-chapman-lennon/', 18),
(34, 'Sharon Tate e la Manson Family, 50 anni dopo', '2019-08-09', 'https://www.corriere.it/cultura/sharon-tate-manson/', 19),
(35, 'Chi era Charles Manson e la sua famiglia', '2017-11-20', 'https://www.ilpost.it/2017/11/20/charles-manson/', 19),
(36, 'Oscar Pistorius scarcerato dopo 10 anni', '2024-01-05', 'https://www.corriere.it/esteri/pistorius-scarcerato/', 20),
(37, 'Il caso Pistorius, dalla gloria olimpica al carcere', '2024-01-05', 'https://www.repubblica.it/esteri/pistorius-storia/', 20),
(38, 'Omicidio Varani, Manuel Foffo condannato a 30 anni', '2017-07-05', 'https://www.corriere.it/roma/varani-foffo-condanna/', 21),
(39, 'Marco Prato suicida in carcere', '2017-06-20', 'https://www.repubblica.it/cronaca/prato-suicidio/', 21),
(40, 'Femminicidio Tramontano, Impagnatiello condannato all\'ergastolo', '2024-11-25', 'https://www.corriere.it/milano/tramontano-ergastolo/', 22),
(41, 'Alessandro Impagnatiello, la doppia vita del killer', '2023-06-01', 'https://www.repubblica.it/cronaca/impagnatiello-doppia-vita/', 22),
(42, 'Gabby Petito, ritrovato il corpo della blogger', '2021-09-21', 'https://www.corriere.it/esteri/gabby-petito-corpo/', 23),
(43, 'Brian Laundrie trovato morto, confessa in un taccuino', '2022-01-21', 'https://www.ilpost.it/2022/01/21/brian-laundrie-confessione/', 23),
(44, 'Chris Watts, l\'uomo che uccise moglie e figlie per l\'amante', '2020-09-30', 'https://www.fanpage.it/esteri/chris-watts-storia/', 24),
(45, 'Il caso Watts, documentario Netflix sulla strage familiare', '2020-09-15', 'https://www.ilpost.it/2020/09/15/chris-watts-netflix/', 24),
(46, 'Jodi Arias, il processo che divise l\'America', '2015-03-05', 'https://www.fanpage.it/esteri/jodi-arias-processo/', 25),
(47, 'Jodi Arias condannata all\'ergastolo', '2015-04-13', 'https://www.corriere.it/esteri/jodi-arias-ergastolo/', 25);

-- --------------------------------------------------------

--
-- Struttura della tabella `caso`
--

CREATE TABLE `caso` (
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
-- Dump dei dati per la tabella `caso`
--

INSERT INTO `caso` (`N_Caso`, `Titolo`, `Slug`, `Data`, `Luogo`, `Descrizione`, `Storia`, `Tipologia`, `Immagine`, `Approvato`, `Visualizzazioni`, `Data_Inserimento`, `Autore`) VALUES
(1, 'Il Mostro di Milwaukee', 'il-mostro-di-milwaukee', '1991-07-22', 'Milwaukee, Wisconsin, USA', 'Jeffrey Dahmer, il \"Mostro di Milwaukee\", ha ucciso e smembrato 17 giovani uomini tra il 1978 e il 1991, compiendo atti di cannibalismo e necrofilia.', 'Jeffrey Lionel Dahmer, noto come il \"Mostro di Milwaukee\" o il \"Cannibale di Milwaukee\", rappresenta uno dei casi più disturbanti nella storia della criminologia americana. Nato il 21 maggio 1960 a Milwaukee, Wisconsin, Dahmer crebbe in una famiglia apparentemente normale ma segnata da tensioni tra i genitori e da un crescente isolamento sociale del giovane Jeffrey.\n\nI primi segnali di un disturbo profondo emersero durante l\'adolescenza, quando Dahmer sviluppò una morbosa fascinazione per gli animali morti. Il suo primo omicidio avvenne nel 1978. Dopo la cattura avvenuta il 22 luglio 1991, furono scoperte fotografie polaroid dei corpi smembrati e resti umani nel suo appartamento.', 'Serial killer', '', 1, 2, '2026-01-16 09:03:21', ''),
(2, 'Il Clown Killer', 'il-clown-killer', '1978-12-11', 'Chicago, Illinois, USA', 'John Wayne Gacy, noto come \"Il Clown Killer\", ha violentato e ucciso almeno 33 adolescenti e giovani uomini, seppellendone la maggior parte sotto la sua casa.', 'John Wayne Gacy Jr., passato alla storia come \"Il Clown Killer\", rappresenta uno dei serial killer più prolifici della storia americana. Dietro la facciata di rispettabilità e il lavoro come clown per feste di bambini, si nascondeva un predatore sessuale. Tra il 1972 e il 1978, Gacy violentò e uccise almeno 33 giovani uomini, seppellendoli nel vespaio sotto la sua abitazione.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(3, 'Ted Bundy - Il Killer delle Studentesse', 'ted-bundy', '1989-01-24', 'Florida, USA', 'Ted Bundy, assassino seriale carismatico e intelligente, ha confessato 30 omicidi di giovani donne in sette stati americani tra il 1974 e il 1978.', 'Theodore Robert Bundy, conosciuto come Ted Bundy, è considerato uno dei serial killer più famosi e studiati. Uomo di bell\'aspetto, intelligente e carismatico, usava il suo fascino per attirare le vittime. La sua scia di morte attraversò almeno sette stati americani. Fu giustiziato sulla sedia elettrica il 24 gennaio 1989.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(4, 'Il Macellaio di Rostov', 'il-macellaio-di-rostov', '1990-11-20', 'Rostov sul Don, Russia', 'Andrej Romanovic Chikatilo, il \"Macellaio di Rostov\", ha ucciso e mutilato almeno 52 donne e bambini nell\'Unione Sovietica tra il 1978 e il 1990.', 'Andrej Romanovic Chikatilo è stato il serial killer più prolifico della storia sovietica. Adescava le sue vittime nelle stazioni ferroviarie e degli autobus. Fu condannato a morte e giustiziato con un colpo di pistola il 14 febbraio 1994.', 'Serial killer', '', 1, 1, '2026-01-16 09:03:21', ''),
(5, 'Night Stalker - Il Predatore della Notte', 'night-stalker', '1985-08-31', 'Los Angeles, California, USA', 'Richard Ramirez, il \"Night Stalker\", terrorizzò Los Angeles e San Francisco nel 1984-1985 con una serie di omicidi, violenze sessuali e rapine.', 'Richard Ramirez seminò il terrore nell\'area di Los Angeles entrando nelle case di notte attraverso finestre aperte. A differenza di molti serial killer, non aveva un tipo specifico di vittima. Fu catturato il 31 agosto 1985 dopo essere stato riconosciuto dai cittadini.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(6, 'Il Mostro di Firenze', 'il-mostro-di-firenze', '1985-09-08', 'Firenze e provincia, Italia', 'Il Mostro di Firenze è il nome dato all\'autore di una serie di 16 omicidi di coppie appartate avvenuti nelle campagne toscane tra il 1968 e il 1985.', 'Il caso del Mostro di Firenze rappresenta uno dei misteri criminali più inquietanti della storia italiana. Il killer colpiva coppie appartate in auto, uccidendo l\'uomo e mutilando la donna. Nonostante le condanne dei \"Compagni di merende\" (Pacciani, Vanni, Lotti), molti aspetti della vicenda rimangono oscuri.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(7, 'Donato Bilancia - Il Serial Killer dei Treni', 'donato-bilancia', '1998-04-21', 'Liguria, Italia', 'Donato Bilancia uccise 17 persone in Liguria tra il 1997 e il 1998, diventando uno dei serial killer più prolifici della storia italiana.', 'Donato Bilancia è stato responsabile di 17 omicidi in poco più di sei mesi. La fase più terrificante iniziò quando cominciò a uccidere casualmente sui treni in Liguria. Fu condannato a 13 ergastoli e morì in carcere per COVID-19 nel 2020.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(8, 'Jack lo Squartatore', 'jack-lo-squartatore', '1888-11-09', 'Whitechapel, Londra, Regno Unito', 'Jack lo Squartatore è il serial killer mai identificato che uccise almeno cinque prostitute nel quartiere di Whitechapel a Londra nel 1888.', 'Jack lo Squartatore è il serial killer più famoso della storia, mai identificato. Nell\'autunno del 1888 uccise e mutilò brutalmente almeno cinque donne nel quartiere di Whitechapel. Il caso rimane il più grande mistero irrisolto della criminologia.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(9, 'Zodiac Killer', 'zodiac-killer', '1969-10-11', 'California, USA', 'Lo Zodiac Killer è un serial killer mai identificato che uccise almeno 5 persone nella California del Nord tra il 1968 e il 1969.', 'Lo Zodiac Killer operò nella California del Nord inviando lettere criptate ai giornali per sfidare la polizia. Nonostante decenni di indagini e la decifrazione dei suoi codici, la sua identità rimane sconosciuta.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(10, 'Ed Gein - Il Macellaio di Plainfield', 'ed-gein', '1957-11-16', 'Plainfield, Wisconsin, USA', 'Ed Gein uccise due donne e dissotterrò decine di cadaveri per creare macabri oggetti con pelle e ossa umane.', 'Ed Gein è uno dei criminali più disturbanti della storia. Le sue attività di profanazione di tombe e la creazione di oggetti con pelle umana hanno ispirato film come Psycho, Non aprite quella porta e Il silenzio degli innocenti.', 'Serial killer', '', 1, 0, '2026-01-16 09:03:21', ''),
(11, 'Il Delitto di Cogne', 'delitto-di-cogne', '2002-01-30', 'Cogne, Valle d\'Aosta, Italia', 'Il caso dell\'omicidio del piccolo Samuele Lorenzi, 3 anni, per il quale fu condannata la madre Annamaria Franzoni.', 'Il 30 gennaio 2002, il piccolo Samuele Lorenzi fu trovato morto nel suo letto. Il caso divise l\'opinione pubblica italiana. La madre, Annamaria Franzoni, fu condannata a 16 anni di carcere.', 'Casi mediatici italiani', '', 1, 0, '2026-01-16 09:03:21', ''),
(12, 'Il Delitto di Avetrana', 'delitto-di-avetrana', '2010-08-26', 'Avetrana, Puglia, Italia', 'L\'omicidio di Sarah Scazzi, 15 anni, uccisa dalla zia Cosima Serrano e dalla cugina Sabrina Misseri.', 'Sarah Scazzi scomparve il 26 agosto 2010. Il ritrovamento del corpo in un pozzo e la confessione dello zio Michele Misseri in diretta TV sconvolsero l\'Italia. Furono condannate all\'ergastolo la cugina Sabrina e la zia Cosima.', 'Casi mediatici italiani', '', 1, 1, '2026-01-16 09:03:21', ''),
(13, 'Omicidio di Yara Gambirasio', 'omicidio-yara-gambirasio', '2010-11-26', 'Brembate di Sopra, Bergamo, Italia', 'Il caso di Yara Gambirasio, 13 anni, risolto grazie a un\'innovativa indagine genetica che identificò Massimo Bossetti.', 'La tredicenne Yara scomparve il 26 novembre 2010. Un\'indagine scientifica senza precedenti portò all\'identificazione di \"Ignoto 1\" nel muratore Massimo Bossetti, condannato all\'ergastolo grazie alla prova del DNA.', 'Casi mediatici italiani', '', 1, 0, '2026-01-16 09:03:21', ''),
(14, 'Delitto di Perugia - Meredith Kercher', 'delitto-perugia-meredith-kercher', '2007-11-01', 'Perugia, Umbria, Italia', 'L\'omicidio della studentessa britannica Meredith Kercher, con un iter giudiziario che coinvolse Amanda Knox e Raffaele Sollecito.', 'Meredith Kercher fu uccisa a Perugia nel 2007. Il caso divenne mediatico a livello mondiale. L\'unico condannato in via definitiva è Rudy Guede, mentre Amanda Knox e Raffaele Sollecito furono assolti dopo un lungo iter giudiziario.', 'Casi mediatici italiani', '', 1, 0, '2026-01-16 09:03:21', ''),
(15, 'La Strage di Erba', 'strage-di-erba', '2006-12-11', 'Erba, Como, Italia', 'La strage in cui Olindo Romano e Rosa Bazzi uccisero quattro persone, caso oggi oggetto di forti discussioni.', 'L\'11 dicembre 2006, Olindo Romano e Rosa Bazzi uccisero quattro vicini di casa, tra cui un bambino di due anni. Condannati all\'ergastolo, continuano a proclamarsi innocenti supportati da nuove teorie difensive.', 'Casi mediatici italiani', '', 1, 0, '2026-01-16 09:03:21', ''),
(16, 'Il Caso O.J. Simpson', 'caso-oj-simpson', '1994-06-12', 'Los Angeles, California, USA', 'Il processo del secolo: l\'ex campione O.J. Simpson accusato dell\'omicidio dell\'ex moglie Nicole Brown e di Ron Goldman.', 'O.J. Simpson fu accusato di aver ucciso l\'ex moglie e un amico nel 1994. Il processo, seguito da milioni di persone, si concluse con un\'assoluzione penale molto controversa, sebbene fu poi ritenuto responsabile in sede civile.', 'Celebrity', '', 1, 0, '2026-01-16 09:03:21', ''),
(17, 'L\'Omicidio di Gianni Versace', 'omicidio-gianni-versace', '1997-07-15', 'Miami Beach, Florida, USA', 'L\'assassinio dello stilista italiano Gianni Versace, ucciso da Andrew Cunanan sulla scalinata della sua villa.', 'Gianni Versace fu ucciso il 15 luglio 1997 davanti alla sua villa di Miami dal serial killer Andrew Cunanan, che si suicidò pochi giorni dopo. Il movente rimane un mistero.', 'Celebrity', '', 1, 0, '2026-01-16 09:03:21', ''),
(18, 'L\'Omicidio di John Lennon', 'omicidio-john-lennon', '1980-12-08', 'New York City, USA', 'L\'assassinio dell\'ex Beatle John Lennon, ucciso a colpi di pistola da Mark David Chapman davanti al Dakota Building.', 'L\'8 dicembre 1980, Mark David Chapman sparò a John Lennon, uccidendo un\'icona della musica mondiale. Chapman attese la polizia leggendo \"Il giovane Holden\".', 'Celebrity', '', 1, 0, '2026-01-16 09:03:21', ''),
(19, 'Sharon Tate e la Manson Family', 'sharon-tate-manson-family', '1969-08-09', 'Los Angeles, California, USA', 'L\'atroce omicidio dell\'attrice Sharon Tate e di altre quattro persone per mano della setta di Charles Manson.', 'Nell\'agosto 1969, i seguaci di Charles Manson massacrarono l\'attrice Sharon Tate (incinta all\'ottavo mese) e i suoi ospiti nella villa di Cielo Drive, in un tentativo delirante di scatenare una guerra razziale.', 'Celebrity', '', 1, 0, '2026-01-16 09:03:21', ''),
(20, 'Il Caso Oscar Pistorius', 'caso-oscar-pistorius', '2013-02-14', 'Pretoria, Sudafrica', 'L\'atleta paralimpico Oscar Pistorius condannato per l\'omicidio della fidanzata Reeva Steenkamp.', 'La notte di San Valentino 2013, Pistorius sparò alla fidanzata Reeva attraverso la porta del bagno, sostenendo di averla scambiata per un intruso. È stato condannato per omicidio.', 'Amore tossico', '', 1, 0, '2026-01-16 09:03:21', ''),
(21, 'L\'Omicidio di Luca Varani', 'omicidio-luca-varani', '2016-03-05', 'Roma, Italia', 'L\'omicidio del giovane Luca Varani, torturato e ucciso da Marco Prato e Manuel Foffo durante un festino a base di droga.', 'Un crimine senza movente apparente se non la crudeltà. Varani fu attirato in una trappola e torturato per ore da Foffo e Prato al termine di un festino di più giorni.', 'Amore tossico', '', 1, 0, '2026-01-16 09:03:21', ''),
(22, 'Il Femminicidio di Giulia Tramontano', 'femminicidio-giulia-tramontano', '2023-05-27', 'Senago, Milano, Italia', 'L\'omicidio di Giulia Tramontano, incinta al settimo mese, uccisa dal compagno Alessandro Impagnatiello.', 'Giulia Tramontano fu uccisa con 37 coltellate dal compagno che conduceva una doppia vita. Impagnatiello tentò poi di bruciare il corpo. È stato condannato all\'ergastolo.', 'Amore tossico', '', 1, 0, '2026-01-16 09:03:21', ''),
(23, 'Il Caso Gabby Petito', 'caso-gabby-petito', '2021-08-27', 'Wyoming, USA', 'L\'omicidio della travel blogger Gabby Petito, strangolata dal fidanzato Brian Laundrie durante un viaggio in van.', 'Il viaggio on the road di due giovani fidanzati si trasformò in tragedia. Il caso esplose sui social media portando al ritrovamento del corpo di Gabby e, successivamente, a quello di Brian, morto suicida.', 'Amore tossico', '', 1, 1, '2026-01-16 09:03:21', ''),
(24, 'Il Caso Chris Watts', 'caso-chris-watts', '2018-08-13', 'Frederick, Colorado, USA', 'Chris Watts uccise la moglie incinta Shanann e le figlie Bella e Celeste per iniziare una nuova vita con l\'amante.', 'Chris Watts sterminò la sua intera famiglia per stare con l\'amante, fingendo poi preoccupazione in TV per la loro scomparsa. Ha confessato ed è stato condannato all\'ergastolo.', 'Amore tossico', '', 1, 0, '2026-01-16 09:03:21', ''),
(25, 'Il Caso Jodi Arias', 'caso-jodi-arias', '2008-06-04', 'Mesa, Arizona, USA', 'Jodi Arias uccise l\'ex fidanzato Travis Alexander con brutale ferocia in un caso simbolo dell\'ossessione amorosa.', 'Jodi Arias uccise l\'ex fidanzato Travis Alexander con 27 coltellate e un colpo di pistola. Il processo ha rivelato una relazione tossica e ossessiva.', 'Amore tossico', '', 1, 0, '2026-01-16 09:03:21', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `colpa`
--

CREATE TABLE `colpa` (
  `Colpevole` int NOT NULL,
  `Caso` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `colpa`
--

INSERT INTO `colpa` (`Colpevole`, `Caso`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(33, 6),
(34, 6),
(35, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(13, 12),
(14, 12),
(15, 13),
(16, 14),
(17, 15),
(18, 15),
(19, 16),
(20, 17),
(21, 18),
(22, 19),
(23, 19),
(24, 19),
(25, 19),
(26, 20),
(27, 21),
(28, 21),
(29, 22),
(30, 23),
(31, 24),
(32, 25);

-- --------------------------------------------------------

--
-- Struttura della tabella `colpevole`
--

CREATE TABLE `colpevole` (
  `ID_Colpevole` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Cognome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `LuogoNascita` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `DataNascita` date NOT NULL,
  `Immagine` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `colpevole`
--

INSERT INTO `colpevole` (`ID_Colpevole`, `Nome`, `Cognome`, `LuogoNascita`, `DataNascita`, `Immagine`) VALUES
(1, 'Jeffrey', 'Dahmer', 'Milwaukee, Wisconsin, USA', '1960-05-21', ''),
(2, 'John Wayne', 'Gacy', 'Chicago, Illinois, USA', '1942-03-17', ''),
(3, 'Ted', 'Bundy', 'Burlington, Vermont, USA', '1946-11-24', ''),
(4, 'Andrej', 'Chikatilo', 'Yabluchne, Ucraina', '1936-10-16', ''),
(5, 'Richard', 'Ramirez', 'El Paso, Texas, USA', '1960-02-29', ''),
(6, 'Ignoto', 'Mostro di Firenze', 'Sconosciuto', '1940-01-01', ''),
(7, 'Donato', 'Bilancia', 'Potenza, Italia', '1951-07-10', ''),
(8, 'Ignoto', 'Jack lo Squartatore', 'Sconosciuto', '1850-01-01', ''),
(9, 'Ignoto', 'Zodiac Killer', 'Sconosciuto', '1940-01-01', ''),
(10, 'Edward', 'Gein', 'La Crosse, Wisconsin, USA', '1906-08-27', ''),
(11, 'Annamaria', 'Franzoni', 'Bologna, Italia', '1971-01-04', ''),
(12, 'Cosima', 'Serrano', 'Avetrana, Italia', '1960-06-25', ''),
(13, 'Sabrina', 'Misseri', 'Avetrana, Italia', '1987-07-04', ''),
(14, 'Michele', 'Misseri', 'Avetrana, Italia', '1953-01-06', ''),
(15, 'Massimo Giuseppe', 'Bossetti', 'Clusone, Bergamo, Italia', '1970-10-30', ''),
(16, 'Rudy', 'Guede', 'Abidjan, Costa d\'Avorio', '1986-12-26', ''),
(17, 'Olindo', 'Romano', 'Erba, Como, Italia', '1962-05-10', ''),
(18, 'Rosa', 'Bazzi', 'Erba, Como, Italia', '1964-02-08', ''),
(19, 'Orenthal James', 'Simpson', 'San Francisco, California, USA', '1947-07-09', ''),
(20, 'Andrew', 'Cunanan', 'San Diego, California, USA', '1969-08-31', ''),
(21, 'Mark David', 'Chapman', 'Fort Worth, Texas, USA', '1955-05-10', ''),
(22, 'Charles', 'Manson', 'Cincinnati, Ohio, USA', '1934-11-12', ''),
(23, 'Charles Tex', 'Watson', 'Farmersville, Texas, USA', '1945-12-02', ''),
(24, 'Susan', 'Atkins', 'San Gabriel, California, USA', '1948-05-07', ''),
(25, 'Patricia', 'Krenwinkel', 'Los Angeles, California, USA', '1947-12-03', ''),
(26, 'Oscar', 'Pistorius', 'Johannesburg, Sudafrica', '1986-11-22', ''),
(27, 'Marco', 'Prato', 'Roma, Italia', '1986-03-10', ''),
(28, 'Manuel', 'Foffo', 'Roma, Italia', '1987-06-18', ''),
(29, 'Alessandro', 'Impagnatiello', 'Milano, Italia', '1991-08-30', ''),
(30, 'Brian', 'Laundrie', 'New York, USA', '1997-11-21', ''),
(31, 'Chris', 'Watts', 'Spring Lake, North Carolina, USA', '1985-05-16', ''),
(32, 'Jodi', 'Arias', 'Salinas, California, USA', '1980-07-09', ''),
(33, 'Pietro', 'Pacciani', 'Vicchio, Firenze, Italia', '1925-01-07', ''),
(34, 'Mario', 'Vanni', 'San Casciano, Firenze, Italia', '1927-08-02', ''),
(35, 'Giancarlo', 'Lotti', 'San Casciano, Firenze, Italia', '1941-03-28', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `commento`
--

CREATE TABLE `commento` (
  `ID_Commento` int NOT NULL,
  `Commento` text COLLATE utf8mb4_general_ci NOT NULL,
  `Email_Utente` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `ID_Caso` int NOT NULL,
  `Data` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Is_Admin` tinyint(1) DEFAULT NULL,
  `Is_Newsletter` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`Email`, `Username`, `Password`, `Is_Admin`, `Is_Newsletter`) VALUES
('admin@test.it', 'Admin', '$2y$10$TctKk6xDjIzLPDGo.Cky6.h3yrev5Qh9qY9mY1JXKGI3DFWs.KPVK', 1, 1),
('lore.grolla04@gmail.com', 'Lorenzo', '$2y$10$RPrVyIOXQguBlR6kRDz2h.KqskfkwyFmKGXGwlbxIil3V06DCMSFm', 0, 0),
('paolo@gmail.com', 'Paolo', '$2y$10$aW49yojpjRL7JsBVv9TlZ.a7119SoKRteJ8XA8.U9rNC/3Xlj8V7i', 0, 0),
('user@test.it', 'User', '$2y$10$9dk6FVWLhA3i.6JtCcUERuz0cW.v7GI3fyuztVmq/5YfB7G/IkEg2', 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `vittima`
--

CREATE TABLE `vittima` (
  `ID_Vittima` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Cognome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `LuogoNascita` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `DataNascita` date NOT NULL,
  `DataDecesso` date DEFAULT NULL,
  `Caso` int NOT NULL,
  `Immagine` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `vittima`
--

INSERT INTO `vittima` (`ID_Vittima`, `Nome`, `Cognome`, `LuogoNascita`, `DataNascita`, `DataDecesso`, `Caso`, `Immagine`) VALUES
(1, 'Steven', 'Hicks', 'Coventry Township, Ohio, USA', '1959-06-22', '1978-06-18', 1, ''),
(2, 'Steven', 'Tuomi', 'Ontonagon, Michigan, USA', '1962-01-01', '1987-09-15', 1, ''),
(3, 'James', 'Doxtator', 'Milwaukee, Wisconsin, USA', '1973-03-01', '1988-01-16', 1, ''),
(4, 'Richard', 'Guerrero', 'Milwaukee, Wisconsin, USA', '1967-01-01', '1988-03-24', 1, ''),
(5, 'Konerak', 'Sinthasomphone', 'Laos', '1976-12-02', '1991-05-27', 1, ''),
(6, 'Tony', 'Hughes', 'Madison, Wisconsin, USA', '1959-08-26', '1991-05-24', 1, ''),
(7, 'Robert', 'Piest', 'Des Plaines, Illinois, USA', '1963-03-11', '1978-12-11', 2, ''),
(8, 'John', 'Butkovich', 'Chicago, Illinois, USA', '1955-01-01', '1975-07-31', 2, ''),
(9, 'Gregory', 'Godzik', 'Chicago, Illinois, USA', '1959-01-01', '1976-12-12', 2, ''),
(10, 'John', 'Szyc', 'Chicago, Illinois, USA', '1958-01-01', '1977-01-20', 2, ''),
(11, 'Randall', 'Reffett', 'Chicago, Illinois, USA', '1963-05-16', '1977-05-14', 2, ''),
(12, 'Michael', 'Bonnin', 'Houston, Texas, USA', '1960-05-03', '1977-06-03', 2, ''),
(13, 'Lynda Ann', 'Healy', 'Seattle, Washington, USA', '1953-01-31', '1974-01-31', 3, ''),
(14, 'Donna Gail', 'Manson', 'Seattle, Washington, USA', '1955-07-29', '1974-03-12', 3, ''),
(15, 'Susan', 'Rancourt', 'LaGrande, Oregon, USA', '1956-01-01', '1974-04-17', 3, ''),
(16, 'Caryn', 'Campbell', 'Detroit, Michigan, USA', '1951-01-01', '1975-01-12', 3, ''),
(17, 'Lisa', 'Levy', 'St. Petersburg, Florida, USA', '1958-08-08', '1978-01-15', 3, ''),
(18, 'Kimberly', 'Leach', 'Lake City, Florida, USA', '1965-10-22', '1978-02-09', 3, ''),
(19, 'Yelena', 'Zakotnova', 'Rostov, Russia', '1969-01-01', '1978-12-22', 4, ''),
(20, 'Larisa', 'Tkachenko', 'Rostov, Russia', '1962-01-01', '1981-09-03', 4, ''),
(21, 'Lyubov', 'Biryuk', 'Rostov, Russia', '1968-01-01', '1982-06-12', 4, ''),
(22, 'Olga', 'Stalmachenok', 'Rostov, Russia', '1972-01-01', '1982-12-11', 4, ''),
(23, 'Laura', 'Sarkisyan', 'Rostov, Russia', '1968-01-01', '1983-06-18', 4, ''),
(24, 'Dmitry', 'Ptashnikov', 'Rostov, Russia', '1973-01-01', '1984-03-27', 4, ''),
(25, 'Jennie', 'Vincow', 'Los Angeles, California, USA', '1905-01-01', '1984-06-28', 5, ''),
(26, 'Dayle', 'Okazaki', 'Los Angeles, California, USA', '1951-01-01', '1985-03-17', 5, ''),
(27, 'Vincent', 'Zazzara', 'Los Angeles, California, USA', '1921-01-01', '1985-03-27', 5, ''),
(28, 'Maxine', 'Zazzara', 'Los Angeles, California, USA', '1936-01-01', '1985-03-27', 5, ''),
(29, 'William', 'Doi', 'Los Angeles, California, USA', '1921-01-01', '1985-05-14', 5, ''),
(30, 'Mabel', 'Bell', 'Los Angeles, California, USA', '1921-01-01', '1985-06-01', 5, ''),
(31, 'Antonio', 'Lo Bianco', 'Firenze, Italia', '1943-01-01', '1968-08-21', 6, ''),
(32, 'Barbara', 'Locci', 'Firenze, Italia', '1942-01-01', '1968-08-21', 6, ''),
(33, 'Stefania', 'Pettini', 'Firenze, Italia', '1956-01-01', '1974-09-14', 6, ''),
(34, 'Pasquale', 'Gentilcore', 'Firenze, Italia', '1955-01-01', '1974-09-14', 6, ''),
(35, 'Nadine', 'Mauriot', 'Montbéliard, Francia', '1950-01-01', '1985-09-08', 6, ''),
(36, 'Jean Michel', 'Kraveichvili', 'Francia', '1960-01-01', '1985-09-08', 6, ''),
(37, 'Giorgio', 'Centenaro', 'Genova, Italia', '1942-01-01', '1997-10-16', 7, ''),
(38, 'Maurizia', 'Catena', 'Genova, Italia', '1945-01-01', '1997-10-16', 7, ''),
(39, 'Bruno', 'Solari', 'Genova, Italia', '1935-01-01', '1997-10-24', 7, ''),
(40, 'Maria Luigia', 'Pitto', 'Genova, Italia', '1938-01-01', '1997-10-24', 7, ''),
(41, 'Elisabetta', 'Zoppetti', 'Genova, Italia', '1966-01-01', '1998-03-12', 7, ''),
(42, 'Maria Angela', 'Rubino', 'Genova, Italia', '1969-01-01', '1998-03-14', 7, ''),
(43, 'Mary Ann', 'Nichols', 'Londra, Regno Unito', '1845-08-26', '1888-08-31', 8, ''),
(44, 'Annie', 'Chapman', 'Londra, Regno Unito', '1841-09-01', '1888-09-08', 8, ''),
(45, 'Elizabeth', 'Stride', 'Svezia', '1843-11-27', '1888-09-30', 8, ''),
(46, 'Catherine', 'Eddowes', 'Wolverhampton, Regno Unito', '1842-04-14', '1888-09-30', 8, ''),
(47, 'Mary Jane', 'Kelly', 'Irlanda', '1863-01-01', '1888-11-09', 8, ''),
(48, 'David', 'Faraday', 'Vallejo, California, USA', '1951-10-21', '1968-12-20', 9, ''),
(49, 'Betty Lou', 'Jensen', 'Vallejo, California, USA', '1952-02-21', '1968-12-20', 9, ''),
(50, 'Darlene', 'Ferrin', 'Mill Valley, California, USA', '1947-03-17', '1969-07-04', 9, ''),
(51, 'Cecelia', 'Shepard', 'Riverside, California, USA', '1947-01-01', '1969-09-29', 9, ''),
(52, 'Paul', 'Stine', 'Camarillo, California, USA', '1940-12-18', '1969-10-11', 9, ''),
(53, 'Mary', 'Hogan', 'Plainfield, Wisconsin, USA', '1900-01-01', '1954-12-08', 10, ''),
(54, 'Bernice', 'Worden', 'Plainfield, Wisconsin, USA', '1899-01-01', '1957-11-16', 10, ''),
(55, 'Samuele', 'Lorenzi', 'Cogne, Italia', '1999-01-21', '2002-01-30', 11, ''),
(56, 'Sarah', 'Scazzi', 'Avetrana, Italia', '1995-05-27', '2010-08-26', 12, ''),
(57, 'Yara', 'Gambirasio', 'Brembate di Sopra, Italia', '1997-07-09', '2010-11-26', 13, ''),
(58, 'Meredith', 'Kercher', 'Coulsdon, Londra, Regno Unito', '1985-12-28', '2007-11-01', 14, ''),
(59, 'Raffaella', 'Castagna', 'Erba, Italia', '1976-01-01', '2006-12-11', 15, ''),
(60, 'Youssef', 'Marzouk', 'Erba, Italia', '2004-01-01', '2006-12-11', 15, ''),
(61, 'Paola', 'Galli', 'Erba, Italia', '1946-01-01', '2006-12-11', 15, ''),
(62, 'Valeria', 'Cherubini', 'Erba, Italia', '1951-01-01', '2006-12-11', 15, ''),
(63, 'Nicole', 'Brown Simpson', 'Francoforte, Germania', '1959-05-19', '1994-06-12', 16, ''),
(64, 'Ron', 'Goldman', 'Chicago, Illinois, USA', '1968-07-02', '1994-06-12', 16, ''),
(65, 'Gianni', 'Versace', 'Reggio Calabria, Italia', '1946-12-02', '1997-07-15', 17, ''),
(66, 'John', 'Lennon', 'Liverpool, Regno Unito', '1940-10-09', '1980-12-08', 18, ''),
(67, 'Sharon', 'Tate', 'Dallas, Texas, USA', '1943-01-24', '1969-08-09', 19, ''),
(68, 'Jay', 'Sebring', 'Birmingham, Alabama, USA', '1933-10-10', '1969-08-09', 19, ''),
(69, 'Abigail', 'Folger', 'San Francisco, California, USA', '1943-08-11', '1969-08-09', 19, ''),
(70, 'Wojciech', 'Frykowski', 'Lodz, Polonia', '1936-12-22', '1969-08-09', 19, ''),
(71, 'Steven', 'Parent', 'Los Angeles, California, USA', '1951-07-26', '1969-08-09', 19, ''),
(72, 'Reeva', 'Steenkamp', 'Città del Capo, Sudafrica', '1983-08-19', '2013-02-14', 20, ''),
(73, 'Luca', 'Varani', 'Roma, Italia', '1992-07-19', '2016-03-05', 21, ''),
(74, 'Giulia', 'Tramontano', 'Sant Antimo, Napoli, Italia', '1994-02-09', '2023-05-27', 22, ''),
(75, 'Gabby', 'Petito', 'Blue Point, New York, USA', '1999-03-19', '2021-08-27', 23, ''),
(76, 'Shanann', 'Watts', 'Passaic, New Jersey, USA', '1984-01-10', '2018-08-13', 24, ''),
(77, 'Bella', 'Watts', 'Frederick, Colorado, USA', '2013-12-17', '2018-08-13', 24, ''),
(78, 'Celeste', 'Watts', 'Frederick, Colorado, USA', '2015-07-17', '2018-08-13', 24, ''),
(79, 'Travis', 'Alexander', 'Riverside, California, USA', '1977-07-28', '2008-06-04', 25, '');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `articolo`
--
ALTER TABLE `articolo`
  ADD PRIMARY KEY (`ID_Articolo`),
  ADD KEY `Caso` (`Caso`);

--
-- Indici per le tabelle `caso`
--
ALTER TABLE `caso`
  ADD PRIMARY KEY (`N_Caso`),
  ADD UNIQUE KEY `Slug` (`Slug`),
  ADD KEY `idx_visualizzazioni` (`Visualizzazioni`),
  ADD KEY `idx_approvato` (`Approvato`),
  ADD KEY `idx_n_caso_desc` (`N_Caso`);

--
-- Indici per le tabelle `colpa`
--
ALTER TABLE `colpa`
  ADD PRIMARY KEY (`Colpevole`,`Caso`),
  ADD KEY `Caso` (`Caso`);

--
-- Indici per le tabelle `colpevole`
--
ALTER TABLE `colpevole`
  ADD PRIMARY KEY (`ID_Colpevole`);

--
-- Indici per le tabelle `commento`
--
ALTER TABLE `commento`
  ADD PRIMARY KEY (`ID_Commento`),
  ADD KEY `Email_Utente` (`Email_Utente`),
  ADD KEY `ID_Caso` (`ID_Caso`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`Email`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indici per le tabelle `vittima`
--
ALTER TABLE `vittima`
  ADD PRIMARY KEY (`ID_Vittima`),
  ADD KEY `Caso` (`Caso`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `articolo`
--
ALTER TABLE `articolo`
  MODIFY `ID_Articolo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT per la tabella `caso`
--
ALTER TABLE `caso`
  MODIFY `N_Caso` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT per la tabella `colpevole`
--
ALTER TABLE `colpevole`
  MODIFY `ID_Colpevole` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT per la tabella `commento`
--
ALTER TABLE `commento`
  MODIFY `ID_Commento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `vittima`
--
ALTER TABLE `vittima`
  MODIFY `ID_Vittima` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `articolo`
--
ALTER TABLE `articolo`
  ADD CONSTRAINT `Articolo_ibfk_1` FOREIGN KEY (`Caso`) REFERENCES `caso` (`N_Caso`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `colpa`
--
ALTER TABLE `colpa`
  ADD CONSTRAINT `colpa_ibfk_1` FOREIGN KEY (`Colpevole`) REFERENCES `colpevole` (`ID_Colpevole`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Colpa_ibfk_2` FOREIGN KEY (`Caso`) REFERENCES `caso` (`N_Caso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `commento`
--
ALTER TABLE `commento`
  ADD CONSTRAINT `Commento_ibfk_1` FOREIGN KEY (`Email_Utente`) REFERENCES `utente` (`Email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Commento_ibfk_2` FOREIGN KEY (`ID_Caso`) REFERENCES `caso` (`N_Caso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `vittima`
--
ALTER TABLE `vittima`
  ADD CONSTRAINT `Vittima_ibfk_1` FOREIGN KEY (`Caso`) REFERENCES `caso` (`N_Caso`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
