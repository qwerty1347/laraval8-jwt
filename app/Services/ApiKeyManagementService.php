<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\HttpConstant;
use App\Models\NgmApiKeyManagement;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\ApiKeyManagementRequest;
use App\Repositories\ApiKeyManagementRepository;

class ApiKeyManagementService
{
    public $apiKeyManagementRepository;

    public function __construct()
    {
        $this->apiKeyManagementRepository = new ApiKeyManagementRepository();
    }

    /**
     * ngm_apiKeyManagement List 가져오기
     *
     * @param   array   [id:회원아이디]
     *
     * @return  ?Collection
     */
    public function getKeyList(array $where): ?Collection
    {
        return  $this->apiKeyManagementRepository->getKeyList($where);
    }

    /**
     * ngm_apiKeyManagement Row 생성
     *
     * @param   array  $insert  [id:회원아이디, apiKey:JWT, dateExpire:토큰만료일, dateReg:토큰생성일, memo:{serviceName:서비스명, serviceUrl:서비스URL, manger:담당자성명}]
     *
     * @return  array
     */
    public function store(array $insert): array
    {
        try {
            $this->apiKeyManagementRepository->store($insert);
            $result = handleSuccessResult();
        } catch (Exception $e) {
            $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        return $result;
    }

    /**
     * ngm_apiKeyManagement Row 가져오기
     *
     * @param   Request  $request
     *
     * @return  array
     */
    public function edit(Request $request): array
    {
        $where = ["no" => $request->no];

        try {
            $row = $this->apiKeyManagementRepository->getKeyRow($where);
            if (!count($row)) {
                throw new Exception("데이터가 존재하지 않습니다.");
            }
            $result = handleSuccessResult();
            $result["list"] = $row;
        } catch (Exception $e) {
            $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        return $result;
    }

    /**
     * ngm_apiKeyManagement Row 업데이트
     *
     * @param   ApiKeyManagementRequest  $request
     *
     * @return  array
     */
    public function update(ApiKeyManagementRequest $request): array
    {
        $where  = ["no" => $request->editNo];
        $update = [
            "memo" => json_encode([
                "serviceName" => $request->serviceName,
                "serviceUrl"  => $request->serviceUrl,
                "managerName" => $request->managerName
            ], JSON_UNESCAPED_UNICODE),
            "dateUpdate" => Carbon::now()
        ];

        try {
            $this->apiKeyManagementRepository->update($where, $update);
            $result = handleSuccessResult();
        } catch (Exception $e) {
            $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        return $result;
    }

    /**
     * ngm_apiKeyManagement Row 삭제
     *
     * @param   int  $no  PK
     *
     * @return  void
     */
    public function destroy(int $no)
    {
        $where = ["no" => $no];

        try {
            $this->apiKeyManagementRepository->destroy($where);
            $result = handleSuccessResult();
        } catch (Exception $e) {
            $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        return $result;
    }

    /**
     * ngm_apiKeyErrorLog Row 추가
     *
     * @param   string  $id        회원 아이디
     * @param   array   $request   요청값
     * @param   array   $response  응답값
     *
     * @return  void
     */
    public function storeApiErrorLog(string $id, array $request, array $response)
    {
        $data = [
            "id"       => $id,
            "request"  => count($request) ? json_encode($request, JSON_UNESCAPED_UNICODE) : "",
            "response" => json_encode($response, JSON_UNESCAPED_UNICODE),
            "dateReg"  => Carbon::now(),
            "memo"     => $_SERVER["REMOTE_ADDR"]."@".$_SERVER['HTTP_USER_AGENT']
        ];
        $this->apiKeyManagementRepository->storeApiErrorLog($data);
    }
}
