<?php
/**
 * ImageHandler - Gestione upload e ridimensionamento immagini
 * 
 * Gestisce:
 * - Upload sicuro di immagini (jpg, png, webp)
 * - Ridimensionamento mantenendo aspect ratio
 * - Organizzazione in sottocartelle (caso, vittime, colpevoli)
 * - Generazione nomi file basati su slug
 */

class ImageHandler
{

    // Configurazione
    private const DIMENSIONI = [
        'caso' => ['width' => 1200, 'height' => 800],      // Banner/header del caso
        'vittime' => ['width' => 400, 'height' => 500],    // Ritratto verticale
        'colpevoli' => ['width' => 400, 'height' => 500]   // Ritratto verticale
    ];

    // Configurazione upload
    private const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private $basePath;

    public function __construct()
    {
        $this->basePath = $this->getProjectRoot() . '/assets/img/casi';
        $this->assicuraCartelle();
    }

    private function getProjectRoot()
    {
        return dirname(dirname(__DIR__));
    }

    /**
     * Crea le cartelle necessarie se non esistono
     */
    private function assicuraCartelle()
    {
        $cartelle = ['caso', 'vittime', 'colpevoli'];

        foreach ($cartelle as $cartella) {
            $path = $this->basePath . '/' . $cartella;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Carica e processa un'immagine
     * 
     * @param array $file - $_FILES['campo']
     * @param string $tipo - 'caso', 'vittime', 'colpevoli'
     * @param string $slug - Slug per il nome file
     * @return array - ['success' => bool, 'path' => string|null, 'message' => string]
     */
    public function caricaImmagine($file, $tipo, $slug)
    {
        // Verifica che il file sia stato caricato
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'path' => null, 'message' => 'Nessuna immagine caricata'];
        }

        // Verifica errori upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'message' => $this->getUploadErrorMessage($file['error'])];
        }

        // Validazione tipo
        if (!in_array($tipo, ['caso', 'vittime', 'colpevoli'])) {
            return ['success' => false, 'path' => null, 'message' => 'Tipo immagine non valido'];
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return ['success' => false, 'path' => null, 'message' => 'File troppo grande. Massimo 2MB consentito'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            return ['success' => false, 'path' => null, 'message' => 'Formato non supportato. Usa JPG, PNG o WebP'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'path' => null, 'message' => 'Estensione file non valida'];
        }

        $slugSanitized = $this->sanitizzaSlug($slug);
        // Sempre WebP per ottimizzazione
        $nomeFile = $slugSanitized . '.webp';
        $pathCompleto = $this->basePath . '/' . $tipo . '/' . $nomeFile;

        if (file_exists($pathCompleto)) {
            $nomeFile = $slugSanitized . '-' . time() . '.webp';
            $pathCompleto = $this->basePath . '/' . $tipo . '/' . $nomeFile;
        }

        $risultato = $this->processaImmagine($file['tmp_name'], $pathCompleto, $tipo, $mimeType);

        if ($risultato) {
            $pathRelativo = 'assets/img/casi/' . $tipo . '/' . $nomeFile;
            return ['success' => true, 'path' => $pathRelativo, 'message' => 'Immagine caricata con successo'];
        } else {
            return ['success' => false, 'path' => null, 'message' => 'Errore durante l\'elaborazione dell\'immagine'];
        }
    }

    /**
     * Processa e ridimensiona l'immagine mantenendo l'aspect ratio
     */
    private function processaImmagine($tmpPath, $destPath, $tipo, $mimeType)
    {
        // Carica immagine sorgente
        switch ($mimeType) {
            case 'image/jpeg':
                $imgSrc = imagecreatefromjpeg($tmpPath);
                break;
            case 'image/png':
                $imgSrc = imagecreatefrompng($tmpPath);
                break;
            case 'image/webp':
                $imgSrc = imagecreatefromwebp($tmpPath);
                break;
            default:
                return false;
        }

        if (!$imgSrc) {
            return false;
        }

        // Dimensioni originali
        $srcWidth = imagesx($imgSrc);
        $srcHeight = imagesy($imgSrc);

        // Dimensioni target
        $maxWidth = self::DIMENSIONI[$tipo]['width'];
        $maxHeight = self::DIMENSIONI[$tipo]['height'];

        // Calcola nuove dimensioni mantenendo aspect ratio
        $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight);

