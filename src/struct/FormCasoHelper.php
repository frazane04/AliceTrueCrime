<?php
/**
 * FormCasoHelper.php - Utility condivise per form segnala/modifica caso
 * Gestisce generazione HTML, parsing POST e upload immagini per vittime/colpevoli/articoli
 */

require_once __DIR__ . '/ImageHandler.php';

class FormCasoHelper {

    private ImageHandler $imageHandler;
    private string $prefix;

    // Configurazione tipologie caso
    public const TIPOLOGIE = [
        'Serial killer',
        'Casi mediatici italiani',
        'Amore tossico',
        'Celebrity',
        'Cold case',
        'Altro'
    ];

    public function __construct(?ImageHandler $imageHandler = null) {
        $this->imageHandler = $imageHandler ?? new ImageHandler();
        $this->prefix = function_exists('getPrefix') ? getPrefix() : '';
    }

    // ========================================
    // PARSING DATI POST
    // ========================================

    /**
     * Estrae array vittime dai dati POST
     */
    public function parseVittimeFromPost(array $post): array {
        $nomi = $post['vittima_nome'] ?? [];
        $cognomi = $post['vittima_cognome'] ?? [];
        $luoghiNascita = $post['vittima_luogo_nascita'] ?? [];
        $dateNascita = $post['vittima_data_nascita'] ?? [];
        $dateDecesso = $post['vittima_data_decesso'] ?? [];
        $ids = $post['vittima_id'] ?? [];
        $immaginiEsistenti = $post['vittima_immagine_esistente'] ?? [];

        $vittime = [];
        for ($i = 0; $i < count($nomi); $i++) {
            if (!empty($nomi[$i]) && !empty($cognomi[$i])) {
                $vittime[] = [
                    'id' => isset($ids[$i]) ? intval($ids[$i]) : 0,
                    'nome' => trim($nomi[$i]),
                    'cognome' => trim($cognomi[$i]),
                    'luogo_nascita' => !empty($luoghiNascita[$i]) ? trim($luoghiNascita[$i]) : 'N/A',
                    'data_nascita' => !empty($dateNascita[$i]) ? $dateNascita[$i] : null,
                    'data_decesso' => !empty($dateDecesso[$i]) ? $dateDecesso[$i] : null,
                    'immagine_esistente' => $immaginiEsistenti[$i] ?? '',
                    'file_index' => $i
                ];
            }
        }
        return $vittime;
    }

    /**
     * Estrae array colpevoli dai dati POST
     */
    public function parseColpevoliFromPost(array $post): array {
        $nomi = $post['colpevole_nome'] ?? [];
        $cognomi = $post['colpevole_cognome'] ?? [];
        $luoghiNascita = $post['colpevole_luogo_nascita'] ?? [];
        $dateNascita = $post['colpevole_data_nascita'] ?? [];
        $ids = $post['colpevole_id'] ?? [];
        $immaginiEsistenti = $post['colpevole_immagine_esistente'] ?? [];

        $colpevoli = [];
        for ($i = 0; $i < count($nomi); $i++) {
            if (!empty($nomi[$i]) && !empty($cognomi[$i])) {
                $colpevoli[] = [
                    'id' => isset($ids[$i]) ? intval($ids[$i]) : 0,
                    'nome' => trim($nomi[$i]),
                    'cognome' => trim($cognomi[$i]),
                    'luogo_nascita' => !empty($luoghiNascita[$i]) ? trim($luoghiNascita[$i]) : 'N/A',
                    'data_nascita' => !empty($dateNascita[$i]) ? $dateNascita[$i] : null,
                    'immagine_esistente' => $immaginiEsistenti[$i] ?? '',
                    'file_index' => $i
                ];
            }
        }
        return $colpevoli;
    }

