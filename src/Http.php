<?php
declare(strict_types=1);

namespace TwitchWatcher;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Http
{
    private HttpClientInterface $h;

    public function __construct()
    {
        $this->h = HttpClient::create();
    }

    public function get(string $url, array $options = []) : ResponseInterface
    {
        return $this->h->request('GET', $url, $options);
    }

    public function post(string $url, array $options = []) : ResponseInterface
    {
        return $this->h->request('POST', $url, $options);
    }
    public function request(string $method, string $url, array $options = []) : ResponseInterface
    {
        return $this->h->request($method, $url, $options);
    }
}