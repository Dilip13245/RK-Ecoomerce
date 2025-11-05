<?php

return [
    'API_KEY' => env('APP_API_KEY'),
    'SECRET' => env('APP_SECRET'), /* Get it from  https://randomkeygen.com/ , 32 bit */
    'IV' => env('APP_IV'), /* 16 digits from above SECRET string*/
    'API_BASE_URL' => env('API_BASE_URL'),
    'SUCCESS' => 200,
    'ERROR' => 400,
    'NOT_FOUND' => 404,
    'INTERNAL_ERROR' => 500,
    'UNAUTHORIZED' => 401,
    'INACTIVE' => 403,
    'for_zero_eight' => 408,
    'ENCRYPTION_ENABLED' => 0,
    'VALIDATION_ERROR' => 422,
];

