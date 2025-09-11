<?php

// Set the document root to backend/public for proper routing
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/backend/public';

// Change working directory to backend/public
chdir(__DIR__ . '/backend/public');

// Include the backend application
require_once __DIR__ . '/backend/public/index.php';
