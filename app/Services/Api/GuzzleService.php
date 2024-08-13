<?php


namespace App\Services\Api;

use App\Constants\HttpConstant;
use App\Services\ApiKeyManagementService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GuzzleService
{
    protected $client;
    public ApiKeyManagementService $apiKeyManagementService;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKeyManagementService = new ApiKeyManagementService();
    }

    /**
     * POST 요청
     *
     * @param   string  $id
     * @param   string  $url
     * @param   array   $headers
     * @param   array   $formParams
     *
     * @return  array
     */
    public function postRequest(string $id, string $url, array $headers=[], array $formParams): array
    {
        $data = [];
        if (count($headers)) {
            $data["headers"] = $headers;
        }
        if (count($formParams)) {
            $data["form_params"] = $formParams;
        }

        try {
            $response = $this->client->request('POST', $url, $data);
            $result = handleSuccessResult();
            $result["list"] = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
            $this->apiKeyManagementService->storeApiErrorLog($id, $data, $result);
        }
        return $result;
    }

    /**
     * GET 요청
     *
     * @param   string  $id
     * @param   string  $url
     * @param   array   $hedaer
     * @param   array   $formParams
     *
     * @return  array
     */
    public function getRequest(string $id, string $url, array $headers=[], array $formParams): array
    {
        $data = [];
        if (count($headers)) {
            $data["headers"] = $headers;
        }
        if (count($formParams)) {
            $data["form_params"] = $formParams;
        }

        try {
            $response = $this->client->request('GET', $url);
            $result = handleSuccessResult();
            $result["list"] = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
            $this->apiKeyManagementService->storeApiErrorLog($id, $data, $result);
        }
        return $result;
    }
}
