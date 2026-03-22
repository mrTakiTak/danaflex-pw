<?php

namespace App\Services\Integration;

use App\Services\NTLM\Request;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;

class SoapClient extends \SoapClient
{
    protected Request $client;

    protected bool $exceptions = true;

    public ?string $lastRespStatusCode = null;

    public ?object $lastRespSoapFault = null;

    public function __construct($wsdl, array $options)
    {
        $username = $options['login'];
        $password = $options['password'];
        $this->client = new Request($username, $password);

        unset($options['login']);
        unset($options['password']);

        $this->exceptions = $options['exceptions'] ?? true;

        $wsdlCacheKey = config('local_app.nav_soap.wsdl_cache_key_prefix.value').md5($wsdl);

        $wsdlDataUri = Cache::get($wsdlCacheKey);

        if (is_null($wsdlDataUri)) {
            $wsdlResponse = $this->client->get($wsdl);

            if (empty($wsdlResponse->body)) {
                throw new \RuntimeException(
                    'SoapClient: При запросе wsdl сервер вернул пустой ответ. Возможна ошибка авторизации.'
                );
            }

            if (! static::isValidWsdl($wsdlResponse->body)) {
                throw new \RuntimeException(
                    "SoapClient: При запросе wsdl сервер вернул ответ, который не соответствует формату wsdl. Возможна ошибка в URL: $wsdl Данные ответа: {$wsdlResponse->body}"
                );
            }

            // Формируем data:// URI чтобы SoapClient принял XML из строки (без файлов)
            $wsdlDataUri = 'data://text/xml;base64,'.base64_encode($wsdlResponse->body);
            Cache::put($wsdlCacheKey, $wsdlDataUri, config('local_app.nav_soap.wsdl_cache_ttl.value'));

        }

        $options['cache_wsdl'] = WSDL_CACHE_NONE;

        parent::__construct($wsdlDataUri, $options);
    }

    public function __doRequest($request, $location, $action, $version, $oneWay = false): ?string
    {
        $this->lastRespStatusCode = null;
        $this->lastRespSoapFault = null;

        $result = $this->client->post($location, $request, [
            'Content-Type: application/xml; charset=utf-8',
            'SOAPAction: '.$action,
        ]);

        $isSoapFault = $this->checkSoapFaultXml($result->body);

        $headersArr = static::headersArr($result->headers);
        $statusCode = static::respCode($headersArr['Status-Line']);
        $this->lastRespStatusCode = $statusCode;

        if ($this->exceptions && $isSoapFault) {
            // сработает родительское / коробочное возбуждение исключение soapFault и возвращаемый резултьтат клиентом будет get_debug_type($result) === 'SoapFault'
            return $result->body;
        }

        if ($this->exceptions && $statusCode !== '200') {

            throw new \RuntimeException(
                "SoapClient: Код ответа на запрос не равен 200. Статус ответа: {$headersArr['Status-Line']}. Запрос: $request. Тело ответа: {$result->body} "
            );

        }

        return $result->body;
    }

    public function lastRespSoapFault(): ?object
    {

        return $this->lastRespSoapFault;
    }

    public function lastRespStatusCode(): ?string
    {
        return $this->lastRespStatusCode;
    }

    private function checkSoapFaultXml(?string $content): bool
    {
        if (empty($content)) {
            return false;
        }
        $dom = new DOMDocument;
        $dom->loadXML($content);
        if (! $dom->loadXML($content)) {
            return false; // не XML
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');

        $faultNodes = $xpath->query('//s:Fault');

        if ($faultNodes->length === 0) {
            return false;
        }

        $fault = $faultNodes->item(0);

        $this->lastRespSoapFault = (object) [
            'faultCode' => $xpath->query('faultcode', $fault)->item(0)?->nodeValue,
            'faultString' => $xpath->query('faultstring', $fault)->item(0)?->nodeValue,
        ];

        return true;

    }

    private static function isValidWsdl(string $content): bool
    {
        $dom = new DOMDocument;
        $dom->loadXML($content);

        $root = $dom->documentElement;

        if ($root->localName !== 'definitions') {

            return false;
        }

        if ($root->namespaceURI !== 'http://schemas.xmlsoap.org/wsdl/') {
            return false;
        }

        return true;
    }

    private static function respCode(string $statusHeader): ?string
    {
        $parts = explode(' ', $statusHeader);

        return $parts[1] ?? null;
    }

    private static function headersArr(string $headersStr): array
    {
        $lines = preg_split('/\R/', trim($headersStr));

        $headers = [];

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }
            if (strpos($line, 'HTTP/') === 0) {
                $headers['Status-Line'] = $line;

                continue;
            }
            [$name, $value] = array_map('trim', explode(':', $line, 2) + [1 => '']);
            if ($name === '') {
                continue;
            }
            // сгруппировать одинаковые заголовки в массив
            if (isset($headers[$name])) {
                if (is_array($headers[$name])) {
                    $headers[$name][] = $value;
                } else {
                    $headers[$name] = [$headers[$name], $value];
                }
            } else {
                $headers[$name] = $value;
            }

        }

        return $headers;
    }
}
