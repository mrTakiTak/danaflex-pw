<?php

/***
 * Файл зарегистрирован в composer.json (autoload/files)
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
