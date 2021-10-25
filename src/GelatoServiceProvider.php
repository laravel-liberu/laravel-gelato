<?php
namespace iWebTouch\Gelato;

use Illuminate\Support\ServiceProvider;
use iWebTouch\Gelato\Http\{ HttpClient, LaravelHttpClient };

class GelatoServiceProvider extends ServiceProvider
{
    public $singletons = [
        HttpClient::class => LaravelHttpClient::class,
    ];
}