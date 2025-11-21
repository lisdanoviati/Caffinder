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
        LIMIT 9
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
    public function searchCafes($keyword, $category = null)
{
    $query = "
        PREFIX caff: <http://www.semanticweb.org/lenovo/ontologies/2025/10/caffinder#>

        SELECT ?cafe ?name ?alamat ?fotoUrl ?kategori ?rating
        WHERE {
            ?cafe a caff:cafe ;
                  caff:nama_cafe ?name ;
                  caff:hasaddress ?alamatNode ;
                  caff:hasphoto ?fotoNode .

            ?alamatNode caff:nama_alamat ?alamat .
            OPTIONAL { ?fotoNode caff:foto_url ?fotoUrl . }

            OPTIONAL { ?cafe caff:hascategory ?kategori . }
            OPTIONAL { ?cafe caff:rating ?rating . }
    ";

    // -------------------------------------------------
    // 🔥 Tambahan filter kategori (TANPA ganggu search)
    // -------------------------------------------------
    if (!empty($category)) {
    $query .= " FILTER(STRAFTER(STR(?kategori), \"#\") = \"$category\") ";
}


    // -------------------------------------------------
    // 🔍 Searching (biarin tetap seperti versi kamu)
    // -------------------------------------------------
    if (!empty($keyword)) {
        $query .= " FILTER(CONTAINS(LCASE(?name), LCASE(\"$keyword\"))) ";
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
            'id'      => $id,
            'name'    => $row['name']['value'],
            'address' => $row['alamat']['value'],
            'image'   => $row['fotoUrl']['value'] ?? null,
            'category'=> $row['kategori']['value'] ?? null,
            'rating'  => $row['rating']['value'] ?? null,
        ];
    }

    return $cafes;
}


public function smartSearch($filters, $raw)
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
            OPTIONAL { ?cafe caff:rating ?rating . }
            OPTIONAL { 
                ?cafe caff:hascategory ?kategoriNode .
                OPTIONAL { ?kategoriNode caff:label ?kategoriLabel . }
            }
    ";

    // === NLP Rules ===

    // Filter category (kalau dari NLP)
    if (!empty($filters['category'])) {
        $query .= " ?cafe caff:hascategory caff:$filters[category] . ";
    }

    if (!empty($filters['wifi'])) {
        $query .= "
            ?cafe caff:hasfacility ?facNode .
            ?facNode caff:wifi true .
        ";
    }

    if (!empty($filters['murah'])) {
        $query .= " 
            ?cafe caff:hasprice ?priceNode .
            ?priceNode caff:min_price ?min .
            FILTER(?min < 30000)
        ";
    }

    if (!empty($filters['laptop'])) {
        $query .= "
            ?cafe caff:hasfacility ?facNode2 .
            ?facNode2 caff:laptop_friendly true .
        ";
    }

    if (!empty($filters['specialty'])) {
        $query .= " ?cafe caff:hascategory caff:kategori_specialty . ";
    }

    if (!empty($filters['instagramable'])) {
        $query .= " ?cafe caff:hascategory caff:kategori_aesthetic . ";
    }

    // ====================================================
    // 🆕 Searching nama café bebas TANPA stopwords
    // ====================================================
    if (!empty($raw)) {
        $words = explode(' ', $raw);

        foreach ($words as $word) {
            $word = trim($word);

            if (strlen($word) > 2) {
                $query .= " FILTER(CONTAINS(LCASE(?name), LCASE(\"$word\"))) ";
            }
        }
    }

    // finish query
    $query .= "} ORDER BY ?name";

    $response = Http::withBody($query, 'application/sparql-query')
        ->withHeaders(['Accept' => 'application/sparql-results+json'])
        ->post('http://localhost:3030/caffinder/sparql');

    $json = $response->json();

    if (!$json || !isset($json['results']['bindings'])) {
        return [];
    }

    $cafes = [];

    foreach ($json['results']['bindings'] as $row) {
        $id = explode('#', $row['cafe']['value'])[1];

        $cafes[] = [
            'id'       => $id,
            'name'     => $row['name']['value'],
            'address'  => $row['alamat']['value'],
            'image'    => $row['fotoUrl']['value'],
            'category' => $row['kategoriLabel']['value'] ?? null,
            'rating'   => $row['rating']['value'] ?? null,
        ];
    }

    return $cafes;
}

}