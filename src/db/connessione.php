<?php
class ConnessioneDB
{
    private $host;
    private $username;
    private $password;
    private $nome_db;
    private $connessione;

    public function __construct()
    {
        // Carica le variabili dal file .env
        $envPath = __DIR__ . '/../../.env';

        if (file_exists($envPath)) {
            $env = parse_ini_file($envPath);

            $this->host = $env['DB_HOST'];
            $this->username = $env['DB_USER'];
            $this->password = $env['DB_PASSWORD'];
            $this->nome_db = $env['DB_NAME'];
        } else {
            error_log("ATTENZIONE: File .env non trovato");
        }
    }

    /**
     * Apre la connessione al database
     * @return bool True se la connessione ha successo, False altrimenti
     */
    public function apriConnessione()
    {
        try {
            $this->connessione = mysqli_connect(
                $this->host,
                $this->username,
                $this->password,
                $this->nome_db
            );

            if (!$this->connessione) {
                throw new mysqli_sql_exception("Connessione a MySQL fallita: " . mysqli_connect_error());
            }

            // Imposta charset UTF-8 per gestire correttamente i caratteri accentati
            mysqli_set_charset($this->connessione, "utf8mb4");

            return true;
        } catch (mysqli_sql_exception $e) {
            error_log("Errore connessione DB: " . $e->getMessage());
            return false;
        }
    }

    public function chiudiConnessione()
    {
        try {
            if ($this->connessione) {
                mysqli_close($this->connessione);
                $this->connessione = null;
                return true;
            } else {
                throw new Exception("Connessione non stabilita");
            }
        } catch (Exception $e) {
            error_log("Errore durante la chiusura della connessione: " . $e->getMessage());
            return false;
        }
    }

    public function getConnessione()
    {
        return $this->connessione;
    }

    /**
     * Restituisce l'ID dell'ultimo record inserito
     * @return int|null
     */
    public function getLastInsertId()
    {
        if ($this->connessione) {
            return mysqli_insert_id($this->connessione);
        }
        return null;
    }

    /**
     * Esegue una query preparata
     * @param string $query Query SQL con placeholder (?)
     * @param array $params Array di parametri
     * @param string $types Stringa dei tipi (es. "ssi" per string, string, int)
     * @return mysqli_result|bool Result set per SELECT, true per INSERT/UPDATE/DELETE, false per errori
     */
    public function query($query, $params = [], $types = "")
    {
        try {
            if (!$this->connessione) {
                throw new Exception("Connessione non aperta");
            }

            $stmt = mysqli_prepare($this->connessione, $query);

            if (!$stmt) {
                throw new Exception("Errore preparazione query: " . mysqli_error($this->connessione));
            }

            // Bind dei parametri se presenti
            if (!empty($params) && !empty($types)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }

            $executed = mysqli_stmt_execute($stmt);

            if (!$executed) {
                $error = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
                throw new Exception("Errore esecuzione query: " . $error);
            }

            $result = mysqli_stmt_get_result($stmt);

            if ($result === false) {
                mysqli_stmt_close($stmt);
                return true;
            }

            mysqli_stmt_close($stmt);
            return $result;

        } catch (Exception $e) {
            error_log("Errore query DB: " . $e->getMessage());
            return false;
        }
    }
}
?>