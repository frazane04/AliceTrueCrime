<?php
    class ConnessioneDB {
        private $host;
        private $username;
        private $password;
        private $nome_db;
        private $connessione;

         public function __construct() {
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
        public function apriConnessione() {
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



        public function chiudiConnessione(){
            try {
                if ($this->connessione) {
                    mysqli_close($this->connessione);
                    $this->connessione = null;
                    return true;
                } else {
                    throw new \Exception("Connessione non stabilita");
                }
            } catch (\Exception $e) {
                echo "Errore durante la chiusura della connessione: " . $e->getMessage();
                return false;
            }
        }


        public function getConnessione() {
            return $this->connessione;
        }

        public function query($query, $params = [], $types = "") {
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

                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                // Se la query non ritorna risultati (INSERT, UPDATE, DELETE)
                if ($result === false) {
                    $result = $stmt;
                }

                return $result;
            } catch (Exception $e) {
                error_log("Errore query DB: " . $e->getMessage());
                return false;
            }
        }


    }

?>