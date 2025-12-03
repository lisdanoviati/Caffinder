<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RdfCafeService
{
    public function getCafes()
{
    $query = '
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>
        
        SELECT ?cafe ?name ?alamat ?fotoUrl ?kategoriLabel ?rating
        WHERE {
            ?cafe a caff:cafe ;
                  caff:nama_cafe ?name ;
                  caff:hasaddress ?alamatNode ;
                  caff:hasphoto ?fotoNode .

            ?alamatNode caff:nama_alamat ?alamat .

            # ambil foto_url dari node foto
            OPTIONAL { ?fotoNode caff:foto_url ?fotoUrl . }

            # kategori → tampilkan label nya
            OPTIONAL { 
                ?cafe caff:hascategory ?kategoriNode .
                OPTIONAL { ?kategoriNode caff:label ?kategoriLabel . }
            }

            OPTIONAL { ?cafe caff:rating ?rating . }
        }
        ORDER BY ?name
       
    ';

    $response = Http::withBody($query, 'application/sparql-query')
        ->withHeaders([
            'Accept' => 'application/sparql-results+json'
        ])
        ->post('http://localhost:3030/caffinder/sparql');

    $json = $response->json();

    if (!$json || !isset($json['results']['bindings'])) {
        return [];
    }

    $cafes = [];

    foreach ($json['results']['bindings'] as $row) {

        // extract id from URI
        $uri = $row['cafe']['value'];
        $parts = explode('#', $uri);
        $id = end($parts);

        $cafes[] = [
            'id'       => $id,
            'name'     => $row['name']['value'],
            'address'  => $row['alamat']['value'],
            'district' => $this->extractDistrict($row['alamat']['value']),
            'image' => $row['fotoUrl']['value'] ?? '/images/default-cafe.jpg',
            'category' => $row['kategoriLabel']['value'] ?? 'Umum',
            'rating'   => $row['rating']['value'] ?? null,
        ];
    }

    return $cafes;
}



    /**
     * LOAD DISTINCT CATEGORY UNTUK DROPDOWN
     */
   public function getCategories()
{
    $query = "
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>

        SELECT DISTINCT ?kategori ?namaKategori
        WHERE {
            ?kategori a caff:kategori .
            OPTIONAL { ?kategori caff:nama_kategori ?namaKategori . }
        }
        ORDER BY ?namaKategori
    ";

    $response = Http::withBody($query, 'application/sparql-query')
        ->withHeaders(['Accept' => 'application/sparql-results+json'])
        ->post('http://localhost:3030/caffinder/sparql');

    $json = $response->json();

    if (!$json || !isset($json['results']['bindings'])) {
        return [];
    }

    $categories = [];

    foreach ($json['results']['bindings'] as $row) {
        $uri = $row['kategori']['value'];
        $parts = explode('#', $uri);
        $id = end($parts);

        $categories[] = [
            'id'   => $id,
            'name' => $row['namaKategori']['value'] ?? $id
        ];
    }

    return $categories;
}

public function getFacilities()
{
    $query = "
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>

        SELECT DISTINCT ?facility
        WHERE {
            ?cafe a caff:cafe ;
                  caff:hasfacility ?fac .

            # Ambil nama properti fasilitas yang bernilai TRUE
            ?fac ?predicate true .

            BIND(STRAFTER(STR(?predicate), \"#\") AS ?facility)
        }
        ORDER BY ?facility
    ";

    $response = Http::withHeaders([
        'Accept' => 'application/sparql-results+json',
        'Content-Type' => 'application/sparql-query'
    ])->withBody($query, 'application/sparql-query')
      ->post('http://localhost:3030/caffinder/sparql');

    $json = $response->json();

    if (!isset($json['results']['bindings'])) return [];

    $facilities = [];
    foreach ($json['results']['bindings'] as $row) {
        $facilities[] = $row['facility']['value']; // contoh: wifi, laptop_friendly
    }

    return $facilities;
}

    /**
     * SEARCH (q + category)
     * NOW PAKAI FILTER DI SPARQL → lebih efisien dari filter di Laravel
     */
public function searchCafes($keyword = null, $category = null, $district = null, $facilities = [], $priceRange = null, $order = null)
{
    $query = "
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>
        PREFIX xsd:  <http://www.w3.org/2001/XMLSchema#>

        SELECT ?cafe ?name ?alamat ?fotoUrl ?kategoriLabel ?rating ?district ?hargaMin ?hargaMax
        WHERE {
            ?cafe a caff:cafe ;
                  caff:nama_cafe ?name ;
                  caff:hasaddress ?alamatNode ;
                  caff:hasphoto ?fotoNode .

            ?alamatNode caff:nama_alamat ?alamat .
            OPTIONAL { ?fotoNode caff:foto_url ?fotoUrl . }

            OPTIONAL { 
                ?cafe caff:hascategory ?kategoriNode .
                OPTIONAL { ?kategoriNode caff:label ?kategoriLabel . }
            }

            OPTIONAL { ?cafe caff:rating ?rating . }

            OPTIONAL { 
                ?cafe caff:hasprice ?priceNode .
                OPTIONAL { ?priceNode caff:harga_min ?hargaMin . }
                OPTIONAL { ?priceNode caff:harga_max ?hargaMax . }
            }

            # Ekstraksi kecamatan
            BIND(
                IF(
                    REGEX(?alamat, \"Kec\\\\.\"),
                    REPLACE(?alamat, \".*Kec\\\\. ([^,]+),.*\", \"$1\"),
                    REPLACE(?alamat, \".*Medan ([A-Za-z]+).*\", \"Medan $1\")
                ) AS ?district
            )
    ";

    // Keyword filter
    if ($keyword) {
        $query .= " FILTER(CONTAINS(LCASE(?name), LCASE(\"$keyword\"))). ";
    }

    // Category filter
    if ($category) {
        $query .= " FILTER(STRAFTER(STR(?kategoriNode), \"#\") = \"$category\"). ";
    }

    // District filter
    if ($district) {
        $query .= " FILTER(CONTAINS(LCASE(?district), LCASE(\"$district\"))). ";
    }

    // Facility filter
    if (!empty($facilities)) {
        $query .= " ?cafe caff:hasfacility ?facNode . ";
        foreach ($facilities as $f) {
            $query .= " ?facNode caff:$f true . ";
        }
    }

    // Harga filter
   if ($priceRange) {
    if ($priceRange == '1-50000') {
    $query .= "
        FILTER(
            BOUND(?hargaMin) && BOUND(?hargaMax) &&
            xsd:int(?hargaMin) < 50000 &&
            xsd:int(?hargaMax) >= 1
        ).
    ";
}

    if ($priceRange == '50000-75000') {
    $query .= "
        FILTER(
            BOUND(?hargaMin) && BOUND(?hargaMax) &&
            xsd:int(?hargaMin) <= 75000 &&
            xsd:int(?hargaMax) >= 50000
        ).
    ";
}
    if ($priceRange == '75000-100000') {
    $query .= "
        FILTER(
            BOUND(?hargaMin) && BOUND(?hargaMax) &&
            xsd:int(?hargaMin) <= 100000 &&
            xsd:int(?hargaMax) >= 75000
        ).
    ";
}


}



    $query .= "} ";   // TUTUP WHERE

    // ORDER BY (TIDAK ADA DUPLIKAT)
    if ($order == 'rating_desc') {
        $query .= " ORDER BY DESC(?rating) ";
    }
    else if ($order == 'rating_asc') {
        $query .= " ORDER BY ASC(?rating) ";
    }
    else {
        $query .= " ORDER BY ?name ";
    }

    // Eksekusi SPARQL
    $response = Http::withBody($query, 'application/sparql-query')
        ->withHeaders(['Accept' => 'application/sparql-results+json'])
        ->post('http://localhost:3030/caffinder/sparql');

    $json = $response->json();

    if (!$json || !isset($json['results']['bindings'])) {
        return [];
    }

    $cafes = [];

    foreach ($json['results']['bindings'] as $row) {
        $uri = $row['cafe']['value'];
        $id = explode('#', $uri)[1];

        $cafes[] = [
            'id'        => $id,
            'name'      => $row['name']['value'],
            'address'   => $row['alamat']['value'],
            'image'     => $row['fotoUrl']['value'] ?? null,
            'category'  => $row['kategoriLabel']['value'] ?? null,
            'rating'    => $row['rating']['value'] ?? null,
            'district'  => $row['district']['value'] ?? null,
        ];
    }

    return $cafes;
}


private function extractDistrict($address)
{
    // 1️⃣ Cek apakah format alamat mengandung "Kec. xxxx"
    if (preg_match('/Kec\. ([^,]+)/i', $address, $match)) {
        return "Medan " . trim($match[1]); 
    }

    // 2️⃣ Kalau tidak ada "Kec.", baru pakai format "Medan xxxx"
    if (preg_match('/Medan\s+([A-Za-z]+)/i', $address, $match)) {
        return "Medan " . trim($match[1]);
    }

    // 3️⃣ Kalau tidak ketemu dua-duanya
    return null;
}

public function getDistricts()
{
    $query = "
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>

        SELECT DISTINCT ?kecamatanFinal
        WHERE {
            ?cafe a caff:cafe ;
                  caff:hasaddress ?alamatNode .

            ?alamatNode caff:nama_alamat ?alamat .

            # 1️⃣ Jika ada 'Kec. Xxxxx' → ambil Xxxxx saja (tanpa 'Medan')
            BIND(
                IF(
                    REGEX(?alamat, \"Kec\\\\.\"),
                    REPLACE(?alamat, \".*Kec\\\\. ([^,]+).*\", \"$1\"),
                    \"\"
                )
                AS ?kecFromKec
            )

            # 2️⃣ Jika tidak ada 'Kec.' → fallback cari 'Medan Yyyyy'
            BIND(
                IF(
                    ?kecFromKec != \"\",
                    ?kecFromKec,
                    IF(
                        REGEX(?alamat, \"Medan [A-Za-z]+\"),
                        REPLACE(?alamat, \".*Medan ([A-Za-z]+).*\", \"Medan $1\"),
                        \"\"
                    )
                )
                AS ?kecamatanFinal
            )
        }
        ORDER BY ?kecamatanFinal
    ";

    $response = Http::withBody($query, 'application/sparql-query')
        ->withHeaders(['Accept' => 'application/sparql-results+json'])
        ->post('http://localhost:3030/caffinder/sparql');

    $json = $response->json();

    if (!$json || !isset($json['results']['bindings'])) {
        return [];
    }

    $districts = [];

    foreach ($json['results']['bindings'] as $row) {
        $value = trim($row['kecamatanFinal']['value']);
        if ($value !== "") {
            $districts[] = $value;
        }
    }

    return array_unique($districts);
}


        
    public function smartSearchWithDistrict($filters, $keyword)
{
    $category = $filters['category'] ?? null;
    $district = $filters['district'] ?? null;

    $query = "
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>

        SELECT ?cafe ?name ?alamat ?fotoUrl ?kategoriLabel ?rating ?district
        WHERE {
            ?cafe a caff:cafe ;
                  caff:nama_cafe ?name ;
                  caff:hasaddress ?alamatNode ;
                  caff:hasphoto ?fotoNode .

            ?alamatNode caff:nama_alamat ?alamat .
            OPTIONAL { ?fotoNode caff:foto_url ?fotoUrl . }

            OPTIONAL { 
                ?cafe caff:hascategory ?kategoriNode .
                OPTIONAL { ?kategoriNode caff:label ?kategoriLabel . }
            }

            OPTIONAL { ?cafe caff:rating ?rating . }

            # Ekstrak Kecamatan dari alamat
            BIND(
                REPLACE(?alamat, \".*Medan ([A-Za-z]+).*\", \"Medan $1\")
                AS ?district
            )
    ";

    // ========================= FILTER ==============================
    if (!empty($keyword)) {
        $query .= " FILTER(CONTAINS(LCASE(?name), LCASE(\"$keyword\"))). ";
    }

    if (!empty($category)) {
        $query .= " FILTER(STRAFTER(STR(?kategoriNode), \"#\") = \"$category\"). ";
    }

    if (!empty($district)) {
        $query .= " FILTER(CONTAINS(LCASE(?district), LCASE(\"$district\"))). ";
    }

    $query .= "}
        ORDER BY ?name
    ";

    // ===================== EXEC FUSEKI =============================
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Content-Type' => 'application/sparql-query',
        'Accept' => 'application/sparql-results+json'
    ])
    ->withBody($query, 'application/sparql-query')
    ->post('http://localhost:3030/caffinder/sparql');

    if ($response->failed()) {
        return [];
    }

    $json = $response->json();
    if (!isset($json['results']['bindings'])) {
        return [];
    }

    // ===================== PARSE HASIL =============================
    $cafes = [];

    foreach ($json['results']['bindings'] as $row) {
        $uri = $row['cafe']['value'];
        $id  = explode('#', $uri)[1];

        $cafes[] = [
            'id'       => $id,
            'name'     => $row['name']['value'],
            'address'  => $row['alamat']['value'],
            'district' => $row['district']['value'] ?? null,
            'image'    => $row['fotoUrl']['value'] ?? '/images/default.jpg',
            'category' => $row['kategoriLabel']['value'] ?? 'Umum',
            'rating'   => $row['rating']['value'] ?? null,
        ];
    }

    return $cafes;
}

    }