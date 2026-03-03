<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/***
 * Для "выстраивания" запросов пользователя, по которым будут отправляться запросы в Nav в очередь с одновременным исполнением только одного запроса по критерию пользователь-площадка
 * Для уменьшения кол-ва одновременных сессий в Nav
 * Использовать с Middleware auth и только после него
 * TODO s.kuznecov доделать и прикрутить площадку после определенности в каком параметре будет указана площадка.
 */

class UseRequestLockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config('local_app.common.disable_user_request_lock_mode')) {
            return $next($request);
        }



        $placeCode = 'nano'; // TODO заглушка брать из параметра запрос

        $key = "user_request_lock-$placeCode-".auth()->user()->id;
        $waitSeconds = 30; // TODO подобрать параметры когда будет понятна примерная длительность тяжелых запросов
        $lockSeconds = 15; // страховка, если процесс умрёт

        // Пытаемся взять блокировку
        $lock = Cache::lock($key, $lockSeconds);

        if (! $lock->block($waitSeconds)) {
            return response()->json([
                'error' => 'User already has an active request. Try again later.',
            ], 429);
        }

        app()->instance('user_request_lock', $lock);

        try {
            // Выполняем запрос
            return $next($request);

        } catch (\Throwable $e) {

            // Обязательно освободить при исключении
            $lock->release();
            throw $e;
        } finally {

            // Освобождение для нормального завершения
            optional($lock)->release();
        }
    }
}
