<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RdfCafeService;
use Illuminate\Support\Facades\Http;

class CaffinderController extends Controller
{
    public function index(Request $request)
    {
        $fuseki = new RdfCafeService();

        $search     = $request->q ?? null;
        $category   = $request->category ?? null;
        $district   = $request->district ?? null;
        $facilities = $request->facilities ?? [];
        $priceRange = $request->price ?? null;
        $order      = $request->order ?? null;

        $filters = [
            'category'   => $category,
            'district'   => $district,
            'facilities' => $facilities,
            'price'      => $priceRange,
            'order'      => $order,
        ];

        if ($search || $category || $district || !empty($facilities) || $priceRange || $order) {
            $cafes = $fuseki->searchCafes($search, $category, $district, $facilities, $priceRange, $order);
        } else {
            $cafes = $fuseki->getCafes();
        }

        // FIX: decode semua entity
        if (is_array($cafes)) {
            $cafes = $this->decodeHtmlEntitiesArray($cafes);
        }

        return view('cafes.index', [
            'cafes'              => $cafes,
            'categories'         => $fuseki->getCategories(),
            'districts'          => $fuseki->getDistricts(),
            'facilities'         => $fuseki->getFacilities(),
            'q'                  => $search,
            'category'           => $category,
            'district'           => $district,
            'filters'            => $filters,
            'selectedFacilities' => $facilities,
        ]);
    }

