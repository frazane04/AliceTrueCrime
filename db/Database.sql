-- Creazione del database
CREATE DATABASE IF NOT EXISTS sistema_gestionale;
USE sistema_gestionale;

-- Tabella Utente
CREATE TABLE Utente (
    ID_Utente INT PRIMARY KEY AUTO_INCREMENT,
    Email VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Is_Admin BOOLEAN NOT NULL
);

-- Tabella Gioco
CREATE TABLE Gioco (
    Nome VARCHAR(100) PRIMARY KEY,
    Descrizione TEXT,
    Immagine VARCHAR(255)
);

-- Tabella Caso 
CREATE TABLE Caso (
    N_Caso INT PRIMARY KEY AUTO_INCREMENT,
    Data DATE NOT NULL,
    Luogo VARCHAR(100) NOT NULL,
    Descrizione TEXT NOT NULL,
    Tipologia VARCHAR(50) NOT NULL,
    Immagine VARCHAR(255) NOT NULL
);

-- Tabella Vittima 
CREATE TABLE Vittima (
    CF_Vittima VARCHAR(16) PRIMARY KEY,
    Nome VARCHAR(50) NOT NULL,
    Cognome VARCHAR(50) NOT NULL,
    LuogoNascita VARCHAR(100) NOT NULL,
    DataNascita DATE NOT NULL,
    DataDecesso DATE,
    Caso INT NOT NULL,  
    FOREIGN KEY (Caso) REFERENCES Caso(N_Caso) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabella Colpevole
CREATE TABLE Colpevole (
    CF_Colpevole VARCHAR(16) PRIMARY KEY,
    Nome VARCHAR(50) NOT NULL,
    Cognome VARCHAR(50) NOT NULL,
    LuogoNascita VARCHAR(100) NOT NULL,
    DataNascita DATE NOT NULL,
    Immagine VARCHAR(255) NOT NULL
);

-- Tabella Articolo
CREATE TABLE Articolo (
    ID_Articolo INT PRIMARY KEY AUTO_INCREMENT,
    Titolo VARCHAR(200) NOT NULL,
    Data DATE NOT NULL,
    Link VARCHAR(255) NOT NULL,
    Caso INT,
    FOREIGN KEY (Caso) REFERENCES Caso(N_Caso) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabella Domanda
CREATE TABLE Domanda (
    ID_Domanda INT PRIMARY KEY AUTO_INCREMENT,
    Tipologia VARCHAR(50) NOT NULL,
    Testo TEXT NOT NULL,
    Gioco VARCHAR(100) NOT NULL, 
    FOREIGN KEY (Gioco) REFERENCES Gioco(Nome) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabella Risposta
CREATE TABLE Risposta (
    ID_Risposta INT PRIMARY KEY AUTO_INCREMENT,
    Opzione TEXT NOT NULL,
    IsTrue BOOLEAN NOT NULL,
    Domanda INT NOT NULL,
    FOREIGN KEY (Domanda) REFERENCES Domanda(ID_Domanda) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabella Commento
CREATE TABLE Commento (
    ID_Commento INT PRIMARY KEY AUTO_INCREMENT,
    Data DATE NOT NULL,
    Commento TEXT NOT NULL,
    ID_Utente INT NOT NULL,
    ID_Caso INT NOT NULL,
    FOREIGN KEY (ID_Utente) REFERENCES Utente(ID_Utente) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ID_Caso) REFERENCES Caso(N_Caso) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabella Partita (relazione tra Gioco e Utente)
CREATE TABLE Partita (
    Utente INT NOT NULL,
    Gioco VARCHAR(100) NOT NULL,
    Punteggio INT DEFAULT 0,
    PRIMARY KEY (Utente, Gioco),
    FOREIGN KEY (Utente) REFERENCES Utente(ID_Utente) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Gioco) REFERENCES Gioco(Nome) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabella Colpa (relazione tra Colpevole e Caso)
CREATE TABLE Colpa (
    Colpevole VARCHAR(16) NOT NULL,
    Caso INT NOT NULL,
    PRIMARY KEY (Colpevole, Caso),
    FOREIGN KEY (Colpevole) REFERENCES Colpevole(CF_Colpevole) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Caso) REFERENCES Caso(N_Caso) ON DELETE CASCADE ON UPDATE CASCADE
);