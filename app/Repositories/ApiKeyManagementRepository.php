<?php

namespace App\Repositories;

use App\Models\NgmApiKeyErrorLog;
use App\Models\NgmApiKeyManagement;
use Illuminate\Database\Eloquent\Collection;

class ApiKeyManagementRepository
{
    public function __construct()
    {
    }

    /**
     * List 가져오기
     *
     * @param   array       $where  [id:회원아이디]
     *
     * @return  ?Collection
     */
    public function getKeyList(array $where): ?Collection
    {
        return NgmApiKeyManagement::where($where)->get();
    }

    /**
     * Row 추가
     *
     * @param   $insert
     *
     * @return  void
     */
    public function store(array $insert)
    {
        NgmApiKeyManagement::insert($insert);
    }

    /**
     * Row 가져오기
     *
     * @param   array   $where  [no:PK]
     *
     * @return  array
     */
    public function getKeyRow(array $where): array
    {
        $obj = NgmApiKeyManagement::where($where);
        return isset($obj) ? $obj->first()->toArray() : [];
    }

    /**
     * Row 업데이트
     *
     * @param   array  $where   [id:PK]
     * @param   array  $update  [apiKey:API 키, dateUpdate:수정날짜]
     *
     * @return  void
     */
    public function update(array $where, array $update)
    {
        NgmApiKeyManagement::where($where)->update($update);
    }

    /**
     * Row 삭제
     *
     * @param   array  $where  [id:PK]
     *
     * @return  void
     */
    public function destroy(array $where)
    {
        NgmApiKeyManagement::where($where)->delete();
    }

    /**
     * Row 추가
     *
     * @param   array  $insert
     *
     * @return  void
     */
    public function storeApiErrorLog(array $insert)
    {
        NgmApiKeyErrorLog::insert($insert);
    }
}
