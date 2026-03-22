<?php

namespace App\Console\Commands\Examples;

use App\Enums\PlaceEnum;
use App\Services\Integration\NavisionSoapService;
use Illuminate\Console\Command;

class NavisionSoapExampleCommand extends Command
{
    protected $signature = 'app:nav-soap-example';

    protected $description = 'Пример использования Navision Soap';

    public function handle()
    {

        echo 'Создаем сервисе для Page с режимом срабатывания http-исключений... ';
        $service1 = NavisionSoapService::of(PlaceEnum::Zao, 'MarkingCodesOrders');
        echo "создан \r\n";

        echo "Запрос ReadMultiple... \r\n";
        $resArray = $service1
            ->readMultiple(
                [
                    'filter' => [
                        'Field' => 'OrderCM',
                        'Criteria' => '2ed422dd-c842-4d8c-af2a-ea77b6e9ad01',
                    ],
                    'setSize' => 10,
                ]
            );

        var_dump($resArray);
        echo "Код последнего ответа (строка): {$service1->lastRespStatusCode()} \r\n";

        echo 'Создаем сервисе для Codeunit с режимом срабатывания http-исключений... ';
        $service2 = NavisionSoapService::of(PlaceEnum::Zao, 'WS_Global',
            [
                'isCodeunit' => true, // false - по умолчанию
            ]
        );
        echo "создан \r\n";

        echo 'Создаем сервисе для Page без срабатывания http-исключений... ';
        $service3 = NavisionSoapService::of(PlaceEnum::Zao, 'MarkingCodesOrders',
            [
                'exceptions' => false, // true - по умолчанию
            ]
        );

        echo "создан \r\n";

        $res = $service3->update(
            [
                // Данные, которые вызовут ошибку из-за неактуальности Key
                'Key' => '64;N8QAAACHAQAAAAAtMmVkNDIyZGQtYzg0Mi00ZDhjLWFmMmEtZWE3N2I2ZTlhZDAx10;64720538940;',
                'StatusTtext' => 'TEST',
            ]
        );
        echo "Код последнего ответа (строка): {$service3->lastRespStatusCode()} \r\n"; // = "500"

        $soapFault = $service3->lastRespSoapFault();
        $soapFaultCode = $soapFault['faultCode'] ?? null; // = "a:Microsoft.Dynamics.Nav.Service.WebServices.ServiceBrokerException"
        $soapFaultString = $soapFault['faultString'] ?? null; // = "Other user has modified "MarkingCodesOrders" "Id=CONST(1),Заказ КМ=CONST(2ed422dd-c842-4d8c-af2a-ea77b6e9ad01)""

        echo "Код последней ошибки Soap: $soapFaultCode \r\n";
        echo "Текст последней ошибки Soap: $soapFaultString \r\n";

    }
}
