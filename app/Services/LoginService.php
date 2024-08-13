<?php


namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function __construct()
    {
    }

    /**
     * API 응답 후 에러 처리
     *
     * @param   array  $errors
     *
     * @return  void
     */
    public function handleFailure(array $errors)
    {
        throw ValidationException::withMessages([
            'api_key' => $errors["dmessage"]
        ]);
    }

    /**
     * 세션 생성
     *
     * @param   string $aid        도매꾹 OpenAPI 키
     * @param   array  $domeggook  도매꾹 OpenAPI setLogin
     *
     * @return  void
     */
    public function generateSession(string $aid, array $domeggook)
    {
        Session::put('aid', $aid);
        Session::put('sId', $domeggook["sId"]);
        Session::put('id', $domeggook["id"]);
        Session::put('name', $domeggook["name"]);
        Session::put('affid', $domeggook["affid"]);
        Session::put('loginKeepTime', $domeggook["loginKeepTime"]);
        Session::put('grade', $domeggook["grade"]);
        Session::put('sIdRenewDate', $domeggook["sIdRenewDate"]);
    }
}
