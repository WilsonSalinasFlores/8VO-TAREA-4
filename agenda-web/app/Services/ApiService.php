<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiService {
    private string $base;
    public function __construct() {
        $this->base = config('app.api_url', 'http://localhost:8000/api');
    }
    private function http() {
        return Http::withToken(Session::get('token', ''))->acceptJson();
    }
    public function post(string $path, array $data): \Illuminate\Http\Client\Response {
        return Http::acceptJson()->post($this->base.$path, $data);
    }
    public function authPost(string $path, array $data): \Illuminate\Http\Client\Response {
        return $this->http()->post($this->base.$path, $data);
    }
    public function authGet(string $path, array $query = []): \Illuminate\Http\Client\Response {
        $url = $this->base . $path;
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        return $this->http()->get($url);
    }
    public function authPatch(string $path, array $data = []): \Illuminate\Http\Client\Response {
        return $this->http()->patch($this->base.$path, $data);
    }
    public function authPut(string $path, array $data = []): \Illuminate\Http\Client\Response {
        return $this->http()->put($this->base.$path, $data);
    }
    public function authDelete(string $path): \Illuminate\Http\Client\Response {
        return $this->http()->delete($this->base.$path);
    }
}
