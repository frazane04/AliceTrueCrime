<?php
class ConnessioneDB
{
    private $host;
    private $username;
    private $password;
    private $nome_db;
    private $connessione;

    // Carica le credenziali dal file .env
    public function __construct()
    {
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

    // Apre la connessione al database
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

            mysqli_set_charset($this->connessione, "utf8mb4");
            return true;
        } catch (mysqli_sql_exception $e) {
            error_log("Errore connessione DB: " . $e->getMessage());
            return false;
        }
    }

    // Chiude la connessione al database
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

    // Restituisce l'oggetto connessione
    public function getConnessione()
    {
        return $this->connessione;
    }

    // Restituisce l'ID dell'ultimo record inserito
    public function getLastInsertId()
    {
        if ($this->connessione) {
            return mysqli_insert_id($this->connessione);
        }
        return null;
    }

    // Esegue una query preparata con parametri
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