        // Se l'immagine è più piccola del target, non ingrandire
        if ($ratio >= 1) {
            $newWidth = $srcWidth;
            $newHeight = $srcHeight;
        } else {
            $newWidth = (int) ($srcWidth * $ratio);
            $newHeight = (int) ($srcHeight * $ratio);
        }

        // Crea nuova immagine
        $imgDst = imagecreatetruecolor($newWidth, $newHeight);

        // Preserva trasparenza per PNG e WebP
        if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
            imagealphablending($imgDst, false);
            imagesavealpha($imgDst, true);
            $transparent = imagecolorallocatealpha($imgDst, 0, 0, 0, 127);
            imagefilledrectangle($imgDst, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Ridimensiona con alta qualità
        imagecopyresampled(
            $imgDst,
            $imgSrc,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $srcWidth,
            $srcHeight
        );

        // Salva sempre in WebP compresso (qualità 80%)
        $success = imagewebp($imgDst, $destPath, 80);

        // Libera memoria
        imagedestroy($imgSrc);
        imagedestroy($imgDst);

        return $success;
    }

    /**
     * Elimina un'immagine dal filesystem
     * 
     * @param string $pathRelativo - Percorso relativo salvato nel DB
     * @return bool
     */
    public function eliminaImmagine($pathRelativo)
    {
        if (empty($pathRelativo)) {
            return true;
        }

        $pathCompleto = $this->getProjectRoot() . '/' . $pathRelativo;

        if (file_exists($pathCompleto)) {
            return unlink($pathCompleto);
        }

        return true;
    }

    /**
     * Sanitizza lo slug per uso come nome file
     */
    private function sanitizzaSlug($slug)
    {
        // Rimuovi caratteri non sicuri
        $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($slug));
        // Limita lunghezza
        $slug = substr($slug, 0, 100);
        // Se vuoto, genera uno casuale
        if (empty($slug)) {
            $slug = 'img-' . uniqid();
        }
        return $slug;
    }

    /**
     * Genera slug da nome e cognome (per vittime/colpevoli)
     */
    public function generaSlugPersona($nome, $cognome, $id = null)
    {
        $slug = strtolower($nome . '-' . $cognome);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        if ($id) {
            $slug .= '-' . $id;
        }

        return $slug;
    }

    /**
     * Messaggi errore upload
     */
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File troppo grande';
            case UPLOAD_ERR_PARTIAL:
                return 'Upload interrotto. Riprova';
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
                return 'Errore server. Contatta l\'amministratore';
            default:
                return 'Errore sconosciuto durante l\'upload';
        }
    }

    /**
     * Verifica se un percorso immagine è valido e il file esiste
     */
    public function immagineEsiste($pathRelativo)
    {
        if (empty($pathRelativo)) {
            return false;
        }

        $pathCompleto = $this->getProjectRoot() . '/' . $pathRelativo;
        return file_exists($pathCompleto);
    }

    /**
     * Ottieni URL pubblico dell'immagine (con prefix)
     */
    public function getUrlImmagine($pathRelativo, $prefix = '')
    {
        if (empty($pathRelativo)) {
            return null;
        }

        return $prefix . '/' . $pathRelativo;
    }

    /**
     * Genera attributo alt accessibile
     */
    public static function generaAlt($tipo, $dati)
    {
        switch ($tipo) {
            case 'caso':
                return 'Immagine del caso: ' . htmlspecialchars($dati['titolo'] ?? 'Caso senza titolo');
            case 'vittima':
                $nome = htmlspecialchars($dati['nome'] ?? '');
                $cognome = htmlspecialchars($dati['cognome'] ?? '');
                return 'Foto della vittima ' . trim($nome . ' ' . $cognome);
            case 'colpevole':
                $nome = htmlspecialchars($dati['nome'] ?? '');
                $cognome = htmlspecialchars($dati['cognome'] ?? '');
                return 'Foto del colpevole ' . trim($nome . ' ' . $cognome);
            default:
                return 'Immagine';
        }
    }
}
?>