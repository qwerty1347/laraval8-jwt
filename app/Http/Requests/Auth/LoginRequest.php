<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Str;
use App\Services\LoginService;
use App\Constants\HttpConstant;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Services\Api\Domeggook\UserManagementService;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'       => ['required', 'string'],
            'password' => ['required', 'string'],
            'api_key'  => ['required', 'string']
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        $userManagementService = new UserManagementService($this->all());
        $result = $userManagementService->setLogin();
        $loginService = new LoginService($result);

        if ($result["result"] == HttpConstant::RETURN_FAILURE || (isset($result["list"]["errors"]) && count($result["list"]["errors"]))) {
            $loginService->handleFailure($result["list"]["errors"]);
        }
        else {
            $aid = $this->request->get('api_key');
            $loginService->generateSession($aid, $result["list"]["domeggook"]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
