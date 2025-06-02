<?php

return [
    'jwtSecret' => getenv('JWT_SECRET') ?: 'secret_key',
];
