<?php


namespace App\Services\Api\Domeggook;

use App\Services\Api\GuzzleService;

class UserManagementService
{
    var $request;
    public GuzzleService $guzzleService;

    public function __construct($request)
    {
        $this->request = $request;
        $this->guzzleService = new GuzzleService();
    }

    /**
     * setLogin 요청
     *
     * @param   array   $data   [ver:API버전, mode:API호출모드, aid:API키, id:회원아이디, pw:회원비밀번호, om:API출력형식, loginKeep:로그인세션유지기간, ip:아이피]
     *
     * @return  array
     */
    public function setLogin(): array
    {
        $id = $this->request["id"];
        $data = [
            "ver"       => env('SET_LOGIN_VER', '1.1'),
            "mode"      => "setLogin",
            "aid"       => $this->request["api_key"],
            "id"        => $id,
            "pw"        => $this->request["password"],
            "om"        => "json",
            "loginKeep" => "off",
            "ip"        => $_SERVER["REMOTE_ADDR"],
            "device"    => "Third Party"
        ];
        return $this->guzzleService->postRequest($id, env('DOMEGGOOK_API_URL', 'https://domeggook.com/ssl/api/'), [], $data);
    }

    /**
     * setLoginChk 요청
     *
     * @return  array
     */
    public function setLoginChk(): array
    {
        $id = session()->has('id') ? session()->get('id') : "";
        $data = [
            "ver"          => env('SET_LOGIN_CHK_VER', '4.0'),
            "mode"         => "setLoginChk",
            "aid"          => session()->has('aid') ? session()->get('aid') : "",
            "cId"          => $id ? md5($id) : "",
            "id"           => $id,
            "sId"          => session()->has('sId') ? session()->get('sId')  : "",
            "sIdRenewDate" => session()->has('sIdRenewDate') ? session()->get('sIdRenewDate') : "",
            "om"           => "json"
        ];
        return $this->guzzleService->postRequest($id, env('DOMEGGOOK_API_URL', 'https://domeggook.com/ssl/api/'), [], $data);
    }
}
