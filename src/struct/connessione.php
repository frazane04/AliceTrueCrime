<?php
    class ConnessioneDB {
        private $host;
        private $username;
        private $password;
        private $nome_db;
        private $connessione;

        public function apriConnessione(){
            try{
                $envPath = __DIR__ . '/../../.env';

            if (file_exists($envPath)) {
                $env = parse_ini_file($envPath);
            } else {
                // Fallback se non usi il .env (o per debug)
                die("Errore: File .env non trovato!");
            }

            
            // Recupera i valori dal .env o usa valori di default
            $this->host = $env['DB_HOST'];
            $this->username = $env['DB_USER'];
            $this->password = $env['DB_PASSWORD'];
            $this->nome_db = $env['DB_NAME'];

            $this->$connessione=mysqli_connect(
                $this->host,
                $this->username,
                $this->password,
                $this->nome_db
            );

            if (!$this->connessione) {
                    throw new \mysqli_sql_exception("Connessione a MySQL fallita: " . mysqli_connect_error());
                }
                return true;
            } catch (\mysqli_sql_exception $e) {
                echo "Connessione fallita: " . $e->getMessage();
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


    }




?>