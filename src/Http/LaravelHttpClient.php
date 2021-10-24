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

    public function request(string $method = 'GET', string $url, array $options = [], ?array $headers = null) : Response
    {
        if (!is_null($headers)) {
            $this->setHeaders($headers);
        }

        $this->response = Http::withHeaders($this->headers)
                ->send($method, $url, $options);

        return $this->response;                
    }

    public function get(string $url, $query = null) : Response
    {
        $this->response = $this->request('GET', $url, [
                                'query' => $query,
                            ]);
        
        return $this->response;
    }

    public function post(string $url, array $data) : Response
    {
        $this->response = $this->request('POST', $url, [
                                    'body' => $data,
                                ]);

        return $this->response;
    }

    public function patch(string $url, array $data) : Response
    {
        $this->response = $this->request('PATCH', $url, [
                                'body' => $data,
                            ]);

        return $this->response;
    }

    public function put(string $url, array $data) : Response
    {
        return $this->request('PUT', $url, [
                            'body' => $data,
                        ]);
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

    public function getResponseData() : array
    {
        return $this->response->json();
    }
}