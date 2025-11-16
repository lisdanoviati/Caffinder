<?php

namespace App\Services;

class RdfCafeService
{
    public function all()
    {
        return [
            [
                "id" => 1,
                "name" => "Ngopi Doeloe",
                "address" => "Jl. Gajah Mada, Medan",
                "rating" => 4.7,
                "phone" => "08123456789",
                "image" => "https://picsum.photos/600/400",
                "latitude" => "3.588",
                "longitude" => "98.678",
                "category" => "Coffee Shop",
                "wifi" => true,
                "ramah_laptop" => true,
                "serves_alcohol" => false,
            ],
            // Tambahkan data lain untuk testing UI
        ];
    }

    public function find($id)
    {
        foreach ($this->all() as $c) {
            if ($c['id'] == $id) return $c;
        }
        return null;
    }
}
