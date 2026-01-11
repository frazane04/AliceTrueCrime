<?php
/**
 * Funzioni per interagire con il database
 * Utilizzano prepared statements per prevenire SQL Injection
 * AGGIORNATO: Email come chiave primaria
 */

require_once __DIR__ . '/connessione.php';

class FunzioniDB {
    private $db;
    
    public function __construct() {
        $this->db = new ConnessioneDB();
    }

    // ========================================
    // GESTIONE UTENTI
    // ========================================
    
    /**
     * Registra un nuovo utente nel database
     * @param string $email Email dell'utente (chiave primaria)
     * @param string $username Nome utente (univoco)
     * @param string $password Password in chiaro (verrà hashata)
     * @param bool $isAdmin Default false
     * @return array ['success' => bool, 'message' => string, 'email' => string|null]
     */
    public function registraUtente($email, $username, $password, $isAdmin = false) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Validazione email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Email non valida',
                    'email' => null
                ];
            }
            
            // Verifica se email esiste già
            if ($this->verificaEmailEsistente($email)) {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Email già registrata',
                    'email' => null
                ];
            }
            
            // Verifica se username esiste già
            if ($this->verificaUsernameEsistente($username)) {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Username già in uso',
                    'email' => null
                ];
            }
            
            // Hash della password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $isAdminInt = $isAdmin ? 1 : 0;
            
            // Insert utente
            $query = "INSERT INTO Utente (Email, Username, Password, Is_Admin) VALUES (?, ?, ?, ?)";
            $result = $this->db->query($query, [$email, $username, $passwordHash, $isAdminInt], "sssi");
            
            if ($result) {
                $this->db->chiudiConnessione();
                
                return [
                    'success' => true,
                    'message' => 'Registrazione completata con successo',
                    'email' => $email
                ];
            } else {
                throw new Exception("Errore durante l'inserimento nel database");
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore registrazione utente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore durante la registrazione. Riprova più tardi.',
                'email' => null
            ];
        }
    }
    
    /**
     * Verifica se un'email esiste già
     * @param string $email
     * @return bool
     */
    private function verificaEmailEsistente($email) {
        $query = "SELECT Email FROM Utente WHERE Email = ?";
        $result = $this->db->query($query, [$email], "s");
        
        if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Verifica se uno username esiste già
     * @param string $username
     * @return bool
     */
    private function verificaUsernameEsistente($username) {
        $query = "SELECT Email FROM Utente WHERE Username = ?";
        $result = $this->db->query($query, [$username], "s");
        
        if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Effettua il login verificando email e password
     * @param string $email Email dell'utente
     * @param string $password Password in chiaro
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function loginUtenteEmail($email, $password) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Validazione email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Email non valida',
                    'user' => null
                ];
            }
            
            $query = "SELECT Email, Username, Password, Is_Admin FROM Utente WHERE Email = ?";
            $result = $this->db->query($query, [$email], "s");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                
                // Verifica password
                if (password_verify($password, $user['Password'])) {
                    $this->db->chiudiConnessione();
                    
                    return [
                        'success' => true,
                        'message' => 'Login effettuato con successo',
                        'user' => [
                            'email' => $user['Email'],
                            'username' => $user['Username'],
                            'is_admin' => (bool)$user['Is_Admin']
                        ]
                    ];
                } else {
                    $this->db->chiudiConnessione();
                    return [
                        'success' => false,
                        'message' => 'Password non corretta',
                        'user' => null
                    ];
                }
            } else {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Email non trovata',
                    'user' => null
                ];
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore login utente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore durante il login. Riprova più tardi.',
                'user' => null
            ];
        }
    }
    
    /**
     * Effettua il login verificando username e password
     * @param string $username Username dell'utente
     * @param string $password Password in chiaro
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function loginUtenteUsername($username, $password) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT Email, Username, Password, Is_Admin FROM Utente WHERE Username = ?";
            $result = $this->db->query($query, [$username], "s");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                
                // Verifica password
                if (password_verify($password, $user['Password'])) {
                    $this->db->chiudiConnessione();
                    
                    return [
                        'success' => true,
                        'message' => 'Login effettuato con successo',
                        'user' => [
                            'email' => $user['Email'],
                            'username' => $user['Username'],
                            'is_admin' => (bool)$user['Is_Admin']
                        ]
                    ];
                } else {
                    $this->db->chiudiConnessione();
                    return [
                        'success' => false,
                        'message' => 'Password non corretta',
                        'user' => null
                    ];
                }
            } else {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Username non trovato',
                    'user' => null
                ];
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore login utente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore durante il login. Riprova più tardi.',
                'user' => null
            ];
        }
    }
    
    /**
     * Ottiene i dati di un utente tramite Email
     * @param string $email
     * @return array|null
     */
    public function getUtenteByEmail($email) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT Email, Username, Is_Admin FROM Utente WHERE Email = ?";
            $result = $this->db->query($query, [$email], "s");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return $user;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero utente: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Ottiene i dati di un utente tramite Username
     * @param string $username
     * @return array|null
     */
    public function getUtenteByUsername($username) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT Email, Username, Is_Admin FROM Utente WHERE Username = ?";
            $result = $this->db->query($query, [$username], "s");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return $user;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero utente: " . $e->getMessage());
            return null;
        }
    }



 // ========================================
    // GESTIONE CASI
    // ========================================
    
    
    /**
     * Recupera l'ID di un caso dal suo slug
     * @param string $slug Slug del caso
     * @return int|null ID del caso o null se non trovato
     */
    public function getCasoIdBySlug($slug) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT N_Caso FROM Caso WHERE Slug = ? AND Approvato = 1";
            $result = $this->db->query($query, [$slug], "s");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return (int)$row['N_Caso'];
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero caso da slug: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Recupera casi per categoria (solo approvati)
     * @param string $tipologia Nome della categoria
     * @param int $limite Numero massimo di risultati
     * @return array Lista di casi
     */
    public function getCasiPerCategoria($tipologia, $limite = 10) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT N_Caso, Titolo, Descrizione, Immagine, Tipologia, Data, Luogo 
                      FROM Caso 
                      WHERE Tipologia = ? AND Approvato = 1 
                      ORDER BY Data DESC 
                      LIMIT ?";
            
            $result = $this->db->query($query, [$tipologia, $limite], "si");
            
            $casi = [];
            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $casi[] = $row;
                }
            }
            
            $this->db->chiudiConnessione();
            return $casi;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero casi per categoria: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Recupera i casi più recenti/letti
     * @param int $limite Numero massimo di risultati
     * @return array Lista di casi
     */
    public function getCasiPiuLetti($limite = 5) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Ordiniamo per data (in futuro potresti aggiungere un campo 'visualizzazioni')
            $query = "SELECT N_Caso, Titolo, Descrizione, Immagine, Tipologia, Data, Luogo 
                      FROM Caso 
                      WHERE Approvato = 1 
                      ORDER BY Data DESC 
                      LIMIT ?";
            
            $result = $this->db->query($query, [$limite], "i");
            
            $casi = [];
            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $casi[] = $row;
                }
            }
            
            $this->db->chiudiConnessione();
            return $casi;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero casi più letti: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Recupera un singolo caso tramite ID
     * @param int $nCaso ID del caso
     * @return array|null Dati del caso o null
     */
    public function getCasoById($nCaso) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT * FROM Caso WHERE N_Caso = ? AND Approvato = 1";
            $result = $this->db->query($query, [$nCaso], "i");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $caso = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return $caso;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero caso: " . $e->getMessage());
            return null;
        }
    }


    /**
     * Recupera le vittime di un caso tramite ID
     * @param int $ID del caso
     * @return array|null Dati dei colpevoli o null
     */
    public function getVittimeByCaso($id) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT * FROM Caso JOIN Vittima ON Caso.N_Caso=Vittima.Caso WHERE N_Caso=? AND Approvato = 1";
            $result = $this->db->query($query, [$id], "i");
            $vittime=[];
            if ($result && is_object($result)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $vittime[] = $row;
            }
        }

        $this->db->chiudiConnessione();
        return $vittime;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero vittime di un caso: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Recupera i colpevoli di un caso tramite ID
     * @param int $ID del caso
     * @return array|null Dati dei colpevoli o null
     */
    public function getColpevoliByCaso($id) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            $query = "SELECT * FROM Caso JOIN Colpa ON Caso.N_Caso=Colpa.Caso JOIN Colpevole ON Colpevole.ID_Colpevole=Colpa.Colpevole WHERE N_Caso=?  AND Approvato = 1";
            $result = $this->db->query($query, [$id], "i");
            
             $colpevoli = [];

            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $colpevoli[] = $row;
                }
            }

        $this->db->chiudiConnessione();
        return $colpevoli;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero colpevoli di un caso: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Recupera gli articoli di un caso tramite ID
     * @param int $ID del caso
     * @return array|null Dati degli articoli o null
     */
    public function getArticoliByCaso($id) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT * FROM Caso JOIN Articolo ON Caso.N_Caso=Articolo.Caso WHERE N_Caso=?";
            $result = $this->db->query($query, [$id], "i");
            
             $articoli = [];

            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $articoli[] = $row;
                }
            }

        $this->db->chiudiConnessione();
        return $articoli;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero articoli di un caso: " . $e->getMessage());
            return null;
        }
    }


    /**
     * Cerca casi per titolo o descrizione
     * @param string $query Testo da cercare
     * @param int $limite Numero massimo di risultati
     * @return array Lista di casi trovati
     */
    public function cercaCasi($query, $limite = 20) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $searchTerm = "%{$query}%";
            $sql = "SELECT N_Caso, Titolo, Descrizione, Immagine, Tipologia, Data, Luogo 
                    FROM Caso 
                    WHERE (Titolo LIKE ? OR Descrizione LIKE ?) 
                    AND Approvato = 1 
                    ORDER BY Data DESC 
                    LIMIT ?";
            
            $result = $this->db->query($sql, [$searchTerm, $searchTerm, $limite], "ssi");
            
            $casi = [];
            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $casi[] = $row;
                }
            }
            
            $this->db->chiudiConnessione();
            return $casi;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore ricerca casi: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Recupera casi in attesa di approvazione (solo admin)
     * @param int $limite Numero massimo di risultati
     * @return array Lista di casi non approvati
     */
    public function getCasiNonApprovati($limite = 50) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT c.*, u.Username as Autore_Username 
                      FROM Caso c
                      LEFT JOIN Utente u ON c.Autore = u.Email
                      WHERE c.Approvato = 0 
                      ORDER BY c.Data DESC 
                      LIMIT ?";
            
            $result = $this->db->query($query, [$limite], "i");
            
            $casi = [];
            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $casi[] = $row;
                }
            }
            
            $this->db->chiudiConnessione();
            return $casi;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero casi non approvati: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Approva un caso (solo admin)
     * @param int $nCaso ID del caso
     * @return bool True se l'operazione ha successo
     */
    public function approvaCaso($nCaso) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "UPDATE Caso SET Approvato = 1 WHERE N_Caso = ?";
            $result = $this->db->query($query, [$nCaso], "i");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore approvazione caso: " . $e->getMessage());
            return false;
        }
    }
    /**
 * Inserisce un nuovo commento
 * @param string $emailUtente Email dell'utente (chiave esterna)
 * @param int $idCaso ID del caso
 * @param string $commento Testo del commento
 * @return array ['success' => bool, 'message' => string, 'commento_id' => int|null]
 */