    /**
     * Estrae array articoli dai dati POST
     */
    public function parseArticoliFromPost(array $post): array {
        $titoli = $post['articolo_titolo'] ?? [];
        $date = $post['articolo_data'] ?? [];
        $links = $post['articolo_link'] ?? [];
        $ids = $post['articolo_id'] ?? [];

        $articoli = [];
        for ($i = 0; $i < count($titoli); $i++) {
            if (!empty($titoli[$i]) || !empty($links[$i])) {
                $articoli[] = [
                    'id' => isset($ids[$i]) ? intval($ids[$i]) : 0,
                    'titolo' => !empty($titoli[$i]) ? trim($titoli[$i]) : 'Fonte senza titolo',
                    'data' => !empty($date[$i]) ? $date[$i] : null,
                    'link' => !empty($links[$i]) ? trim($links[$i]) : ''
                ];
            }
        }
        return $articoli;
    }

    // ========================================
    // GESTIONE IMMAGINI
    // ========================================

    /**
     * Gestisce upload immagine caso (nuovo o sostituzione)
     *
     * @param array $files $_FILES array
     * @param string $slug Slug del caso
     * @param string|null $immagineEsistente Path immagine attuale (per modifica)
     * @return array ['path' => string|null, 'error' => string|null, 'aggiorna' => bool]
     */
    public function gestisciImmagineCaso(array $files, string $slug, ?string $immagineEsistente = null): array {
        $result = ['path' => null, 'error' => null, 'aggiorna' => false];

        // Nuova immagine caricata?
        if (isset($files['immagine_caso']) &&
            $files['immagine_caso']['error'] === UPLOAD_ERR_OK) {

            // Elimina vecchia immagine se presente
            if (!empty($immagineEsistente)) {
                $this->imageHandler->eliminaImmagine($immagineEsistente);
            }

            $uploadResult = $this->imageHandler->caricaImmagine($files['immagine_caso'], 'caso', $slug);

            if ($uploadResult['success'] && $uploadResult['path']) {
                $result['path'] = $uploadResult['path'];
                $result['aggiorna'] = true;
            } elseif (!$uploadResult['success'] && $uploadResult['message'] !== 'Nessuna immagine caricata') {
                $result['error'] = $uploadResult['message'];
            }
        }

        return $result;
    }

    /**
     * Gestisce upload immagine per una persona (vittima o colpevole)
     *
     * @param array $files $_FILES array
     * @param string $tipo 'vittima' o 'colpevole'
     * @param int $index Indice nell'array
     * @param string $nome Nome persona
     * @param string $cognome Cognome persona
     * @param string|null $immagineEsistente Path immagine attuale
     * @return string|null Path nuova immagine o immagine esistente
     */
    public function gestisciImmaginePersona(
        array $files,
        string $tipo,
        int $index,
        string $nome,
        string $cognome,
        ?string $immagineEsistente = null
    ): ?string {
        $fileKey = $tipo === 'vittima' ? 'vittima_immagine' : 'colpevole_immagine';
        $tipoCartella = $tipo === 'vittima' ? 'vittime' : 'colpevoli';

        // Verifica se c'è un nuovo file caricato
        if (isset($files[$fileKey]['name'][$index]) &&
            !empty($files[$fileKey]['name'][$index]) &&
            isset($files[$fileKey]['error'][$index]) &&
            $files[$fileKey]['error'][$index] === UPLOAD_ERR_OK) {

            // Costruisci array file singolo
            $file = [
                'name' => $files[$fileKey]['name'][$index],
                'type' => $files[$fileKey]['type'][$index],
                'tmp_name' => $files[$fileKey]['tmp_name'][$index],
                'error' => $files[$fileKey]['error'][$index],
                'size' => $files[$fileKey]['size'][$index]
            ];

            // Elimina vecchia immagine se presente
            if (!empty($immagineEsistente)) {
                $this->imageHandler->eliminaImmagine($immagineEsistente);
            }

            $slug = $this->imageHandler->generaSlugPersona($nome, $cognome);
            $result = $this->imageHandler->caricaImmagine($file, $tipoCartella, $slug);

            if ($result['success'] && $result['path']) {
                return $result['path'];
            }
        }

        // Nessun nuovo upload: ritorna immagine esistente (o null)
        return !empty($immagineEsistente) ? $immagineEsistente : null;
    }

