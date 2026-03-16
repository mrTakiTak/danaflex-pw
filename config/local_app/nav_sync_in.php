<?php

return [
    'sync_places' => [
        'value' => [\App\Enums\PlaceEnum::Zao, \App\Enums\PlaceEnum::Nano, \App\Enums\PlaceEnum::Alabuga],
        'comment' => 'Площадки и порядок их запуска для синхронизации.',
    ],
    'queue_prefix' => [
        'value' => 'nav_sync_in_',
        'comment' => 'Префикс очереди job по синхронизации данных Nav по каждой площадке. Например очередь для ЗАО будет nav_sync_in_zao',
    ],
    'sync_process_cache_key_prefix' => [
        'value' => 'nav_sync_process_working_',
        'comment' => 'Для продолжения выполнения процесса синхронизации по площадке в бесконечном цикле в кеш должен 
        присутствовать значение с ключом (пример: nav_sync_process_working_zao). Проверяется в каждой итерации бесконечного цикла.
        Если нет - выход',
    ],
    'setting_name_for_last_sync' => [
        'value' => 'lastSyncModel',
        'comment' => 'Setting для хранения времени последней синхронизации c Nav.',
    ],
    'setting_name_for_last_sync_delete_action' => [
        'value' => 'lastSyncModelDeleteAction',
        'comment' => 'Setting для хранения последнего обработанного значение удаленной в Nav записи из таблицы NAV Queue',
    ],

];