    public function show($id)
    {
        $query = " 
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>
        PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

        SELECT ?name ?telepon ?rating ?alamat ?kategori 
               ?wifi ?alcohol ?laptop ?wheel 
               ?latitude ?longitude ?locurl 
               ?bestmenu ?foto
               ?harga_min ?harga_max
               ?open_senin ?close_senin
               ?open_selasa ?close_selasa
               ?open_rabu ?close_rabu
               ?open_kamis ?close_kamis
               ?open_jumat ?close_jumat
               ?open_sabtu ?close_sabtu
               ?open_minggu ?close_minggu
               ?live_music ?pet_friendly
        WHERE {
            caff:$id a caff:cafe ;
                     caff:nama_cafe ?name ;
                     caff:rating ?rating ;
                     caff:hasaddress ?alamatNode .

            OPTIONAL { caff:$id caff:telepon ?telepon . }
            ?alamatNode caff:nama_alamat ?alamat .

            OPTIONAL {
                caff:$id caff:hascategory ?kategoriNode .
                BIND(REPLACE(STR(?kategoriNode), \"^.*_\", \"\") AS ?kategori)
            }

            OPTIONAL {
                caff:$id caff:hasfacility ?facNode .
                OPTIONAL { ?facNode caff:wifi ?wifi . }
                OPTIONAL { ?facNode caff:alcohol ?alcohol . }
                OPTIONAL { ?facNode caff:laptop_friendly ?laptop . }
                OPTIONAL { ?facNode caff:wheel_chair_access ?wheel . }
                OPTIONAL { ?facNode caff:live_music ?live_music . }
                OPTIONAL { ?facNode caff:pet_friendly ?pet_friendly . }
            }

            OPTIONAL {
                caff:$id caff:hasprice ?priceNode .
                OPTIONAL { ?priceNode caff:harga_min ?harga_min . }
                OPTIONAL { ?priceNode caff:harga_max ?harga_max . }
            }

            OPTIONAL {
                caff:$id caff:hasopenhour ?openNode .
                OPTIONAL { ?openNode caff:open_senin ?open_senin ; caff:close_senin ?close_senin . }
                OPTIONAL { ?openNode caff:open_selasa ?open_selasa ; caff:close_selasa ?close_selasa . }
                OPTIONAL { ?openNode caff:open_rabu ?open_rabu ; caff:close_rabu ?close_rabu . }
                OPTIONAL { ?openNode caff:open_kamis ?open_kamis ; caff:close_kamis ?close_kamis . }
                OPTIONAL { ?openNode caff:open_jumat ?open_jumat ; caff:close_jumat ?close_jumat . }
                OPTIONAL { ?openNode caff:open_sabtu ?open_sabtu ; caff:close_sabtu ?close_sabtu . }
                OPTIONAL { ?openNode caff:open_minggu ?open_minggu ; caff:close_minggu ?close_minggu . }
            }

            OPTIONAL {
                caff:$id caff:haslocation ?loc .
                OPTIONAL { ?loc caff:latitude ?latitude . }
                OPTIONAL { ?loc caff:longitude ?longitude . }
                OPTIONAL { ?loc caff:loc_url ?locurl . }
            }

            OPTIONAL {
                caff:$id caff:hasmenubest ?recNode .
                OPTIONAL { ?recNode caff:bestmenu ?bestmenu . }
            }

            OPTIONAL {
                caff:$id caff:hasphoto ?photoNode .
                OPTIONAL { ?photoNode caff:foto_url ?foto . }
            }
        }
        LIMIT 1
        ";

        $response = Http::withHeaders([
            'Content-Type' => 'application/sparql-query',
            'Accept' => 'application/sparql-results+json'
        ])->withBody($query, 'application/sparql-query')
          ->post('http://localhost:3030/caffinder/sparql');

        if ($response->failed()) {
            abort(500, "Fuseki error: " . $response->body());
        }

        $json = $response->json();

        if (!isset($json['results']['bindings'][0])) {
            abort(404, "Cafe not found: $id");
        }

        $row = $json['results']['bindings'][0];
        $get = fn($key) => $row[$key]['value'] ?? null;

        $cafe = [
            'id'        => $id,
            'name'      => $get('name'),
            'telepon'   => $get('telepon'),
            'rating'    => $get('rating'),
            'alamat'    => $get('alamat'),

            'kategori'  => $this->simplifyKategori($get('kategori')),
            'foto'      => $get('foto'),

            'wifi'         => $get('wifi') === null ? null : filter_var($get('wifi'), FILTER_VALIDATE_BOOLEAN),
            'alcohol'      => $get('alcohol') === null ? null : filter_var($get('alcohol'), FILTER_VALIDATE_BOOLEAN),
            'laptop'       => $get('laptop') === null ? null : filter_var($get('laptop'), FILTER_VALIDATE_BOOLEAN),
            'wheel'        => $get('wheel') === null ? null : filter_var($get('wheel'), FILTER_VALIDATE_BOOLEAN),
            'live_music'   => $get('live_music') === null ? null : filter_var($get('live_music'), FILTER_VALIDATE_BOOLEAN),
            'pet_friendly' => $get('pet_friendly') === null ? null : filter_var($get('pet_friendly'), FILTER_VALIDATE_BOOLEAN),

            'harga_min' => $get('harga_min'),
            'harga_max' => $get('harga_max'),

            'open_senin'   => $this->formatTime($get('open_senin')),
            'close_senin'  => $this->formatTime($get('close_senin')),
            'open_selasa'  => $this->formatTime($get('open_selasa')),
            'close_selasa' => $this->formatTime($get('close_selasa')),
            'open_rabu'    => $this->formatTime($get('open_rabu')),
            'close_rabu'   => $this->formatTime($get('close_rabu')),
            'open_kamis'   => $this->formatTime($get('open_kamis')),
            'close_kamis'  => $this->formatTime($get('close_kamis')),
            'open_jumat'   => $this->formatTime($get('open_jumat')),
            'close_jumat'  => $this->formatTime($get('close_jumat')),
            'open_sabtu'   => $this->formatTime($get('open_sabtu')),
            'close_sabtu'  => $this->formatTime($get('close_sabtu')),
            'open_minggu'  => $this->formatTime($get('open_minggu')),
            'close_minggu' => $this->formatTime($get('close_minggu')),

            'latitude'  => $get('latitude'),
            'longitude' => $get('longitude'),
            'locurl'    => $get('locurl'),

            'bestmenu'  => $get('bestmenu'),
        ];

        // === INTEGRASI DATASET EKSTERNAL OSM (NOMINATIM) ===
        $lat = $cafe['latitude'];
        $lon = $cafe['longitude'];

        $osmResponse = Http::withHeaders([
            'User-Agent' => 'CaffinderApp/1.0 (contact: example@gmail.com)'
        ])->get("https://nominatim.openstreetmap.org/reverse", [
            'lat' => $lat,
            'lon' => $lon,
            'format' => 'json',
            'addressdetails' => 1,
            'zoom' => 18,
        ]);

        $osmData = $osmResponse->json();   // <-- INI WAJIB ADA

        $cafe['osm'] = [
            'display_name' => $osmData['display_name'] ?? null,
            'address'      => $osmData['address'] ?? null,
            'boundingbox'  => $osmData['boundingbox'] ?? null,
        ];

        // FIX: decode semua entity utk halaman detail
        $cafe = array_map(fn($v) => $this->deepDecode($v), $cafe);

        return view('cafes.show', compact('cafe'));
    }

    private function simplifyKategori($uri)
    {
        if (!$uri) return null;

        if (str_contains($uri, 'kategori_cafe')) return 'Cafe';
        if (str_contains($uri, 'kategori_specialty')) return 'Specialty Coffee';

        return $uri;
    }

    // ============================
    // ENTITY DECODER FINAL
    // ============================

    private function deepDecode($value)
    {
        if (!is_string($value)) return $value;

        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');

        $value = str_replace("&apos;", "'", $value);
        $value = str_replace("&#39;", "'", $value);
        $value = str_replace("&quot;", "\"", $value);
        $value = str_replace("&amp;", "&", $value);

        return $value;
    }

    private function decodeHtmlEntitiesArray(array $items): array
    {
        return array_map(function ($item) {
            return array_map(fn($v) => $this->deepDecode($v), $item);
        }, $items);
    }

    public function nlpSearch(Request $req)
    {
        $raw = strtolower($req->q ?? '');

        $filters = [
            'wifi'          => str_contains($raw, 'wifi'),
            'murah'         => str_contains($raw, 'murah'),
            'laptop'        => str_contains($raw, 'nugas') || str_contains($raw, 'laptop'),
            'specialty'     => str_contains($raw, 'specialty'),
            'instagramable' => str_contains($raw, 'aesthetic') || str_contains($raw, 'instagramable'),
        ];

        $keyword = $raw;

        $service = app(\App\Services\RdfCafeService::class);
        $cafes = $service->smartSearchWithDistrict($filters, $keyword);

        if (is_array($cafes)) {
            $cafes = $this->decodeHtmlEntitiesArray($cafes);
        }

        return view('cafes.index', [
            'cafes'      => $cafes,
            'query'      => $req->q,
            'categories' => $service->getCategories(),
        ]);
    }

    private function formatTime($time)
    {
        if (!$time) return null;

        // normalisasi: ambil hanya bagian jam dan menit valid
        $clean = preg_replace('/[^0-9:]/', '', $time); // buang karakter aneh

        // tambahkan detik jika hilang
        if (preg_match('/^\d{1,2}:\d{2}$/', $clean)) {
            $clean .= ':00';
        }

        try {
            return \Carbon\Carbon::createFromFormat('H:i:s', $clean)->format('H:i');
        } catch (\Exception $e) {
            return null; 
        }
    }
}
