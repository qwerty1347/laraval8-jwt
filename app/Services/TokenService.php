<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Constants\HttpConstant;
use App\Http\Requests\ApiKeyManagementRequest;
use App\Services\ApiKeyManagementService;

class TokenService
{
    public $apiKeyManagementService;

    public function __construct()
    {
        $this->apiKeyManagementService = new ApiKeyManagementService();
    }

    /**
     * JWT 생성
     *
     * @param   ApiKeyManagementRequest  $request
     *
     * @return  array
     */
    public function generateToken(ApiKeyManagementRequest $request): array
    {   $id = $request->input('id');
        try {
            $exp = time() + 9999999999;
            $secretKey = env('JWT_SECRET');
            $payload = [
                'id'  => $id,
                'exp' => $exp,
            ];
            $jwt = JWT::encode($payload, $secretKey, 'HS256');
            $result = handleSuccessResult();
            $result["insert"] = [
                "id"         => $id,
                "apiKey"     => $jwt,
                "dateExpire" => Carbon::createFromTimestamp($exp),
                "dateReg"    => Carbon::now(),
                "memo"       => json_encode([
                    "serviceName" => $request->input('serviceName'),
                    "serviceUrl"  => $request->input('serviceUrl'),
                    "managerName" => $request->input('managerName')
                ], JSON_UNESCAPED_UNICODE),
            ];
        } catch (Exception $e) {
            $result = handleFailureResult(HttpConstant::BAD_REQUEST, $e->getMessage());
            $this->apiKeyManagementService->storeApiErrorLog($id, [], $result);
        }
        return $result;
    }

    /**
     * JWT 검증
     *
     * @param   Request  $request
     *
     * @return  array
     */
    public function decodeToken(Request $request): array
    {
        $token = $request->bearerToken();
        $secretKey = env('JWT_SECRET');

        if (isset($token)) {
            try {
                $result = handleSuccessResult();
                $result["decoded"] = JWT::decode($token, new Key($secretKey, 'HS256'));
            } catch (Exception $e) {
                $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
            }
        }
        else{
            $result = handleFailureResult(HttpConstant::BAD_REQUEST, "토큰이 존재하지 않습니다.");
        }
        return $result;
    }
}
