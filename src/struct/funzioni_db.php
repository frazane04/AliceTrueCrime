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
     * Inserisce un nuovo caso nel database (non approvato di default)
     * @param string $titolo Titolo del caso
     * @param string $data Data dell'accaduto (formato YYYY-MM-DD)
     * @param string $luogo Luogo del caso
     * @param string $descrizione Descrizione dettagliata
     * @param string $autoreEmail Email dell'autore (chiave esterna)
     * @param string|null $immagine Path dell'immagine (opzionale)
     * @param string|null $tipologia Categoria del caso (opzionale)
     * @return array ['success' => bool, 'message' => string, 'caso_id' => int|null]
     */
    public function inserisciCaso($titolo, $data, $luogo, $descrizione, $immagine = null, $tipologia = null) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Validazione base
            if (empty($titolo) || empty($data) || empty($luogo) || empty($descrizione)) {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Tutti i campi obbligatori devono essere compilati',
                    'caso_id' => null
                ];
            }
            
            // Insert caso (Approvato = 0 di default)
            $query = "INSERT INTO Caso (Titolo, Data, Luogo, Descrizione, Immagine, Tipologia, Approvato) 
                      VALUES (?, ?, ?, ?, ?, ?, 0)";
            
            $params = [
                $titolo,
                $data,
                $luogo,
                $descrizione,
                $tipologia,
                $immagine
                
            ];
            
            $result = $this->db->query($query, $params, "ssssss");
            
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
            error_log("Errore inserimento caso: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore durante l\'inserimento. Riprova più tardi.'. var_dump($params),
                'caso_id' => null
            ];
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
}
?>