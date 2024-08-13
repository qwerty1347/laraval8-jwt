<?php

use App\Constants\HttpConstant;

if (!function_exists("handleSuccessResult")) {
    /**
     * 성공 처리
     *
     * @return  array
     */
    function handleSuccessResult(): array
    {
        return [
            "result"  => HttpConstant::RETURN_SUCCESS,
            "code"    => HttpConstant::OK,
            "message" => ""
        ];
    }
}

if (!function_exists("handleFailureResult")) {
    /**
     * 실패 결과
     *
     * @param   string      $code
     * @param   string      $message
     *
     * @return  array
     */
    function handleFailureResult(string $code, string $message): array
    {
        return [
            "result"  => HttpConstant::RETURN_FAILURE,
            "code"    => $code,
            "message" => $message
        ];
    }
}
