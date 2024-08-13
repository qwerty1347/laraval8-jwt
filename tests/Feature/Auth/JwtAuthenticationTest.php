<?php

namespace Tests\Feature;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Constants\HttpConstant;
use Tests\TestCase;

class JwtAuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * JWT 검증 테스트
     *
     * @return  void
     */
    public function testDecode()
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZW1iZXJJZCI6ImRldjE4IiwiZXhwIjoxODIxODAzOTY3fQ.CxaOtQACbGl3Jh-HIeVYGzOFl6SJ_M5OmZnx2biLHgM";
        $secretKey = env('JWT_SECRET', '');

        if (isset($token)) {
            try {
                $result = handleSuccessResult();
                $result["decoded"] = JWT::decode($token, new Key($secretKey, 'HS256'));
            } catch (Exception $e) {
                $result = handleFailureResult(HttpConstant::INTERNAL_SERVER_ERROR, $e->getMessage());
            }
        }
        else {
            $result = handleFailureResult(HttpConstant::BAD_REQUEST, "토큰이 존재하지 않습니다.");
        }
        $this->assertEquals(200, $result["code"]);
    }
}
