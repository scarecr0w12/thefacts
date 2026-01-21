<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
            'stance' => ['required', 'in:SUPPORTS,REFUTES,CONTEXT'],
            'excerpt' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    protected function prepareForValidation()
    {
        // SSRF protection: block private IP ranges
        $url = $this->input('url');
        if ($url) {
            $host = parse_url($url, PHP_URL_HOST);
            $ip = gethostbyname($host);
            
            if ($this->isPrivateIp($ip)) {
                $this->failedValidation(
                    new \Illuminate\Validation\ValidationException(
                        new \Illuminate\Validation\Validator(
                            null,
                            [],
                            ['url' => ['Invalid or private URL']],
                            null
                        )
                    )
                );
            }
        }
    }

    private function isPrivateIp($ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
