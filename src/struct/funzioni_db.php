<?php
/**
 * Funzioni per interagire con il database
 * Utilizzano prepared statements per prevenire SQL Injection
 * 
 * REFACTORED: Rimossa duplicazione metodi admin/utente
 * Ora si usa un parametro $soloApprovati per controllare il filtro
 * 
 * AGGIORNATO: Aggiunto supporto per upload immagini
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
     * Registra un nuovo utente
     */
    public function registraUtente($email, $username, $password, $isAdmin = false) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Email non valida'];
            }
            
            if ($this->verificaEmailEsistente($email)) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Email già registrata'];
            }
            
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $isAdminInt = $isAdmin ? 1 : 0;
            
            $query = "INSERT INTO Utente (Email, Username, Password, Is_Admin, Is_Newsletter) VALUES (?, ?, ?, ?, 0)";
            $result = $this->db->query($query, [$email, $username, $passwordHash, $isAdminInt], "sssi");
            
            $this->db->chiudiConnessione();
            return ['success' => true, 'email' => $email];
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Aggiorna lo stato newsletter dell'utente
     */
    public function updateNewsletter($email, $stato) {
        try {
            if (!$this->db->apriConnessione()) return false;
            $query = "UPDATE Utente SET Is_Newsletter = ? WHERE Email = ?";
            $result = $this->db->query($query, [$stato, $email], "is");
            $this->db->chiudiConnessione();
            return (bool)$result;
        } catch (Exception $e) { 
            return false; 
        }
    }
  
    /**
     * Verifica se un'email esiste già
     */
    private function verificaEmailEsistente($email) {
        $query = "SELECT Email FROM Utente WHERE Email = ?";
        $result = $this->db->query($query, [$email], "s");
        
        return ($result && is_object($result) && mysqli_num_rows($result) > 0);
    }
    
    /**
     * Effettua il login verificando email e password
     */
    public function loginUtenteEmail($email, $password) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Email non valida', 'user' => null];
            }
            
            $query = "SELECT Email, Username, Password, Is_Admin FROM Utente WHERE Email = ?";
            $result = $this->db->query($query, [$email], "s");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                
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
                    return ['success' => false, 'message' => 'Password non corretta', 'user' => null];
                }
            } else {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Email non trovata', 'user' => null];
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore durante il login.', 'user' => null];
        }
    }
    
    /**
     * Effettua il login verificando username e password
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
                    return ['success' => false, 'message' => 'Password non corretta', 'user' => null];
                }
            } else {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Username non trovato', 'user' => null];
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore durante il login.', 'user' => null];
        }
    }
    
    /**
     * Ottiene i dati di un utente tramite Email
     */
    public function getUtenteByEmail($email) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT Email, Username, Is_Admin, Is_Newsletter FROM Utente WHERE Email = ?";
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
            return null;
        }
    }
    
    /**
     * Ottiene i dati di un utente tramite Username
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
            return null;
        }
    }

    // ========================================
    // GESTIONE CASI - METODI REFACTORIZZATI
    // ========================================
    
    /**
     * Recupera gli ultimi casi per l'area newsletter
     * 
     * @param int $limite Numero massimo di casi
     * @param bool $soloApprovati Se true, filtra solo i casi approvati
     */
    public function getContenutiNewsletter($limite = 6, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) return [];
            
            $query = "SELECT N_Caso, Titolo, Slug, Descrizione, Data FROM Caso";
            
            if ($soloApprovati) {
                $query .= " WHERE Approvato = 1";
            }
            
            $query .= " ORDER BY Data DESC LIMIT ?";
            
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
            return [];
        }
    }
    
    /**
     * Recupera l'ID di un caso dal suo slug
     * 
     * @param string $slug Lo slug del caso
     * @param bool $soloApprovati Se true (default), cerca solo tra i casi approvati
     * @return int|null L'ID del caso o null se non trovato
     */
    public function getCasoIdBySlug($slug, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT N_Caso FROM Caso WHERE Slug = ?";
            
            if ($soloApprovati) {
                $query .= " AND Approvato = 1";
            }
            
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
            return null;
        }
    }
    
    /**
     * Recupera casi per categoria
     * 
     * @param string $tipologia La categoria del caso
     * @param int $limite Numero massimo di risultati
     * @param bool $soloApprovati Se true (default), filtra solo approvati
     */
    public function getCasiPerCategoria($tipologia, $limite = 10, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT N_Caso, Titolo, Slug, Descrizione, Immagine, Tipologia, Data, Luogo 
                      FROM Caso 
                      WHERE Tipologia = ?";
            
            if ($soloApprovati) {
                $query .= " AND Approvato = 1";
            }
            
            $query .= " ORDER BY Data DESC LIMIT ?";
            
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
            return [];
        }
    }
    
    /**
     * Recupera i casi più recenti/letti
     * 
     * @param int $limite Numero massimo di risultati
     * @param bool $soloApprovati Se true (default), filtra solo approvati
     */
    public function getCasiPiuLetti($limite = 5, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT N_Caso, Titolo, Slug, Descrizione, Immagine, Tipologia, Data, Luogo 
                      FROM Caso";
            
            if ($soloApprovati) {
                $query .= " WHERE Approvato = 1";
            }
            
            $query .= " ORDER BY Data DESC LIMIT ?";
            
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
            return [];
        }
    }
    
    /**
     * Recupera un singolo caso tramite ID
     * 
     * @param int $nCaso ID del caso
     * @param bool $soloApprovati Se true (default), cerca solo tra i casi approvati
     * @return array|null Dati del caso o null se non trovato
     */
    public function getCasoById($nCaso, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT * FROM Caso WHERE N_Caso = ?";
            
            if ($soloApprovati) {
                $query .= " AND Approvato = 1";
            }
            
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
            return null;
        }
    }

    /**
     * Recupera le vittime di un caso
     * 
     * @param int $id ID del caso
     * @param bool $soloApprovati Se true (default), filtra per casi approvati
     */
    public function getVittimeByCaso($id, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if ($soloApprovati) {
                $query = "SELECT v.* FROM Vittima v 
                          JOIN Caso c ON v.Caso = c.N_Caso 
                          WHERE c.N_Caso = ? AND c.Approvato = 1";
            } else {
                $query = "SELECT * FROM Vittima WHERE Caso = ?";
            }
            
            $result = $this->db->query($query, [$id], "i");
            $vittime = [];
            
            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $vittime[] = $row;
                }
            }

            $this->db->chiudiConnessione();
            return $vittime;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return [];
        }
    }

    /**
     * Recupera i colpevoli di un caso
     * 
     * @param int $id ID del caso
     * @param bool $soloApprovati Se true (default), filtra per casi approvati
     */
    public function getColpevoliByCaso($id, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if ($soloApprovati) {
                $query = "SELECT col.* FROM Colpevole col 
                          JOIN Colpa cp ON col.ID_Colpevole = cp.Colpevole 
                          JOIN Caso c ON cp.Caso = c.N_Caso 
                          WHERE c.N_Caso = ? AND c.Approvato = 1";
            } else {
                $query = "SELECT c.* FROM Colpevole c 
                          JOIN Colpa cp ON c.ID_Colpevole = cp.Colpevole 
                          WHERE cp.Caso = ?";
            }
            
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
            return [];
        }
    }

    /**
     * Recupera gli articoli di un caso
     * Nota: Gli articoli non hanno filtro approvazione perché sono legati al caso
     */
    public function getArticoliByCaso($id) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT * FROM Articolo WHERE Caso = ?";
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
            return [];
        }
    }

    /**
     * Cerca casi per titolo o descrizione
     * 
     * @param string $query Termine di ricerca
     * @param int $limite Numero massimo risultati
     * @param bool $soloApprovati Se true (default), filtra solo approvati
     */
    public function cercaCasi($query, $limite = 20, $soloApprovati = true) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $searchTerm = "%{$query}%";
            $sql = "SELECT N_Caso, Titolo, Slug, Descrizione, Immagine, Tipologia, Data, Luogo 
                    FROM Caso 
                    WHERE (Titolo LIKE ? OR Descrizione LIKE ?)";
            
            if ($soloApprovati) {
                $sql .= " AND Approvato = 1";
            }
            
            $sql .= " ORDER BY Data DESC LIMIT ?";
            
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
            return [];
        }
    }
    
    /**
     * Recupera casi in attesa di approvazione (solo admin)
     * Questo metodo resta separato perché ha una logica specifica
     */
    public function getCasiNonApprovati($limite = 50) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT N_Caso, Titolo, Slug, Descrizione, Data, Luogo, Tipologia, Data_Inserimento, Autore
                      FROM Caso
                      WHERE Approvato = 0 
                      ORDER BY Data_Inserimento DESC 
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
            return [];
        }
    }
    
    /**
     * Approva un caso (solo admin)
     */
    public function approvaCaso($nCaso) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "UPDATE Caso SET Approvato = 1 WHERE N_Caso = ?";
            $result = $this->db->query($query, [$nCaso], "i");
            
            $this->db->chiudiConnessione();
            
            if ($result) {
                return ['success' => true, 'message' => 'Caso approvato con successo'];
            }
            return ['success' => false, 'message' => 'Errore durante l\'approvazione'];
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage()];
        }
    }

    /**
     * Rifiuta/Elimina un caso (solo admin)
     */
    public function rifiutaCaso($nCaso) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Prima elimina le relazioni collegate
            $this->db->query("DELETE FROM Commento WHERE ID_Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Articolo WHERE Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Vittima WHERE Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Colpa WHERE Caso = ?", [$nCaso], "i");
            
            // Poi elimina il caso
            $query = "DELETE FROM Caso WHERE N_Caso = ?";
            $result = $this->db->query($query, [$nCaso], "i");
            
            $this->db->chiudiConnessione();
            
            if ($result) {
                return ['success' => true, 'message' => 'Caso rifiutato e rimosso con successo'];
            }
            return ['success' => false, 'message' => 'Errore durante l\'eliminazione'];
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage()];
        }
    }

    // ========================================
    // GESTIONE COMMENTI
    // ========================================

    /**
     * Inserisce un nuovo commento
     */
    public function inserisciCommento($emailUtente, $idCaso, $commento) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($emailUtente) || empty($idCaso) || empty($commento)) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Tutti i campi sono obbligatori', 'commento_id' => null];
            }
            
            if (strlen($commento) > 2000) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Il commento non può superare i 2000 caratteri', 'commento_id' => null];
            }
            
            $query = "INSERT INTO Commento (Commento, Email_Utente, ID_Caso) VALUES (?, ?, ?)";
            $result = $this->db->query($query, [$commento, $emailUtente, $idCaso], "ssi");
            
            if ($result) {
                $commentoId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                return ['success' => true, 'message' => 'Commento pubblicato con successo', 'commento_id' => $commentoId];
            } else {
                throw new Exception("Errore durante l'inserimento del commento");
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage(), 'commento_id' => null];
        }
    }

    /**
     * Recupera i commenti di un caso
     */
    public function getCommentiCaso($idCaso, $limite = 50) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $query = "SELECT c.ID_Commento, c.Data, c.Commento, u.Username, u.Email
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
            return [];
        }
    }

    /**
     * Conta i commenti di un caso
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
            return 0;
        }
    }

    /**
     * Elimina un commento
     */
    public function eliminaCommento($idCommento, $emailUtente, $isAdmin = false) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (!$isAdmin) {
                $query = "SELECT Email_Utente FROM Commento WHERE ID_Commento = ?";
                $result = $this->db->query($query, [$idCommento], "i");
                
                if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    
                    if ($row['Email_Utente'] !== $emailUtente) {
                        $this->db->chiudiConnessione();
                        return ['success' => false, 'message' => 'Non hai i permessi per eliminare questo commento'];
                    }
                } else {
                    $this->db->chiudiConnessione();
                    return ['success' => false, 'message' => 'Commento non trovato'];
                }
            }
            
            $query = "DELETE FROM Commento WHERE ID_Commento = ?";
            $result = $this->db->query($query, [$idCommento], "i");
            
            $this->db->chiudiConnessione();
            
            if ($result) {
                return ['success' => true, 'message' => 'Commento eliminato con successo'];
            } else {
                return ['success' => false, 'message' => 'Errore durante l\'eliminazione'];
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage()];
        }
    }

    // ========================================
    // GESTIONE CASO COMPLETO
    // ========================================
    
    /**
     * Inserisce un caso completo con generazione automatica dello slug
     * AGGIORNATO: Supporto immagine
     */
    public function inserisciCaso($titolo, $data, $luogo, $descrizione, $storia, $tipologia = null, $immagine = null, $autoreEmail = '') {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($titolo) || empty($data) || empty($luogo) || empty($descrizione) || empty($storia)) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Tutti i campi obbligatori devono essere compilati', 'caso_id' => null, 'slug' => null];
            }
            
            $slug = $this->generaSlugUnico($titolo);
            
            $query = "INSERT INTO Caso (Titolo, Slug, Data, Luogo, Descrizione, Storia, Tipologia, Immagine, Approvato, Autore) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
            
            $params = [$titolo, $slug, $data, $luogo, $descrizione, $storia, $tipologia, $immagine, $autoreEmail];
            
            $result = $this->db->query($query, $params, "sssssssss");
            
            if ($result) {
                $casoId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                return ['success' => true, 'message' => 'Caso inserito con successo.', 'caso_id' => $casoId, 'slug' => $slug];
            } else {
                throw new Exception("Errore durante l'inserimento del caso");
            }
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage(), 'caso_id' => null, 'slug' => null];
        }
    }

    /**
     * Genera uno slug unico dal titolo
     */
    public function generaSlugUnico($titolo) {
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

    /**
     * Verifica se uno slug esiste già
     */
    private function slugEsiste($slug) {
        $query = "SELECT N_Caso FROM Caso WHERE Slug = ?";
        $result = $this->db->query($query, [$slug], "s");
        
        return ($result && is_object($result) && mysqli_num_rows($result) > 0);
    }

    /**
     * Recupera lo slug di un caso dall'ID
     */
    public function getSlugById($casoId) {
        try {
            if (!$this->db->apriConnessione()) {
                return null;
            }
            
            $query = "SELECT Slug FROM Caso WHERE N_Caso = ?";
            $result = $this->db->query($query, [$casoId], "i");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return $row['Slug'];
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return null;
        }
    }

    // ========================================
    // GESTIONE VITTIME
    // ========================================
    
    /**
     * Inserisce una vittima
     * AGGIORNATO: Supporto immagine
     */
    public function inserisciVittima($casoId, $nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $dataDecesso = null, $immagine = null) {
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
            $immagineFinal = !empty($immagine) ? $immagine : '';
            
            $query = "INSERT INTO Vittima (Nome, Cognome, LuogoNascita, DataNascita, DataDecesso, Caso, Immagine) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [$nome, $cognome, $luogoNascita, $dataNascitaFinal, $dataDecessoFinal, $casoId, $immagineFinal];
            
            $result = $this->db->query($query, $params, "sssssis");
            
            if ($result) {
                $vittimaId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                return $vittimaId;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return null;
        }
    }

    // ========================================
    // GESTIONE COLPEVOLI
    // ========================================
    
    /**
     * Inserisce un colpevole
     * AGGIORNATO: Supporto immagine
     */
    public function inserisciColpevole($nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $immagine = null) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($nome) || empty($cognome)) {
                $this->db->chiudiConnessione();
                return null;
            }
            
            $dataNascitaFinal = !empty($dataNascita) ? $dataNascita : '1990-01-01';
            $immagineFinal = !empty($immagine) ? $immagine : '';
            
            $query = "INSERT INTO Colpevole (Nome, Cognome, LuogoNascita, DataNascita, Immagine) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $params = [$nome, $cognome, $luogoNascita, $dataNascitaFinal, $immagineFinal];
            
            $result = $this->db->query($query, $params, "sssss");
            
            if ($result) {
                $colpevoleId = $this->db->getLastInsertId();
                $this->db->chiudiConnessione();
                return $colpevoleId;
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return null;
        }
    }

    /**
     * Collega un colpevole a un caso
     */
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
            return false;
        }
    }

    // ========================================
    // GESTIONE ARTICOLI/FONTI
    // ========================================
    
    /**
     * Inserisce un articolo/fonte
     */
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
            
            $query = "INSERT INTO Articolo (Titolo, Data, Link, Caso) VALUES (?, ?, ?, ?)";
            
            $params = [$titolo, $dataFinal, $linkFinal, $casoId];
            
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
            return null;
        }
    }

    /**
     * Incrementa il contatore delle visualizzazioni di un caso
     * Viene chiamato quando un utente visualizza un caso approvato
     * 
     * @param int $casoId - ID del caso
     * @return bool - true se l'incremento è avvenuto con successo
     */
    public function incrementaVisualizzazioni($casoId) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "UPDATE caso SET Visualizzazioni = Visualizzazioni + 1 WHERE N_Caso = ? AND Approvato = 1";
            $result = $this->db->query($query, [$casoId], "i");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // ========================================
    // GESTIONE MODIFICA CASO
    // ========================================

    /**
     * Aggiorna i dati principali di un caso
     * AGGIORNATO: Supporto immagine opzionale
     * 
     * @param int $nCaso ID del caso
     * @param string $titolo Nuovo titolo
     * @param string $data Nuova data
     * @param string $luogo Nuovo luogo
     * @param string $descrizione Nuova descrizione
     * @param string $storia Nuova storia
     * @param string|null $tipologia Nuova tipologia
     * @param bool $riApprova Se true, imposta Approvato = 0
     * @param string|null $immagine Nuovo percorso immagine (null = non modificare)
     * @return array Risultato operazione
     */
    public function aggiornaCaso($nCaso, $titolo, $data, $luogo, $descrizione, $storia, $tipologia = null, $riApprova = false, $immagine = null) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            if (empty($titolo) || empty($data) || empty($luogo) || empty($descrizione) || empty($storia)) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Tutti i campi obbligatori devono essere compilati'];
            }
            
            // Costruisci la query in base ai parametri
            if ($immagine !== null) {
                // Aggiorna anche l'immagine
                if ($riApprova) {
                    $query = "UPDATE Caso SET Titolo = ?, Data = ?, Luogo = ?, Descrizione = ?, Storia = ?, Tipologia = ?, Immagine = ?, Approvato = 0 WHERE N_Caso = ?";
                } else {
                    $query = "UPDATE Caso SET Titolo = ?, Data = ?, Luogo = ?, Descrizione = ?, Storia = ?, Tipologia = ?, Immagine = ? WHERE N_Caso = ?";
                }
                $params = [$titolo, $data, $luogo, $descrizione, $storia, $tipologia, $immagine, $nCaso];
                $types = "sssssssi";
            } else {
                // Non toccare l'immagine
                if ($riApprova) {
                    $query = "UPDATE Caso SET Titolo = ?, Data = ?, Luogo = ?, Descrizione = ?, Storia = ?, Tipologia = ?, Approvato = 0 WHERE N_Caso = ?";
                } else {
                    $query = "UPDATE Caso SET Titolo = ?, Data = ?, Luogo = ?, Descrizione = ?, Storia = ?, Tipologia = ? WHERE N_Caso = ?";
                }
                $params = [$titolo, $data, $luogo, $descrizione, $storia, $tipologia, $nCaso];
                $types = "ssssssi";
            }
            
            $result = $this->db->query($query, $params, $types);
            
            $this->db->chiudiConnessione();
            
            if ($result) {
                return ['success' => true, 'message' => 'Caso aggiornato con successo'];
            }
            return ['success' => false, 'message' => 'Errore durante l\'aggiornamento'];
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage()];
        }
    }

    /**
     * Aggiorna solo l'immagine di un caso
     */
    public function aggiornaImmagineCaso($nCaso, $immagine) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "UPDATE Caso SET Immagine = ? WHERE N_Caso = ?";
            $result = $this->db->query($query, [$immagine, $nCaso], "si");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Verifica se un utente può modificare un caso
     * 
     * @param int $nCaso ID del caso
     * @param string $emailUtente Email dell'utente
     * @param bool $isAdmin Se l'utente è admin
     * @return bool True se può modificare
     */
    public function puoModificareCaso($nCaso, $emailUtente, $isAdmin = false) {
        // Admin può sempre modificare
        if ($isAdmin) {
            return true;
        }
        
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            // Verifica se l'utente è l'autore
            $query = "SELECT Autore FROM Caso WHERE N_Caso = ?";
            $result = $this->db->query($query, [$nCaso], "i");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return ($row['Autore'] === $emailUtente);
            }
            
            $this->db->chiudiConnessione();
            return false;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Elimina un caso completo (con tutte le relazioni)
     * Può essere usato da admin o autore
     * 
     * @param int $nCaso ID del caso
     * @param string $emailUtente Email dell'utente che richiede l'eliminazione
     * @param bool $isAdmin Se l'utente è admin
     * @return array Risultato operazione
     */
    public function eliminaCaso($nCaso, $emailUtente, $isAdmin = false) {
        try {
            // Verifica permessi
            if (!$this->puoModificareCaso($nCaso, $emailUtente, $isAdmin)) {
                return ['success' => false, 'message' => 'Non hai i permessi per eliminare questo caso'];
            }
            
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            // Elimina tutte le relazioni collegate (stesso codice di rifiutaCaso)
            $this->db->query("DELETE FROM Commento WHERE ID_Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Articolo WHERE Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Vittima WHERE Caso = ?", [$nCaso], "i");
            
            // Per i colpevoli: elimina solo la relazione, non il colpevole stesso
            // (potrebbe essere collegato ad altri casi)
            $this->db->query("DELETE FROM Colpa WHERE Caso = ?", [$nCaso], "i");
            
            // Elimina il caso
            $query = "DELETE FROM Caso WHERE N_Caso = ?";
            $result = $this->db->query($query, [$nCaso], "i");
            
            $this->db->chiudiConnessione();
            
            if ($result) {
                return ['success' => true, 'message' => 'Caso eliminato con successo'];
            }
            return ['success' => false, 'message' => 'Errore durante l\'eliminazione'];
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage()];
        }
    }

    // ========================================
    // GESTIONE VITTIME - MODIFICA/ELIMINAZIONE
    // ========================================

    /**
     * Aggiorna una vittima esistente
     * AGGIORNATO: Supporto immagine
     */
    public function aggiornaVittima($idVittima, $nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $dataDecesso = null, $immagine = null) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $dataNascitaFinal = !empty($dataNascita) ? $dataNascita : '1980-01-01';
            
            if ($immagine !== null) {
                $query = "UPDATE Vittima SET Nome = ?, Cognome = ?, LuogoNascita = ?, DataNascita = ?, DataDecesso = ?, Immagine = ? WHERE ID_Vittima = ?";
                $params = [$nome, $cognome, $luogoNascita, $dataNascitaFinal, $dataDecesso, $immagine, $idVittima];
                $types = "ssssssi";
            } else {
                $query = "UPDATE Vittima SET Nome = ?, Cognome = ?, LuogoNascita = ?, DataNascita = ?, DataDecesso = ? WHERE ID_Vittima = ?";
                $params = [$nome, $cognome, $luogoNascita, $dataNascitaFinal, $dataDecesso, $idVittima];
                $types = "sssssi";
            }
            
            $result = $this->db->query($query, $params, $types);
            $this->db->chiudiConnessione();
            
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Aggiorna solo l'immagine di una vittima
     */
    public function aggiornaImmagineVittima($idVittima, $immagine) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "UPDATE Vittima SET Immagine = ? WHERE ID_Vittima = ?";
            $result = $this->db->query($query, [$immagine, $idVittima], "si");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Elimina una vittima
     */
    public function eliminaVittima($idVittima) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "DELETE FROM Vittima WHERE ID_Vittima = ?";
            $result = $this->db->query($query, [$idVittima], "i");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Elimina tutte le vittime di un caso
     */
    public function eliminaVittimeByCaso($casoId) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "DELETE FROM Vittima WHERE Caso = ?";
            $result = $this->db->query($query, [$casoId], "i");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // ========================================
    // GESTIONE COLPEVOLI - MODIFICA/ELIMINAZIONE
    // ========================================

    /**
     * Aggiorna un colpevole esistente
     * AGGIORNATO: Supporto immagine
     */
    public function aggiornaColpevole($idColpevole, $nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $immagine = null) {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $dataNascitaFinal = !empty($dataNascita) ? $dataNascita : '1990-01-01';
            
            if ($immagine !== null) {
                $query = "UPDATE Colpevole SET Nome = ?, Cognome = ?, LuogoNascita = ?, DataNascita = ?, Immagine = ? WHERE ID_Colpevole = ?";
                $params = [$nome, $cognome, $luogoNascita, $dataNascitaFinal, $immagine, $idColpevole];
                $types = "sssssi";
            } else {
                $query = "UPDATE Colpevole SET Nome = ?, Cognome = ?, LuogoNascita = ?, DataNascita = ? WHERE ID_Colpevole = ?";
                $params = [$nome, $cognome, $luogoNascita, $dataNascitaFinal, $idColpevole];
                $types = "ssssi";
            }
            
            $result = $this->db->query($query, $params, $types);
            $this->db->chiudiConnessione();
            
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Aggiorna solo l'immagine di un colpevole
     */
    public function aggiornaImmagineColpevole($idColpevole, $immagine) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "UPDATE Colpevole SET Immagine = ? WHERE ID_Colpevole = ?";
            $result = $this->db->query($query, [$immagine, $idColpevole], "si");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Rimuove un colpevole da un caso (elimina solo la relazione in Colpa)
     */
    public function rimuoviColpevoleDaCaso($idColpevole, $casoId) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "DELETE FROM Colpa WHERE Colpevole = ? AND Caso = ?";
            $result = $this->db->query($query, [$idColpevole, $casoId], "ii");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Rimuove tutti i colpevoli da un caso
     */
    public function rimuoviColpevoliByCaso($casoId) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "DELETE FROM Colpa WHERE Caso = ?";
            $result = $this->db->query($query, [$casoId], "i");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // ========================================
    // GESTIONE ARTICOLI - MODIFICA/ELIMINAZIONE
    // ========================================

    /**
     * Aggiorna un articolo esistente
     */
    public function aggiornaArticolo($idArticolo, $titolo, $data = null, $link = '') {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }
            
            $dataFinal = !empty($data) ? $data : date('Y-m-d');
            $linkFinal = !empty($link) ? $link : 'https://source-unavailable.com';
            
            $query = "UPDATE Articolo SET Titolo = ?, Data = ?, Link = ? WHERE ID_Articolo = ?";
            $params = [$titolo, $dataFinal, $linkFinal, $idArticolo];
            
            $result = $this->db->query($query, $params, "sssi");
            $this->db->chiudiConnessione();
            
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Elimina un articolo
     */
    public function eliminaArticolo($idArticolo) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "DELETE FROM Articolo WHERE ID_Articolo = ?";
            $result = $this->db->query($query, [$idArticolo], "i");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    /**
     * Elimina tutti gli articoli di un caso
     */
    public function eliminaArticoliByCaso($casoId) {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }
            
            $query = "DELETE FROM Articolo WHERE Caso = ?";
            $result = $this->db->query($query, [$casoId], "i");
            
            $this->db->chiudiConnessione();
            return (bool)$result;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // ========================================
    // UTILITY: Recupera autore caso
    // ========================================

    /**
     * Recupera l'email dell'autore di un caso
     */
    public function getAutoreCaso($nCaso) {
        try {
            if (!$this->db->apriConnessione()) {
                return null;
            }
            
            $query = "SELECT Autore FROM Caso WHERE N_Caso = ?";
            $result = $this->db->query($query, [$nCaso], "i");
            
            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return $row['Autore'];
            }
            
            $this->db->chiudiConnessione();
            return null;
            
        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return null;
        }
    }
}
?>