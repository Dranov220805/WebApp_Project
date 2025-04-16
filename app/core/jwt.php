<?php
$requiredEnvVars = [
    'JWT_KEY_SECRET',
    'JWT_ISSUER',
    'JWT_AUDIENCE',
    'JWT_ACCESS_TOKEN_EXP'];


foreach ($requiredEnvVars as $var) {
    if (!isset($_ENV[$var])) {
        throw new Exception("Missing required environment variable: $var");
    }
}

return [
    'secret' => $_ENV['JWT_KEY_SECRET'],
    'issuer' => $_ENV['JWT_ISSUER'],
    'audience' => $_ENV['JWT_AUDIENCE'],
    'accessTokenExp' => (int) $_ENV['JWT_ACCESS_TOKEN_EXP'],
];

//foreach ($requiredEnvVars as $var) {
//    if (getenv($var) === false) {
//        throw new Exception("Missing required environment variable: $var");
//    }
//}
//
//return [
//    'secret' => getenv('JWT_KEY_SECRET'),
//    'issuer' => getenv('JWT_ISSUER'),
//    'audience' => getenv('JWT_AUDIENCE'),
//    'accessTokenExp' => (int) getenv('JWT_ACCESS_TOKEN_EXP'),
//];
