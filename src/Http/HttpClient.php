<?php
namespace iWebTouch\Gelato\Http;

interface HttpClient 
{
    public function setHeaders(array $headers);
    public function request(string $url, string $method = 'GET', array $options = [], ?array $headers = null);
    public function get(string $url, $query = null);
    public function post(string $url, array $data);
    public function getResponse();
}