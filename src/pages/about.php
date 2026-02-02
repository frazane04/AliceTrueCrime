<?php
require_once __DIR__ . '/../helpers/utils.php';

$content = loadTemplate('chi_siamo');
echo getTemplatePage('Chi Siamo - AliceTrueCrime', $content);
?>