<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\TokenService;
use App\Constants\HttpConstant;
use Illuminate\Http\JsonResponse;
use App\Services\ApiKeyManagementService;
use App\Http\Requests\ApiKeyManagementRequest;
use App\Services\Api\Domeggook\UserManagementService;

class ApiKeyManagementController extends Controller
{
    public TokenService $tokenService;
    public ApiKeyManagementService $apiKeyManagementService;

    public function __construct() {
        $this->tokenService = new TokenService;
        $this->apiKeyManagementService = new ApiKeyManagementService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return  View
     */
    public function index(): View
    {
        $where = ["id" => session()->get('id')];
        $params = [
            "userRow" => $this->apiKeyManagementService->getKeyList($where),
        ];
        return view('key-managements.index')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ApiKeyManagementRequest  $request
     * @return JsonResponse
     */
    public function store(ApiKeyManagementRequest $request): JsonResponse
    {
        $userManagementService = new UserManagementService($request);
        $response = $userManagementService->setLoginChk();

        if ($response["result"] == HttpConstant::RETURN_FAILURE || (isset($response["list"]["errors"]) && count($response["list"]["errors"]))) {
            Session::flush();
            return redirect()->route('login');
        }

        $result = $this->tokenService->generateToken($request);
        if ($result["result"] == HttpConstant::RETURN_SUCCESS) {
            $result = $this->apiKeyManagementService->store($result["insert"]);
        }
        return response()->json($result, $result["code"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        $result = $this->apiKeyManagementService->edit($request);
        return response()->json($result, $result["code"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \app\Http\Requests\ApiKeyManagementRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ApiKeyManagementRequest $request): ?JsonResponse
    {
        $result = $this->apiKeyManagementService->update($request);
        return response()->json($result, $result["code"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $result = $this->apiKeyManagementService->destroy($request->no);
        return response()->json($result, $result["code"]);
    }
}