    /**
     * Elimina tutte le immagini associate a un caso (caso + vittime + colpevoli)
     */
    public function eliminaTutteImmaginiCaso(array $caso, array $vittime, array $colpevoli): void {
        if (!empty($caso['Immagine'])) {
            $this->imageHandler->eliminaImmagine($caso['Immagine']);
        }
        foreach ($vittime as $v) {
            if (!empty($v['Immagine'])) {
                $this->imageHandler->eliminaImmagine($v['Immagine']);
            }
        }
        foreach ($colpevoli as $c) {
            if (!empty($c['Immagine'])) {
                $this->imageHandler->eliminaImmagine($c['Immagine']);
            }
        }
    }

    /**
     * Pulisce immagini orfane (non più associate) per modifica caso
     *
     * @param array $personeNuove Array persone dal form (con 'immagine_esistente')
     * @param array $personeVecchie Array persone dal DB (con 'Immagine')
     */
    public function pulisciImmaginiOrfane(array $personeNuove, array $personeVecchie): void {
        $immaginiDaPreservare = [];
        foreach ($personeNuove as $p) {
            if (!empty($p['immagine_esistente'])) {
                $immaginiDaPreservare[] = $p['immagine_esistente'];
            }
        }

        foreach ($personeVecchie as $pOld) {
            if (!empty($pOld['Immagine']) && !in_array($pOld['Immagine'], $immaginiDaPreservare)) {
                $this->imageHandler->eliminaImmagine($pOld['Immagine']);
            }
        }
    }

    // ========================================
    // GENERAZIONE HTML
    // ========================================

    /**
     * Genera HTML per entry vittima (per modifica caso)
     */
    public function generaHtmlVittima(?array $dati = null, int $index = 0): string {
        $id = $dati['ID_Vittima'] ?? 0;
        $nome = htmlspecialchars($dati['Nome'] ?? '');
        $cognome = htmlspecialchars($dati['Cognome'] ?? '');
        $luogo = htmlspecialchars($dati['LuogoNascita'] ?? '');
        $dataNascita = $dati['DataNascita'] ?? '';
        $dataDecesso = $dati['DataDecesso'] ?? '';
        $immagine = $dati['Immagine'] ?? '';

        $hiddenImmagine = '<input type="hidden" name="vittima_immagine_esistente[]" id="vittima-img-hidden-' . $index . '" value="' . htmlspecialchars($immagine) . '">';
        $anteprimaImg = $this->generaAnteprimaImmagine('vittima', $index, $immagine, $nome, $cognome);

        return renderComponent('vittima-form-entry', [
            'ID' => $id,
            'INDEX' => $index,
            'HIDDEN_IMMAGINE' => $hiddenImmagine,
            'NOME' => $nome,
            'COGNOME' => $cognome,
            'LUOGO' => $luogo,
            'DATA_NASCITA' => $dataNascita,
            'DATA_DECESSO' => $dataDecesso,
            'ANTEPRIMA_IMMAGINE' => $anteprimaImg
        ]);
    }

    /**
     * Genera HTML per entry colpevole (per modifica caso)
     */
    public function generaHtmlColpevole(?array $dati = null, int $index = 0): string {
        $id = $dati['ID_Colpevole'] ?? 0;
        $nome = htmlspecialchars($dati['Nome'] ?? '');
        $cognome = htmlspecialchars($dati['Cognome'] ?? '');
        $luogo = htmlspecialchars($dati['LuogoNascita'] ?? '');
        $dataNascita = $dati['DataNascita'] ?? '';
        $immagine = $dati['Immagine'] ?? '';

        $hiddenImmagine = '<input type="hidden" name="colpevole_immagine_esistente[]" id="colpevole-img-hidden-' . $index . '" value="' . htmlspecialchars($immagine) . '">';
        $anteprimaImg = $this->generaAnteprimaImmagine('colpevole', $index, $immagine, $nome, $cognome);

        return renderComponent('colpevole-form-entry', [
            'ID' => $id,
            'INDEX' => $index,
            'HIDDEN_IMMAGINE' => $hiddenImmagine,
            'NOME' => $nome,
            'COGNOME' => $cognome,
            'LUOGO' => $luogo,
            'DATA_NASCITA' => $dataNascita,
            'ANTEPRIMA_IMMAGINE' => $anteprimaImg
        ]);
    }

