<?php
namespace iWebTouch\Gelato\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class LaravelHttpClient implements HttpClient
{
    protected array $headers = [];

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    
        return $this;
    }

    public function request(string $url, string $method = 'GET', array $options = [], ?array $headers = null) : Response
    {
        if (! is_null($headers)) {
            $this->setHeaders($headers);
        }

        $this->response = Http::withHeaders($this->headers)
                ->send($method, $url, $options);

        return $this->response;                
    }

    public function get(string $url, $query = null) : Response
    {
        $this->response = Http::withHeaders($this->headers)
                            ->get($url, $query);
        
        return $this->response;
    }

    public function post(string $url, array $data) : Response
    {
        $this->response = Http::withHeaders($this->headers)
                            ->post($url, $data);

        return $this->response;
    }

    public function patch(string $url, array $data) : Response
    {
        $this->response = Http::withHeaders($this->headers)
                            ->patch($url, $data);

        return $this->response;
    }

    public function put(string $url, array $data) : Response
    {        
        $this->response = Http::withHeaders($this->headers)
                            ->put($url, $data);

        return $this->response;
    }

    public function delete(string $url) : Response
    { 
        $this->response = $this->request('DELETE', $url);

        return $this->response;
    }

    public function getResponse() : Response
    {
        return $this->response;
    }
}