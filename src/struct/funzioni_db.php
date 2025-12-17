<?php
/**
 * Funzioni per interagire con il database
 * Utilizzano prepared statements per prevenire SQL Injection
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
     * @param string $username
     * @param string $password Password in chiaro (verrà hashata)
     * @param bool $isAdmin Default false
     * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
     */
    public function registraUtente($username, $password, $isAdmin = false) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Verifica se username esiste già
            if ($this->verificaUsernameEsistente($username)) {
                $this->db->chiudiConnessione();
                return [
                    'success' => false,
                    'message' => 'Username già in uso',
                    'user_id' => null
                ];
            }
            
            // Hash della password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $isAdminInt = $isAdmin ? 1 : 0;
            
            // Insert utente
            $query = "INSERT INTO Utente (Username, Password, Is_Admin) VALUES (?, ?, ?)";
            $result = $this->db->query($query, [$username, $passwordHash, $isAdminInt], "ssi");
            
            if ($result) {
                $userId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                
                return [
                    'success' => true,
                    'message' => 'Registrazione completata con successo',
                    'user_id' => $userId
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
                'user_id' => null
            ];
        }
    }
    
    /**
     * Verifica se uno username esiste già
     * @param string $username
     * @return bool
     */
    private function verificaUsernameEsistente($username) {
        $query = "SELECT ID_Utente FROM Utente WHERE Username = ?";
        $result = $this->db->query($query, [$username], "s");
        
        if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Effettua il login verificando username e password
     * @param string $username
     * @param string $password Password in chiaro
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function loginUtente($username, $password) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT ID_Utente, Username, Password, Is_Admin FROM Utente WHERE Username = ?";
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
                            'id' => $user['ID_Utente'],
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
     * Ottiene i dati di un utente tramite ID
     * @param int $userId
     * @return array|null
     */
    public function getUtenteById($userId) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT ID_Utente, Username, Is_Admin FROM Utente WHERE ID_Utente = ?";
            $result = $this->db->query($query, [$userId], "i");
            
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
}
?>