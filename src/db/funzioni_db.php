<?php
require_once __DIR__ . '/connessione.php';

class FunzioniDB
{
    private $db;

    public function __construct()
    {
        $this->db = new ConnessioneDB();
    }

    // Registra un nuovo utente nel database
    public function registraUtente($email, $username, $password, $isAdmin = false, $newsletter = false)
    {
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
                return ['success' => false, 'message' => 'Registrazione non riuscita. Verifica i dati o prova ad accedere se hai già un account.'];
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $isAdminInt = $isAdmin ? 1 : 0;
            $newsletterInt = $newsletter ? 1 : 0;

            $query = "INSERT INTO Utente (Email, Username, Password, Is_Admin, Is_Newsletter) VALUES (?, ?, ?, ?, ?)";
            $result = $this->db->query($query, [$email, $username, $passwordHash, $isAdminInt, $newsletterInt], "sssii");

            $this->db->chiudiConnessione();
            return ['success' => true, 'email' => $email];

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Aggiorna lo stato di iscrizione alla newsletter
    public function updateNewsletter($email, $stato)
    {
        try {
            if (!$this->db->apriConnessione())
                return false;
            $query = "UPDATE Utente SET Is_Newsletter = ? WHERE Email = ?";
            $result = $this->db->query($query, [$stato, $email], "is");
            $this->db->chiudiConnessione();
            return (bool) $result;
        } catch (Exception $e) {
            return false;
        }
    }

    // Verifica se un'email è già registrata
    private function verificaEmailEsistente($email)
    {
        $query = "SELECT Email FROM Utente WHERE Email = ?";
        $result = $this->db->query($query, [$email], "s");

        return ($result && is_object($result) && mysqli_num_rows($result) > 0);
    }

    // Effettua il login tramite email o username
    public function loginUtente($identificativo, $password)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $isEmail = filter_var($identificativo, FILTER_VALIDATE_EMAIL);
            $campo = $isEmail ? 'Email' : 'Username';
            $erroreNonTrovato = $isEmail ? 'Email non trovata' : 'Username non trovato';

            $query = "SELECT Email, Username, Password, Is_Admin FROM Utente WHERE {$campo} = ?";
            $result = $this->db->query($query, [$identificativo], "s");

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
                            'is_admin' => (bool) $user['Is_Admin']
                        ]
                    ];
                } else {
                    $this->db->chiudiConnessione();
                    return ['success' => false, 'message' => 'Password non corretta', 'user' => null];
                }
            } else {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => $erroreNonTrovato, 'user' => null];
            }

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore durante il login.', 'user' => null];
        }
    }

    // Aggiorna username e opzionalmente password di un utente
    public function aggiornaProfilo($email, $nuovoUsername, $nuovaPassword = null)
    {
        try {
            if (!$this->db->apriConnessione()) return false;

            if (!empty($nuovaPassword)) {
                $passwordHash = password_hash($nuovaPassword, PASSWORD_DEFAULT);
                $query = "UPDATE Utente SET Username = ?, Password = ? WHERE Email = ?";
                $params = [$nuovoUsername, $passwordHash, $email];
                $types = "sss";
            } else {
                $query = "UPDATE Utente SET Username = ? WHERE Email = ?";
                $params = [$nuovoUsername, $email];
                $types = "ss";
            }

            $result = $this->db->query($query, $params, $types);
            $this->db->chiudiConnessione();
            return (bool) $result;
        } catch (Exception $e) {
            return false;
        }
    }

    // Login tramite email
    public function loginUtenteEmail($email, $password)
    {
        return $this->loginUtente($email, $password);
    }

    // Login tramite username 
    public function loginUtenteUsername($username, $password)
    {
        return $this->loginUtente($username, $password);
    }

    // Recupera i dati di un utente tramite email
    public function getUtenteByEmail($email)
    {
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

    // Salva il token "ricordami" per il login persistente
    public function salvaRememberToken($email, $token)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $tokenHash = password_hash($token, PASSWORD_DEFAULT);
            $query = "UPDATE Utente SET Remember_Token = ? WHERE Email = ?";
            $result = $this->db->query($query, [$tokenHash, $email], "ss");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Verifica il token "ricordami" e restituisce l'utente
    function verificaRememberToken($email, $token)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return null;
            }

            $query = "SELECT Email, Username, Is_Admin, Is_Newsletter, Remember_Token FROM Utente WHERE Email = ?";
            $result = $this->db->query($query, [$email], "s");

            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                if (!empty($user['Remember_Token']) && password_verify($token, $user['Remember_Token'])) {
                    $this->db->chiudiConnessione();
                    unset($user['Remember_Token']);
                    return $user;
                }
            }

            $this->db->chiudiConnessione();
            return null;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return null;
        }
    }

    // Rimuove il token "ricordami"
    public function rimuoviRememberToken($email)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "UPDATE Utente SET Remember_Token = NULL WHERE Email = ?";
            $result = $this->db->query($query, [$email], "s");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Verifica se un username è già in uso
    public function verificaUsernameEsistente($username)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "SELECT Username FROM Utente WHERE Username = ?";
            $result = $this->db->query($query, [$username], "s");

            $esiste = ($result && is_object($result) && mysqli_num_rows($result) > 0);
            $this->db->chiudiConnessione();
            return $esiste;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Recupera i dati di un utente tramite username
    public function getUtenteByUsername($username)
    {
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

    // Restituisce l'ID di un caso dato lo slug
    public function getCasoIdBySlug($slug, $soloApprovati = true)
    {
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
                return (int) $row['N_Caso'];
            }

            $this->db->chiudiConnessione();
            return null;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return null;
        }
    }

    // Restituisce i casi di una specifica categoria
    public function getCasiPerCategoria($tipologia, $limite = 10, $soloApprovati = true)
    {
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

    // Restituisce i casi più visualizzati
    public function getCasiPiuLetti($limite = 5, $soloApprovati = true)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $query = "SELECT N_Caso, Titolo, Slug, Descrizione, Immagine, Tipologia, Data, Luogo
                      FROM Caso";

            if ($soloApprovati) {
                $query .= " WHERE Approvato = 1";
            }

            $query .= " ORDER BY Visualizzazioni DESC LIMIT ?";

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

    // Restituisce casi casuali
    public function getCasiCasuali($limite = 4, $soloApprovati = true)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $query = "SELECT N_Caso, Titolo, Slug, Descrizione, Immagine, Tipologia, Data, Luogo
                      FROM Caso";

            if ($soloApprovati) {
                $query .= " WHERE Approvato = 1";
            }

            $query .= " ORDER BY RAND() LIMIT ?";

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

    // Restituisce i casi più recenti
    public function getCasiRecenti($limite = 5, $soloApprovati = true)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $query = "SELECT N_Caso, Titolo, Slug, Descrizione, Immagine, Tipologia, Data, Luogo
                      FROM Caso";

            if ($soloApprovati) {
                $query .= " WHERE Approvato = 1";
            }

            $query .= " ORDER BY N_Caso DESC LIMIT ?";

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

    // Restituisce un caso dato il suo ID
    public function getCasoById($nCaso, $soloApprovati = true)
    {
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

    // Restituisce le vittime di un caso
    public function getVittimeByCaso($id, $soloApprovati = true)
    {
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

    // Restituisce i colpevoli di un caso
    public function getColpevoliByCaso($id, $soloApprovati = true)
    {
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

    // Restituisce gli articoli collegati a un caso
    public function getArticoliByCaso($id)
    {
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

    // Cerca casi per parola chiave
    public function cercaCasi($query, $limite = 20, $soloApprovati = true)
    {
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

    // Cerca casi con filtri avanzati (testo, categoria, anno)
    public function cercaCasiConFiltri($filtri = [], $limite = 50, $soloApprovati = true)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $sql = "SELECT N_Caso, Titolo, Slug, Descrizione, Immagine, Tipologia, Data, Luogo
                    FROM Caso
                    WHERE 1=1";

            $params = [];
            $types = "";

            if ($soloApprovati) {
                $sql .= " AND Approvato = 1";
            }

            if (!empty($filtri['q'])) {
                $searchTerm = "%" . $filtri['q'] . "%";
                $sql .= " AND (Titolo LIKE ? OR Descrizione LIKE ? OR Luogo LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }

            if (!empty($filtri['categoria'])) {
                $sql .= " AND Tipologia = ?";
                $params[] = $filtri['categoria'];
                $types .= "s";
            }

            if (!empty($filtri['anno'])) {
                $sql .= " AND YEAR(Data) = ?";
                $params[] = (int) $filtri['anno'];
                $types .= "i";
            }

            $sql .= " ORDER BY Data DESC LIMIT ?";
            $params[] = $limite;
            $types .= "i";

            $result = $this->db->query($sql, $params, $types);

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

    // Restituisce tutte le categorie con il conteggio dei casi
    public function getCategorie($soloApprovati = true)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $sql = "SELECT Tipologia, COUNT(*) as conteggio
                    FROM Caso
                    WHERE Tipologia IS NOT NULL AND Tipologia != ''";

            if ($soloApprovati) {
                $sql .= " AND Approvato = 1";
            }

            $sql .= " GROUP BY Tipologia ORDER BY Tipologia ASC";

            $result = $this->db->query($sql, [], "");

            $categorie = [];
            if ($result && is_object($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $categorie[] = $row;
                }
            }

            $this->db->chiudiConnessione();
            return $categorie;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return [];
        }
    }

    // Conta i casi di una specifica categoria
    public function contaCasiPerCategoria($tipologia, $soloApprovati = true)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $sql = "SELECT COUNT(*) as totale FROM Caso WHERE Tipologia = ?";

            if ($soloApprovati) {
                $sql .= " AND Approvato = 1";
            }

            $result = $this->db->query($sql, [$tipologia], "s");

            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return (int) $row['totale'];
            }

            $this->db->chiudiConnessione();
            return 0;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return 0;
        }
    }

    // Restituisce i casi in attesa di approvazione
    public function getCasiNonApprovati($limite = 50)
    {
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

    // Approva un caso rendendolo visibile pubblicamente
    public function approvaCaso($nCaso)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            // 1. Recupera i dati necessari PRIMA di chiudere tutto
            $queryDati = "SELECT Titolo, Slug, Descrizione FROM Caso WHERE N_Caso = ?";
            $resDati = $this->db->query($queryDati, [$nCaso], "i");
            $datiCaso = mysqli_fetch_assoc($resDati);

            // 2. Esegui l'UPDATE
            $query = "UPDATE Caso SET Approvato = 1 WHERE N_Caso = ?";
            $result = $this->db->query($query, [$nCaso], "i");

            // 3. CHIUDI la connessione qui solo se hai finito tutto col DB
            $this->db->chiudiConnessione(); 

            if ($result && $datiCaso) {
                // 4. Ora chiama la newsletter (che aprirà la sua connessione separata)
                inviaNewsletterNuovoCaso($datiCaso['Titolo'], $datiCaso['Slug'], $datiCaso['Descrizione']);
                return ['success' => true, 'message' => 'Caso approvato e mail inviata'];
            }
            
            return ['success' => false, 'message' => 'Errore durante l\'approvazione'];

        } catch (Exception $e) {
            // Assicurati di chiudere sempre in caso di errore
            $this->db->chiudiConnessione();
            return ['success' => false, 'message' => 'Errore: ' . $e->getMessage()];
        }
    }

    // Aggiungi anche questo metodo per recuperare le email
    public function getIscrittiNewsletter() {
    try {
        if (!$this->db->apriConnessione()) return [];
        
        // Verifica sulla colonna Is_Newsletter della tabella Utente
        $query = "SELECT Email FROM Utente WHERE Is_Newsletter = 1";
        $result = $this->db->query($query, [], "");
        
        $emails = [];
        if ($result && is_object($result)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $emails[] = $row['Email'];
            }
        }
        $this->db->chiudiConnessione();
        return $emails;
    } catch (Exception $e) {
        return [];
    }
}

    // Rifiuta ed elimina un caso e tutti i dati collegati
    public function rifiutaCaso($nCaso)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $this->db->query("DELETE FROM Commento WHERE ID_Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Articolo WHERE Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Vittima WHERE Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Colpa WHERE Caso = ?", [$nCaso], "i");

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

    // Inserisce un nuovo commento su un caso
    public function inserisciCommento($emailUtente, $idCaso, $commento)
    {
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

    // Restituisce i commenti di un caso
    public function getCommentiCaso($idCaso, $limite = 50)
    {
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

    // Conta il numero di commenti di un caso
    public function contaCommentiCaso($idCaso)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $query = "SELECT COUNT(*) as totale FROM Commento WHERE ID_Caso = ?";
            $result = $this->db->query($query, [$idCaso], "i");

            if ($result && is_object($result) && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $this->db->chiudiConnessione();
                return (int) $row['totale'];
            }

            $this->db->chiudiConnessione();
            return 0;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return 0;
        }
    }

    // Elimina un commento (solo proprietario o admin)
    public function eliminaCommento($idCommento, $emailUtente, $isAdmin = false)
    {
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

    // Inserisce un nuovo caso nel database
    public function inserisciCaso($titolo, $data, $luogo, $descrizione, $storia, $tipologia = null, $immagine = null, $autoreEmail = null)
    {
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

    // Genera uno slug URL-friendly unico dal titolo
    public function generaSlugUnico($titolo)
    {
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

    // Verifica se uno slug esiste già
    private function slugEsiste($slug)
    {
        $query = "SELECT N_Caso FROM Caso WHERE Slug = ?";
        $result = $this->db->query($query, [$slug], "s");

        return ($result && is_object($result) && mysqli_num_rows($result) > 0);
    }

    // Restituisce lo slug di un caso dato l'ID
    public function getSlugById($casoId)
    {
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

    // Inserisce una nuova vittima collegata a un caso
    public function inserisciVittima($casoId, $nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $dataDecesso = null, $immagine = null)
    {
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

    // Inserisce un nuovo colpevole
    public function inserisciColpevole($nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $immagine = null)
    {
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

    // Collega un colpevole a un caso tramite la tabella Colpa
    public function collegaColpevoleACaso($colpevoleId, $casoId)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $query = "INSERT INTO Colpa (Colpevole, Caso) VALUES (?, ?)";
            $result = $this->db->query($query, [$colpevoleId, $casoId], "ii");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Inserisce un nuovo articolo/fonte collegato a un caso
    public function inserisciArticolo($casoId, $titolo, $data = null, $link = '')
    {
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

    // Incrementa il contatore visualizzazioni di un caso
    public function incrementaVisualizzazioni($casoId)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "UPDATE caso SET Visualizzazioni = Visualizzazioni + 1 WHERE N_Caso = ? AND Approvato = 1";
            $result = $this->db->query($query, [$casoId], "i");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Aggiorna i dati di un caso esistente
    public function aggiornaCaso($nCaso, $titolo, $data, $luogo, $descrizione, $storia, $tipologia = null, $riApprova = false, $immagine = null)
    {
        try {
            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            if (empty($titolo) || empty($data) || empty($luogo) || empty($descrizione) || empty($storia)) {
                $this->db->chiudiConnessione();
                return ['success' => false, 'message' => 'Tutti i campi obbligatori devono essere compilati'];
            }

            if ($immagine !== null) {
                $immagineValue = ($immagine === '') ? null : $immagine;

                if ($riApprova) {
                    $query = "UPDATE Caso SET Titolo = ?, Data = ?, Luogo = ?, Descrizione = ?, Storia = ?, Tipologia = ?, Immagine = ?, Approvato = 0 WHERE N_Caso = ?";
                } else {
                    $query = "UPDATE Caso SET Titolo = ?, Data = ?, Luogo = ?, Descrizione = ?, Storia = ?, Tipologia = ?, Immagine = ? WHERE N_Caso = ?";
                }
                $params = [$titolo, $data, $luogo, $descrizione, $storia, $tipologia, $immagineValue, $nCaso];
                $types = "sssssssi";
            } else {
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

    // Aggiorna solo l'immagine di un caso
    public function aggiornaImmagineCaso($nCaso, $immagine)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "UPDATE Caso SET Immagine = ? WHERE N_Caso = ?";
            $result = $this->db->query($query, [$immagine, $nCaso], "si");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Verifica se un utente può modificare un caso
    public function puoModificareCaso($nCaso, $emailUtente, $isAdmin = false)
    {
        if ($isAdmin) {
            return true;
        }

        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

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

    // Elimina un caso e tutti i dati collegati
    public function eliminaCaso($nCaso, $emailUtente, $isAdmin = false)
    {
        try {
            if (!$this->puoModificareCaso($nCaso, $emailUtente, $isAdmin)) {
                return ['success' => false, 'message' => 'Non hai i permessi per eliminare questo caso'];
            }

            if (!$this->db->apriConnessione()) {
                throw new Exception("Impossibile connettersi al database");
            }

            $this->db->query("DELETE FROM Commento WHERE ID_Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Articolo WHERE Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Vittima WHERE Caso = ?", [$nCaso], "i");
            $this->db->query("DELETE FROM Colpa WHERE Caso = ?", [$nCaso], "i");

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

    // Aggiorna i dati di una vittima
    public function aggiornaVittima($idVittima, $nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $dataDecesso = null, $immagine = null)
    {
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

            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Aggiorna solo l'immagine di una vittima
    public function aggiornaImmagineVittima($idVittima, $immagine)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "UPDATE Vittima SET Immagine = ? WHERE ID_Vittima = ?";
            $result = $this->db->query($query, [$immagine, $idVittima], "si");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Elimina una vittima
    public function eliminaVittima($idVittima)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "DELETE FROM Vittima WHERE ID_Vittima = ?";
            $result = $this->db->query($query, [$idVittima], "i");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Elimina tutte le vittime di un caso
    public function eliminaVittimeByCaso($casoId)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "DELETE FROM Vittima WHERE Caso = ?";
            $result = $this->db->query($query, [$casoId], "i");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Aggiorna i dati di un colpevole
    public function aggiornaColpevole($idColpevole, $nome, $cognome, $luogoNascita = 'N/A', $dataNascita = null, $immagine = null)
    {
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

            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Aggiorna solo l'immagine di un colpevole
    public function aggiornaImmagineColpevole($idColpevole, $immagine)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "UPDATE Colpevole SET Immagine = ? WHERE ID_Colpevole = ?";
            $result = $this->db->query($query, [$immagine, $idColpevole], "si");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Rimuove il collegamento tra un colpevole e un caso
    public function rimuoviColpevoleDaCaso($idColpevole, $casoId)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "DELETE FROM Colpa WHERE Colpevole = ? AND Caso = ?";
            $result = $this->db->query($query, [$idColpevole, $casoId], "ii");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Rimuove tutti i colpevoli da un caso ed elimina quelli orfani
    public function rimuoviColpevoliByCaso($casoId)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $querySelect = "SELECT Colpevole FROM Colpa WHERE Caso = ?";
            $colpevoliIds = $this->db->query($querySelect, [$casoId], "i");

            $idsToCheck = [];
            if ($colpevoliIds && $colpevoliIds->num_rows > 0) {
                while ($row = $colpevoliIds->fetch_assoc()) {
                    $idsToCheck[] = $row['Colpevole'];
                }
            }

            $queryDelete = "DELETE FROM Colpa WHERE Caso = ?";
            $result = $this->db->query($queryDelete, [$casoId], "i");

            foreach ($idsToCheck as $colpevoleId) {
                $queryCheck = "SELECT COUNT(*) as cnt FROM Colpa WHERE Colpevole = ?";
                $checkResult = $this->db->query($queryCheck, [$colpevoleId], "i");

                if ($checkResult && $checkResult->fetch_assoc()['cnt'] == 0) {
                    $queryDeleteColpevole = "DELETE FROM Colpevole WHERE ID_Colpevole = ?";
                    $this->db->query($queryDeleteColpevole, [$colpevoleId], "i");
                }
            }

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Aggiorna i dati di un articolo/fonte
    public function aggiornaArticolo($idArticolo, $titolo, $data = null, $link = '')
    {
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

            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Elimina un articolo/fonte
    public function eliminaArticolo($idArticolo)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "DELETE FROM Articolo WHERE ID_Articolo = ?";
            $result = $this->db->query($query, [$idArticolo], "i");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Elimina tutti gli articoli/fonti di un caso
    public function eliminaArticoliByCaso($casoId)
    {
        try {
            if (!$this->db->apriConnessione()) {
                return false;
            }

            $query = "DELETE FROM Articolo WHERE Caso = ?";
            $result = $this->db->query($query, [$casoId], "i");

            $this->db->chiudiConnessione();
            return (bool) $result;

        } catch (Exception $e) {
            $this->db->chiudiConnessione();
            return false;
        }
    }

    // Restituisce l'email dell'autore di un caso
    public function getAutoreCaso($nCaso)
    {
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
