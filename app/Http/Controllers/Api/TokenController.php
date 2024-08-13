<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TokenService;
use App\Constants\HttpConstant;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiKeyManagementRequest;
use App\Services\ApiKeyManagementService;

class TokenController extends Controller
{
    public TokenService $tokenService;
    public ApiKeyManagementService $apiKeyManagementService;

    public function __construct()
    {
        $this->tokenService = new TokenService;
        $this->apiKeyManagementService = new ApiKeyManagementService();
    }

    /**
     * JWT 생성
     *
     * @param   ApiKeyManagementRequest  $request
     *
     * @return  JsonResponse
     */
    public function create(ApiKeyManagementRequest $request): JsonResponse
    {
        $result = $this->tokenService->generateToken($request);
        if ($result["result"] == HttpConstant::RETURN_SUCCESS) {
            $result = $this->apiKeyManagementService->store($result["insert"]);
        }
        return response()->json($result, $result["code"]);
    }

    /**
     * JWT 검증
     *
     * @param   Request       $request
     *
     * @return  JsonResponse
     */
    public function decode(Request $request): JsonResponse
    {
        $result = $this->tokenService->decodeToken($request);
        return response()->json($result, $result["code"]);
    }
}
