-- Script per popolare il database sistema_gestionale con dati realistici
USE sistema_gestionale;

-- ============================================
-- POPOLAMENTO TABELLA UTENTE
-- ============================================
INSERT INTO Utente (Email, Username, Password, Is_Admin) VALUES
('admin@alicetruecrime.it', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('detective1@email.it', 'SherlockHolmes', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('detective2@email.it', 'HerculePoirot', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('investigatore@email.it', 'MissMarple', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('crimine@email.it', 'ColomboCIA', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('alice@truecrime.it', 'AliceDetective', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0);

-- ============================================
-- POPOLAMENTO TABELLA GIOCO
-- ============================================
INSERT INTO Gioco (Nome, Descrizione, Immagine) VALUES
('Quiz Criminologia Base', 'Un quiz introduttivo sui principi fondamentali della criminologia e investigazione', 'img/quiz_base.jpg'),
('Indovina il Colpevole', 'Analizza le prove e identifica il colpevole tra i sospetti', 'img/indovina_colpevole.jpg'),
('Timeline Criminale', 'Ricostruisci la sequenza temporale degli eventi di un crimine', 'img/timeline.jpg'),
('Expert Detective', 'Quiz avanzato per veri esperti di criminologia forense', 'img/expert.jpg');

-- ============================================
-- POPOLAMENTO TABELLA CASO
-- ============================================
INSERT INTO Caso (Data, Luogo, Descrizione, Tipologia, Immagine) VALUES
('2023-03-15', 'Milano, Via Montenapoleone', 'Rapina a mano armata in una gioielleria di lusso. I malviventi hanno rubato gioielli per un valore di 2 milioni di euro prima di fuggire con un motociclo.', 'Rapina', 'img/caso_rapina_milano.jpg'),
('2023-06-22', 'Roma, Quartiere EUR', 'Frode informatica ai danni di una multinazionale. Sottratti 500.000 euro attraverso un sofisticato attacco di phishing mirato.', 'Frode', 'img/caso_frode_roma.jpg'),
('2023-09-10', 'Napoli, Centro Storico', 'Omicidio avvenuto in un appartamento del centro storico. La vittima presentava segni di colluttazione.', 'Omicidio', 'img/caso_omicidio_napoli.jpg'),
('2023-11-05', 'Torino, Zona Industriale', 'Traffico di sostanze stupefacenti scoperto durante un controllo di routine. Sequestrati 50kg di cocaina.', 'Traffico di Droga', 'img/caso_droga_torino.jpg'),
('2024-01-18', 'Firenze, Ponte Vecchio', 'Furto di opere d\'arte da una galleria privata. Rubati tre quadri del valore complessivo di 1,5 milioni di euro.', 'Furto', 'img/caso_furto_firenze.jpg'),
('2024-03-30', 'Bologna, Stazione Centrale', 'Aggressione a scopo di rapina ai danni di un turista straniero. La vittima è stata soccorsa e trasportata in ospedale.', 'Aggressione', 'img/caso_aggressione_bologna.jpg');

-- ============================================
-- POPOLAMENTO TABELLA COLPEVOLE
-- ============================================
INSERT INTO Colpevole (CF_Colpevole, Nome, Cognome, LuogoNascita, DataNascita, Immagine) VALUES
('RSSMRA85M15F205Z', 'Marco', 'Rossi', 'Milano', '1985-08-15', 'img/colpevole_rossi.jpg'),
('BNCLGU78H20H501Y', 'Luigi', 'Bianchi', 'Roma', '1978-06-20', 'img/colpevole_bianchi.jpg'),
('VRDGPP90A10F839W', 'Giuseppe', 'Verdi', 'Napoli', '1990-01-10', 'img/colpevole_verdi.jpg'),
('NRIFNC82D25L219X', 'Francesca', 'Neri', 'Torino', '1982-04-25', 'img/colpevole_neri.jpg'),
('GLLSRA88M50D612V', 'Sara', 'Galli', 'Firenze', '1988-08-10', 'img/colpevole_galli.jpg'),
('CSTDVD92T15A944U', 'Davide', 'Costa', 'Bologna', '1992-12-15', 'img/colpevole_costa.jpg'),
('MRNGNN86L30F205T', 'Giovanni', 'Marino', 'Milano', '1986-07-30', 'img/colpevole_marino.jpg');

-- ============================================
-- POPOLAMENTO TABELLA VITTIMA
-- ============================================
INSERT INTO Vittima (CF_Vittima, Nome, Cognome, LuogoNascita, DataNascita, DataDecesso, Caso) VALUES
('RSSPLA65M12F205R', 'Paola', 'Russo', 'Milano', '1965-08-12', NULL, 1),
('FNTSMT70H15H501S', 'Simone', 'Fontana', 'Roma', '1970-06-15', NULL, 2),
('MRNLRA83C20F839P', 'Laura', 'Morini', 'Napoli', '1983-03-20', '2023-09-10', 3),
('CRBMTT75D10D612Q', 'Matteo', 'Carboni', 'Firenze', '1975-04-10', NULL, 5),
('LMBGVN88T22L219W', 'Giovanni', 'Lombardi', 'Bologna', '1988-12-22', NULL, 6);

-- ============================================
-- POPOLAMENTO TABELLA COLPA
-- ============================================
INSERT INTO Colpa (Colpevole, Caso) VALUES
('RSSMRA85M15F205Z', 1),
('MRNGNN86L30F205T', 1),
('BNCLGU78H20H501Y', 2),
('VRDGPP90A10F839W', 3),
('NRIFNC82D25L219X', 4),
('GLLSRA88M50D612V', 5),
('CSTDVD92T15A944U', 6);

-- ============================================
-- POPOLAMENTO TABELLA ARTICOLO
-- ============================================
INSERT INTO Articolo (Titolo, Data, Link, Caso) VALUES
('Colpo in gioielleria: rubati 2 milioni di euro', '2023-03-16', 'https://corriere.it/milano/rapina-montenapoleone', 1),
('Rapina lampo a Milano: indagini in corso', '2023-03-17', 'https://repubblica.it/cronaca/rapina-milano-2023', 1),
('Maxi frode informatica: 500mila euro spariti', '2023-06-23', 'https://ilsole24ore.com/frode-informatica-roma', 2),
('Phishing colpisce multinazionale: esperto IT arrestato', '2023-06-25', 'https://corriere.it/tecnologia/frode-phishing', 2),
('Omicidio nel centro storico: fermato un sospettato', '2023-09-11', 'https://repubblica.it/napoli/omicidio-centro', 3),
('Droga sequestrata a Torino: maxi operazione antidroga', '2023-11-06', 'https://lastampa.it/torino/sequestro-droga', 4),
('Traffico internazionale di cocaina: 5 arresti', '2023-11-07', 'https://corriere.it/cronaca/droga-torino-arresti', 4),
('Furto d\'arte a Firenze: spariti tre capolavori', '2024-01-19', 'https://lanazione.it/firenze/furto-arte-ponte-vecchio', 5),
('Aggressione in stazione: turista ferito', '2024-03-31', 'https://ilrestodelcarlino.it/bologna/aggressione-stazione', 6);

-- ============================================
-- POPOLAMENTO TABELLA DOMANDA
-- ============================================
INSERT INTO Domanda (Tipologia, Testo, Gioco) VALUES
('Multipla', 'Qual è il primo principio fondamentale della scena del crimine?', 'Quiz Criminologia Base'),
('Multipla', 'Cosa significa "modus operandi" in criminologia?', 'Quiz Criminologia Base'),
('Vero/Falso', 'Le impronte digitali sono uniche per ogni individuo', 'Quiz Criminologia Base'),
('Multipla', 'Quale tecnica viene utilizzata per rilevare tracce di sangue invisibili?', 'Quiz Criminologia Base'),
('Deduttiva', 'Basandoti sulle prove raccolte nella rapina di Milano, chi è il principale sospettato?', 'Indovina il Colpevole'),
('Deduttiva', 'Nel caso della frode informatica, quale competenza deve avere il colpevole?', 'Indovina il Colpevole'),
('Multipla', 'Quale indizio è più rilevante in un caso di omicidio?', 'Indovina il Colpevole'),
('Sequenza', 'Ordina cronologicamente le fasi di una rapina a mano armata', 'Timeline Criminale'),
('Sequenza', 'Qual è la corretta sequenza investigativa?', 'Timeline Criminale'),
('Complessa', 'In un caso di avvelenamento, quale esame tossicologico è prioritario?', 'Expert Detective'),
('Complessa', 'Quale tecnica forense è più efficace per datare un documento?', 'Expert Detective'),
('Multipla', 'In quale situazione si applica la catena di custodia delle prove?', 'Expert Detective');

-- ============================================
-- POPOLAMENTO TABELLA RISPOSTA
-- ============================================
INSERT INTO Risposta (Opzione, IsTrue, Domanda) VALUES
('Preservare la scena del crimine', TRUE, 1),
('Arrestare immediatamente i sospetti', FALSE, 1),
('Interrogare i testimoni', FALSE, 1),
('Chiamare i giornalisti', FALSE, 1),
('Il metodo caratteristico con cui un criminale commette i reati', TRUE, 2),
('Il movente del crimine', FALSE, 2),
('La prova principale', FALSE, 2),
('L\'alibi del sospettato', FALSE, 2),
('Vero', TRUE, 3),
('Falso', FALSE, 3),
('Luminol', TRUE, 4),
('Fenolftaleina', FALSE, 4),
('Acido nitrico', FALSE, 4),
('Blu di metilene', FALSE, 4),
('Marco Rossi, pregiudicato per rapine', TRUE, 5),
('Luigi Bianchi, esperto informatico', FALSE, 5),
('Giuseppe Verdi, senza precedenti', FALSE, 5),
('Competenze informatiche avanzate', TRUE, 6),
('Forza fisica', FALSE, 6),
('Conoscenze mediche', FALSE, 6),
('DNA sulla scena del crimine', TRUE, 7),
('Colore dei capelli del sospettato', FALSE, 7),
('Orario di chiusura dei negozi', FALSE, 7),
('Sopralluogo, esecuzione, fuga, spartizione', TRUE, 8),
('Fuga, esecuzione, sopralluogo', FALSE, 8),
('Esecuzione, sopralluogo, fuga', FALSE, 8),
('Sopralluogo, raccolta prove, analisi, interrogatori, arresto', TRUE, 9),
('Arresto, interrogatori, raccolta prove', FALSE, 9),
('Interrogatori, sopralluogo, arresto', FALSE, 9),
('Spettroscopia di massa', TRUE, 10),
('Radiografia', FALSE, 10),
('Ecografia', FALSE, 10),
('Analisi dell\'inchiostro e della carta', TRUE, 11),
('Peso del documento', FALSE, 11),
('Dimensioni del foglio', FALSE, 11),
('In ogni caso che coinvolge prove fisiche', TRUE, 12),
('Solo per armi da fuoco', FALSE, 12),
('Solo per stupefacenti', FALSE, 12);

-- ============================================
-- POPOLAMENTO TABELLA COMMENTO
-- ============================================
INSERT INTO Commento (Data, Commento, Email_Utente, ID_Caso) VALUES
('2023-03-18', 'Caso molto interessante! Mi chiedo come abbiano fatto a fuggire così velocemente.', 'detective1@email.it', 1),
('2023-03-19', 'La sicurezza delle gioiellerie dovrebbe essere migliorata. Questi crimini sono troppo frequenti.', 'detective2@email.it', 1),
('2023-06-24', 'Un altro caso di phishing! Le aziende devono investire di più nella formazione del personale.', 'investigatore@email.it', 2),
('2023-09-12', 'Spero che venga fatta giustizia per la vittima. Un caso davvero tragico.', 'crimine@email.it', 3),
('2023-09-13', 'Complimenti agli investigatori per la rapidità nell\'individuare il sospettato.', 'detective1@email.it', 3),
('2023-11-08', 'Il traffico di droga è un problema serio. Ottimo lavoro delle forze dell\'ordine.', 'alice@truecrime.it', 4),
('2024-01-20', 'Opere d\'arte inestimabili! Spero vengano recuperate presto.', 'detective2@email.it', 5),
('2024-01-21', 'La sicurezza dei musei e gallerie deve essere una priorità assoluta.', 'investigatore@email.it', 5),
('2024-04-01', 'Bisogna aumentare i controlli nelle stazioni, soprattutto di sera.', 'crimine@email.it', 6);

-- ============================================
-- POPOLAMENTO TABELLA PARTITA
-- ============================================
INSERT INTO Partita (Email_Utente, Gioco, Punteggio) VALUES
('detective1@email.it', 'Quiz Criminologia Base', 85),
('detective1@email.it', 'Indovina il Colpevole', 70),
('detective2@email.it', 'Quiz Criminologia Base', 92),
('detective2@email.it', 'Timeline Criminale', 88),
('investigatore@email.it', 'Indovina il Colpevole', 65),
('investigatore@email.it', 'Expert Detective', 78),
('crimine@email.it', 'Quiz Criminologia Base', 95),
('crimine@email.it', 'Timeline Criminale', 82),
('crimine@email.it', 'Expert Detective', 90);

-- ============================================
-- VERIFICA DATI INSERITI
-- ============================================
SELECT 'Utenti inseriti:' AS Info, COUNT(*) AS Totale FROM Utente
UNION ALL
SELECT 'Giochi inseriti:', COUNT(*) FROM Gioco
UNION ALL
SELECT 'Casi inseriti:', COUNT(*) FROM Caso
UNION ALL
SELECT 'Colpevoli inseriti:', COUNT(*) FROM Colpevole
UNION ALL
SELECT 'Vittime inserite:', COUNT(*) FROM Vittima
UNION ALL
SELECT 'Articoli inseriti:', COUNT(*) FROM Articolo
UNION ALL
SELECT 'Domande inserite:', COUNT(*) FROM Domanda
UNION ALL
SELECT 'Risposte inserite:', COUNT(*) FROM Risposta
UNION ALL
SELECT 'Commenti inseriti:', COUNT(*) FROM Commento
UNION ALL
SELECT 'Partite inserite:', COUNT(*) FROM Partita
UNION ALL
SELECT 'Relazioni Colpa:', COUNT(*) FROM Colpa;