    /**
     * Genera HTML per entry articolo
     */
    public function generaHtmlArticolo(?array $dati = null): string {
        return renderComponent('articolo-form-entry', [
            'ID' => $dati['ID_Articolo'] ?? 0,
            'TITOLO' => htmlspecialchars($dati['Titolo'] ?? ''),
            'DATA' => $dati['Data'] ?? '',
            'LINK' => htmlspecialchars($dati['Link'] ?? '')
        ]);
    }

    /**
     * Genera HTML anteprima immagine esistente con bottoni rimuovi/annulla
     */
    private function generaAnteprimaImmagine(
        string $tipo,
        int $index,
        string $immagine,
        string $nome = '',
        string $cognome = ''
    ): string {
        if (empty($immagine) || !$this->imageHandler->immagineEsiste($immagine)) {
            return '';
        }

        $alt = ImageHandler::generaAlt($tipo, ['nome' => $nome, 'cognome' => $cognome]);
        $imgPath = htmlspecialchars($immagine);
        $imgPathQuoted = htmlspecialchars($immagine, ENT_QUOTES);

        return <<<HTML
        <div class="image-preview-existing" id="{$tipo}-img-preview-{$index}">
            <img src="{$this->prefix}/{$imgPath}" alt="{$alt}" class="preview-image preview-image-small">
            <span class="img-label">Immagine attuale</span>
            <button type="button" class="btn-remove-img" data-img-action="remove" data-img-type="{$tipo}" data-img-index="{$index}" data-img-path="{$imgPathQuoted}">✕ Rimuovi</button>
        </div>
        <div class="image-removed-notice hidden" id="{$tipo}-img-removed-{$index}">
            <span>🗑️ Immagine sarà rimossa al salvataggio</span>
            <button type="button" class="btn-undo-remove" data-img-action="undo" data-img-type="{$tipo}" data-img-index="{$index}" data-img-path="{$imgPathQuoted}">↩ Annulla</button>
        </div>
HTML;
    }

    /**
     * Genera HTML per anteprima immagine caso
     */
    public function generaAnteprimaImmagineCaso(array $caso): array {
        $immagine = $caso['Immagine'] ?? '';

        $hiddenInput = '<input type="hidden" name="caso_immagine_esistente" id="caso-img-hidden" value="' . htmlspecialchars($immagine) . '">';

        if (empty($immagine) || !$this->imageHandler->immagineEsiste($immagine)) {
            return ['hidden' => $hiddenInput, 'anteprima' => ''];
        }

        $alt = ImageHandler::generaAlt('caso', ['titolo' => $caso['Titolo'] ?? '']);
        $imgPath = htmlspecialchars($immagine);
        $imgPathQuoted = htmlspecialchars($immagine, ENT_QUOTES);

        $anteprima = <<<HTML
        <div class="image-preview-existing" id="caso-img-preview">
            <img src="{$this->prefix}/{$imgPath}" alt="{$alt}" class="preview-image">
            <span class="img-label">Immagine attuale</span>
            <button type="button" class="btn-remove-img" data-img-action="remove" data-img-type="caso" data-img-index="0" data-img-path="{$imgPathQuoted}">✕ Rimuovi</button>
        </div>
        <div class="image-removed-notice hidden" id="caso-img-removed">
            <span>🗑️ Immagine sarà rimossa al salvataggio</span>
            <button type="button" class="btn-undo-remove" data-img-action="undo" data-img-type="caso" data-img-index="0" data-img-path="{$imgPathQuoted}">↩ Annulla</button>
        </div>
HTML;

        return ['hidden' => $hiddenInput, 'anteprima' => $anteprima];
    }

