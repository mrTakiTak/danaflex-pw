<?php

namespace App\Services\Integration;

use App\Enums\PlaceEnum;
use Arr;


class NavisionSoapService
{
    protected ?SoapClient $soapClient = null;

    protected ?PlaceEnum $place = null;

    protected ?string $pageOrCodeunitName = null;

    protected bool $isCodeunit = false;

    public function __construct(PlaceEnum $place, string $pageOrCodeunitName, ?array $params)
    {
        $this->place = $place;
        $this->pageOrCodeunitName = $pageOrCodeunitName;

        $this->isCodeunit = $params['isCodeunit'] ?? false;

        if ($this->isCodeunit) {
            $unitName = 'Codeunit';
        } else {
            $unitName = 'Page';
        }

        $url = config('local_app.nav_soap.base_url.value')[$place->value]."$unitName/$pageOrCodeunitName";

        $options = config('local_app.nav_soap.credentials.value');
        $options['exceptions'] = $params['exceptions'] ?? true;

        $this->soapClient = new SoapClient($url, $options);

    }

    public static function of(PlaceEnum $place, string $pageOrCodeunitName, ?array $params = null): static
    {
        return new static($place,$pageOrCodeunitName,$params);
    }

    public function lastRespStatusCode(): ?string
    {

        return $this->soapClient->lastRespStatusCode();
    }

    public function lastRespSoapFault(): ?array
    {
        return get_object_vars($this->soapClient->lastRespSoapFault());
    }

    public function client(): SoapClient
    {
        return $this->soapClient;
    }

    public function readMultiple(array $params): ?array
    {
        $result = $this->soapClient->ReadMultiple($params);

        if ($this->lastRespStatusCode() !== '200') {
            // при http-ошибке возвращаем null
            return null;
        }

        $resultData = $result->ReadMultiple_Result;

        // результат всегда массив - пустой, с одной или несколькими записями
        $resultArr = Arr::wrap($resultData->{$this->pageOrCodeunitName} ?? []);

        // преобразование массива объектов (для непустого) в массив ассоциативных массивов.
        return array_map('get_object_vars', $resultArr);
    }

    public function update(array $data): ?array
    {

        $dataSoap = (object) [
            "{$this->pageOrCodeunitName}" => (object) $data,
        ];

        $result = $this->soapClient->Update($dataSoap);

        if ($this->lastRespStatusCode() !== '200') {
            // при http-ошибке возвращаем null
            return null;
        }

        $resultData = $result->{$this->pageOrCodeunitName};

        return get_object_vars($resultData);

    }
    /***
     * TODO Остальные требуемые методы Soap реализуем при необходимости по аналогии с readMultiple() и update()
     */
}
