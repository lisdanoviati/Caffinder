<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        // contoh data dummy (nanti diganti hasil SPARQL)
        $cafes = [
            ['id' => 1, 'name' => 'Copas Coffee', 'location' => 'Medan Johor', 'rating' => 4.6],
            ['id' => 2, 'name' => 'Ngopi Dari Hati', 'location' => 'Medan Sunggal', 'rating' => 4.2],
        ];

        return view('home', ['cafes' => $cafes]);
    }
}
