<?php

/***
 * Файл зарегистрирован в composer.json (autoload/files)
 * На среде разработки после изменений делаем: composer dump-autoload
 */

/**
 * dd() со снятием lock, установленного UseRequestLockMiddleware
 */
function dd_(...$vars): void
{
    if (app()->has('user_request_lock')) {
        $lock = app('user_request_lock');
        optional($lock)->release();
    }

    dd(...$vars);
}

function arrayValueOrNull(array $dataArray, string $key): mixed
{
    $value = $dataArray[$key] ?? null;
    switch ($value) {
        case null:
        case '':
            return null;
        default:
            return $value;

    }

}
