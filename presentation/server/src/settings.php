<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$dbCreds = require __DIR__. '/db-credentials.php';

return [
    'settings' => [
        'displayErrorDetails' => true,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        'db' => $dbCreds,

        'conpExamQuestionsNumber' => 24,
        'conpExamCorrectQuestionsRequired' => 22
    ],
];
