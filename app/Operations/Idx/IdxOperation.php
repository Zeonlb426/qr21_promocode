<?php

declare(strict_types=1);

namespace App\Operations\Idx;

use GuzzleHttp\Client;

/**
 * Class IdxOperation
 * @package App\Operations\Idx
 */
final class IdxOperation
{

    private $idxApiUrl;

    private $accessKey;

    private $secretKey;

    public function __construct()
    {
        $this->idxApiUrl = \config('idx_url', 'https://api.id-x.org/idx/api2/');
        $this->accessKey = \config('idx_accessKey', 'jti-ploom121reg-a6c0e36cba601e8dc141f64cf13de5431a3eb143');
        $this->secretKey = \config('idx_secretKey', '3e6d6c72aaff345af9f6b7a7aeb1f86c4f6fdb87');
    }

    /**
     * @param string $operation
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processRequest(string $operation, array $data): \Psr\Http\Message\ResponseInterface
    {
        $client = new Client();
        $url = $this->idxApiUrl . $operation;
        $result = array_merge([
            'accessKey' => $this->accessKey,
            'secretKey' => $this->secretKey,
        ], $data);

        return  $client->post($url,  [
            'headers' => [
                'Accept'     => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json'  =>  $result
        ]);
    }
}