    /**
     * Genera opzioni select per tipologia caso
     */
    public function generaOpzioniTipologia(?string $tipologiaSelezionata = null): string {
        $html = '<option value="">-- Seleziona categoria --</option>';
        foreach (self::TIPOLOGIE as $t) {
            $selected = ($tipologiaSelezionata === $t) ? 'selected' : '';
            $html .= "<option value=\"$t\" $selected>$t</option>";
        }
        return $html;
    }

    /**
     * Genera HTML lista vittime da array DB
     */
    public function generaHtmlListaVittime(array $vittime): string {
        $html = '';
        $index = 0;
        foreach ($vittime as $v) {
            $html .= $this->generaHtmlVittima($v, $index);
            $index++;
        }
        // Se vuoto, genera entry vuota
        if (empty($vittime)) {
            $html = $this->generaHtmlVittima(null, 0);
        }
        return $html;
    }

    /**
     * Genera HTML lista colpevoli da array DB
     */
    public function generaHtmlListaColpevoli(array $colpevoli): string {
        $html = '';
        $index = 0;
        foreach ($colpevoli as $c) {
            $html .= $this->generaHtmlColpevole($c, $index);
            $index++;
        }
        if (empty($colpevoli)) {
            $html = $this->generaHtmlColpevole(null, 0);
        }
        return $html;
    }

    /**
     * Genera HTML lista articoli da array DB
     */
    public function generaHtmlListaArticoli(array $articoli): string {
        $html = '';
        foreach ($articoli as $a) {
            $html .= $this->generaHtmlArticolo($a);
        }
        return $html;
    }

    // ========================================
    // MESSAGGI FEEDBACK
    // ========================================

    /**
     * Genera HTML per messaggio errori
     */
    public static function generaMessaggioErrori(array $errori): string {
        if (empty($errori)) {
            return '';
        }
        $html = "<div class='alert alert-error'><strong>⚠️ Errori:</strong><ul>";
        foreach ($errori as $errore) {
            $html .= "<li>" . htmlspecialchars($errore) . "</li>";
        }
        $html .= "</ul></div>";
        return $html;
    }

    /**
     * Genera HTML per messaggio successo modifica
     */
    public static function generaMessaggioSuccessoModifica(bool $riApprova, string $prefix, string $slug, bool $isAdmin): string {
        $msgRiApprova = $riApprova ? '<br><strong>⚠️ Il caso è stato rimesso in attesa di approvazione.</strong>' : '';
        $btnVisualizza = $isAdmin ? "<br><br><a href='$prefix/caso/$slug' class='btn btn-primary'>Visualizza Caso</a>" : '';

        return <<<HTML
        <div class='alert alert-success'>
            <strong>✅ Caso aggiornato con successo!</strong>
            $msgRiApprova
            $btnVisualizza
        </div>
HTML;
    }

    /**
     * Genera HTML per messaggio successo segnalazione
     */
    public static function generaMessaggioSuccessoSegnalazione(
        int $casoId,
        int $numVittime,
        int $numColpevoli,
        int $numArticoli,
        string $autore
    ): string {
        return <<<HTML
        <div class='alert alert-success'>
            <strong>✅ Segnalazione inviata con successo!</strong><br>
            Il caso è stato inoltrato per la revisione.<br><br>
            <small>
                <strong>Riepilogo:</strong><br>
                • Caso ID: {$casoId}<br>
                • Vittime: {$numVittime}<br>
                • Colpevoli: {$numColpevoli}<br>
                • Fonti: {$numArticoli}<br>
                • Segnalato da: {$autore}
            </small>
        </div>
HTML;
    }
}
