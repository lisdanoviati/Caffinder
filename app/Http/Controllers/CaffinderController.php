<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RdfCafeService;

class CaffinderController extends Controller
{
    public function index(Request $request)
    {
        $service = new RdfCafeService();

        $q = $request->q;
        $category = $request->category;

        $cafes = $service->all();

        // Filter pencarian sederhana (frontend)
        if ($q) {
            $cafes = array_filter($cafes, function ($cafe) use ($q) {
                return stripos($cafe['name'], $q) !== false ||
                       stripos($cafe['address'], $q) !== false;
            });
        }

        if ($category) {
            $cafes = array_filter($cafes, function ($cafe) use ($category) {
                return ($cafe['category'] ?? '') === $category;
            });
        }

        return view('cafes.index', [
            'cafes' => $cafes,
            'categories' => collect($service->all())->pluck('category')->unique()->filter(),
            'q' => $q,
            'category' => $category
        ]);
    }

    public function show($id)
    {
        $service = new RdfCafeService();
        $cafe = $service->find($id);

        return view('cafes.show', compact('cafe'));
    }
}
