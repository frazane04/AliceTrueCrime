<?php
require_once __DIR__ . '/../helpers/utils.php';

$content = loadTemplate('about');
echo getTemplatePage('Chi Siamo - AliceTrueCrime', $content, "Scopri chi c'è dietro AliceTrueCrime e la nostra missione di approfondimento sulla cronaca nera italiana.");
?>