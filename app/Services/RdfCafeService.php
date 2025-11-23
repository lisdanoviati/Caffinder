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
        LIMIT 6
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


    /**
     * SEARCH (q + category)
     * NOW PAKAI FILTER DI SPARQL → lebih efisien dari filter di Laravel
     */
    public function searchCafes($keyword, $category = null, $district = null)
{
    $query = "
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>

        SELECT ?cafe ?name ?alamat ?fotoUrl ?kategoriLabel ?rating
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

            # Ekstrak kecamatan dari alamat
            BIND(
                REPLACE(?alamat, \".*Medan ([A-Za-z]+).*\", \"Medan $1\")
                AS ?district
            )
    ";

    // -------------------- FILTER --------------------
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
            'id'       => $id,
            'name'     => $row['name']['value'],
            'address'  => $row['alamat']['value'],
            'district' => $row['district']['value'] ?? null,
            'image'    => $row['fotoUrl']['value'] ?? '/images/default-cafe.jpg',
            'category' => $row['kategoriLabel']['value'] ?? 'Umum',
            'rating'   => $row['rating']['value'] ?? null
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