<?php

namespace App\Http\Controllers;

use App\Services\RdfCafeService;

class HomeController extends Controller
{
    public function index()
    {
        $service = new RdfCafeService();

        $cafes = $service->getCafes();

        // decode entitas HTML
        $cafes = array_map(function ($cafe) {
            return array_map(function ($value) {
                return is_string($value)
                    ? html_entity_decode($value, ENT_QUOTES, 'UTF-8')
                    : $value;
            }, $cafe);
        }, $cafes);

        return view('home', compact('cafes'));
    }
}