public function inserisciCommento($emailUtente, $idCaso, $commento) {
    try {
        if (!$this->db->apriConnessione()) {
            throw new Exception("Impossibile connettersi al database");
        }
        
        // Validazione base
        if (empty($emailUtente) || empty($idCaso) || empty($commento)) {
            $this->db->chiudiConnessione();
            return [
                'success' => false,
                'message' => 'Tutti i campi sono obbligatori',
                'commento_id' => null
            ];
        }
        
        
        if (strlen($commento) > 2000) {
            $this->db->chiudiConnessione();
            return [
                'success' => false,
                'message' => 'Il commento non può superare i 2000 caratteri',
                'commento_id' => null
            ];
        }
        
        
        $query = "INSERT INTO Commento (Commento, Email_Utente, ID_Caso) 
                  VALUES (?, ?, ?)";
        
        $result = $this->db->query($query, [$commento, $emailUtente, $idCaso], "ssi");
        
        if ($result) {
            $commentoId = $this->db->getLastInsertId();
            $this->db->chiudiConnessione();
            
            return [
                'success' => true,
                'message' => 'Commento pubblicato con successo',
                'commento_id' => $commentoId
            ];
        } else {
            // Log dell'errore MySQL
            error_log("Errore MySQL inserimento commento: " . mysqli_error($this->db->getConnessione()));
            throw new Exception("Errore durante l'inserimento del commento");
        }
        
    } catch (Exception $e) {
        $this->db->chiudiConnessione();
        error_log("Errore inserimento commento: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Errore durante la pubblicazione del commento: ' . $e->getMessage(),
            'commento_id' => null
        ];
    }
}

    /**
     * Recupera i commenti di un caso
     * @param int $idCaso ID del caso
     * @param int $limite Numero massimo di commenti
     * @return array Lista di commenti con dati utente
     */
    public function getCommentiCaso($idCaso, $limite = 50) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT c.ID_Commento, c.Data, c.Commento, 
                            u.Username, u.Email
                    FROM Commento c
                    JOIN Utente u ON c.Email_Utente = u.Email
                    WHERE c.ID_Caso = ?
                    ORDER BY c.Data DESC, c.ID_Commento DESC
                    LIMIT ?";
            
            $result = $this->db->query($query, [$idCaso, $limite], "ii");
            
            $commenti = [];
            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $commenti[] = $row;
                }
            }
            
            $this->db->chiudiConnessione();
            return $commenti;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore recupero commenti: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta i commenti di un caso
     * @param int $idCaso ID del caso
     * @return int Numero di commenti
     */
    public function contaCommentiCaso($idCaso) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT COUNT(*) as totale FROM Commento WHERE ID_Caso = ?";
            $result = $this->db->query($query, [$idCaso], "i");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return (int)$row['totale'];
            }
            
            $this->db->chiudiConnessione();
            return 0;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore conteggio commenti: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Elimina un commento (solo se l'utente è il proprietario o admin)
     * @param int $idCommento ID del commento
     * @param string $emailUtente Email dell'utente che richiede l'eliminazione
     * @param bool $isAdmin Se l'utente è admin
     * @return array ['success' => bool, 'message' => string]
     */
    public function eliminaCommento($idCommento, $emailUtente, $isAdmin = false) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Verifica proprietà del commento
            if (!$isAdmin) {
                $query = "SELECT Email_Utente FROM Commento WHERE ID_Commento = ?";
                $result = $this->db->query($query, [$idCommento], "i");
                
                if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    
                    if ($row['Email_Utente'] !== $emailUtente) {
                        $this->db->chiudiConnessione();
                        return [
                            'success' => false,
                            'message' => 'Non hai i permessi per eliminare questo commento'
                        ];
                    }
                } else {
                    $this->db->chiudiConnessione();
                    return [
                        'success' => false,
                        'message' => 'Commento non trovato'
                    ];
                }
            }
            
            // Elimina il commento
            $query = "DELETE FROM Commento WHERE ID_Commento = ?";
            $result = $this->db->query($query, [$idCommento], "i");
            
            $this->db->chiudiConnessione();
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Commento eliminato con successo'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Errore durante l\'eliminazione'
                ];
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore eliminazione commento: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore durante l\'eliminazione del commento'
            ];
        }
    }
    


    // ========================================
    // GESTIONE CASO COMPLETO (NUOVE FUNZIONI)
    // ========================================
    
    /**
     * Inserisce un caso completo con generazione automatica dello slug
     */
    public function inserisciCaso($titolo, $data, $luogo, $descrizione, $storia, $tipologia = null, $immagine = null, $autoreEmail = '') {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($titolo) || empty($data) || empty($luogo) || empty($descrizione) || empty($storia)) {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Tutti i campi obbligatori devono essere compilati',
                    'caso_id' => null
                ];
            }
            
            $slug = $this->generaSlugUnico($titolo);
            
            $query = "INSERT INTO Caso (Titolo, Slug, Data, Luogo, Descrizione, Storia, Tipologia, Immagine, Approvato, Autore) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, '')";
            
            $params = [
                $titolo,
                $slug,
                $data,
                $luogo,
                $descrizione,
                $storia,
                $tipologia,
                $immagine
            ];
            
            $result = $this->db->query($query, $params, "ssssssss");
            
            if ($result) {
                $casoId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                
                return [
                    'success' => true,
                    'message' => 'Caso inserito con successo. In attesa di approvazione.',
                    'caso_id' => $casoId
                ];
            } else {
                throw new Exception("Errore durante l'inserimento del caso");
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore inserimento caso completo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore durante l\'inserimento. Riprova più tardi.',
                'caso_id' => null
            ];
        }
    }

    private function generaSlugUnico($titolo) {
        $slug = strtolower($titolo);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug = substr($slug, 0, 200);
        
        $slugBase = $slug;
        $counter = 1;
        
        while ($this->slugEsiste($slug)) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    private function slugEsiste($slug) {
        $query = "SELECT N_Caso FROM Caso WHERE Slug = ?";
        $result = $this->db->query($query, [$slug], "s");
        
        if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
            return true;
        }
        return false;
    }

    // ========================================
    // GESTIONE VITTIME
    // ========================================
    
    public function inserisciVittima($casoId, $nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $dataDecesso = null) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($nome) || empty($cognome) || empty($casoId)) {
                $this->db->chiudiConnessione();
                return null;
            }
            
            $dataNascitaFinal = !empty($dataNascita) ? $dataNascita : '1980-01-01';
            $dataDecessoFinal = !empty($dataDecesso) ? $dataDecesso : null;
            
            $query = "INSERT INTO Vittima (Nome, Cognome, LuogoNascita, DataNascita, DataDecesso, Caso, Immagine) 
                      VALUES (?, ?, ?, ?, ?, ?, '')";
            
            $params = [
                $nome,
                $cognome,
                $luogoNascita,
                $dataNascitaFinal,
                $dataDecessoFinal,
                $casoId
            ];
            
            $result = $this->db->query($query, $params, "sssssi");
            
            if ($result) {
                $vittimaId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                return $vittimaId;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore inserimento vittima: " . $e->getMessage());
            return null;
        }
    }

    // ========================================
    // GESTIONE COLPEVOLI
    // ========================================
    
    public function inserisciColpevole($nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($nome) || empty($cognome)) {
                $this->db->chiudiConnessione();
                return null;
            }
            
            $dataNascitaFinal = !empty($dataNascita) ? $dataNascita : '1990-01-01';
            
            $query = "INSERT INTO Colpevole (Nome, Cognome, LuogoNascita, DataNascita, Immagine) 
                      VALUES (?, ?, ?, ?, '')";
            
            $params = [
                $nome,
                $cognome,
                $luogoNascita,
                $dataNascitaFinal
            ];
            
            $result = $this->db->query($query, $params, "ssss");
            
            if ($result) {
                $colpevoleId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                return $colpevoleId;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore inserimento colpevole: " . $e->getMessage());
            return null;
        }
    }

    public function collegaColpevoleACaso($colpevoleId, $casoId) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "INSERT INTO Colpa (Colpevole, Caso) VALUES (?, ?)";
            $result = $this->db->query($query, [$colpevoleId, $casoId], "ii");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore collegamento colpevole-caso: " . $e->getMessage());
            return false;
        }
    }

    // ========================================
    // GESTIONE ARTICOLI/FONTI
    // ========================================
    
    public function inserisciArticolo($casoId, $titolo, $data = null, $link = '') {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($casoId) || (empty($titolo) && empty($link))) {
                $this->db->chiudiConnessione();
                return null;
            }
            
            $dataFinal = !empty($data) ? $data : date('Y-m-d');
            $linkFinal = !empty($link) ? $link : 'https://source-unavailable.com';
            
            $query = "INSERT INTO Articolo (Titolo, Data, Link, Caso) 
                      VALUES (?, ?, ?, ?)";
            
            $params = [
                $titolo,
                $dataFinal,
                $linkFinal,
                $casoId
            ];
            
            $result = $this->db->query($query, $params, "sssi");
            
            if ($result) {
                $articoloId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                return $articoloId;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            error_log("Errore inserimento articolo: " . $e->getMessage());
            return null;
        }
    }

}
?>