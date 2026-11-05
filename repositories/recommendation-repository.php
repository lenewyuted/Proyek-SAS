<?php
require_once __DIR__ . "/product-repository.php";

// Mengambil jawaban dari URL (GET), hanya nilai yang ada di daftar opsi yang diterima
function getRecommendationAnswers($request, $questions)
{
    $answers = [];

    foreach ($questions as $key => $question) {
        $picked = isset($request[$key]) && is_array($request[$key]) ? $request[$key] : [];
        $answers[$key] = array_values(array_intersect($picked, array_keys($question['options'])));
    }

    return $answers;
}

// Mengambil produk rekomendasi: difilter berdasarkan budget, lalu diurutkan berdasarkan skor kecocokan
function getRecommendedProducts($answers, $submitted, $budgetRange, $purposeCategories, $limit = 5)
{
    $all = getAllProducts();
    $products = $all;

    if (!$submitted) {
        // Belum submit: tampilkan produk dengan rating tertinggi
        usort($products, fn($a, $b) => (float) $b['rating'] <=> (float) $a['rating']);

        return array_slice($products, 0, $limit);
    }

    // 1. Filter anggaran (gabungan semua rentang yang dipilih)
    if ($answers['budget']) {
        $products = array_filter($all, function ($product) use ($answers, $budgetRange) {
            foreach ($answers['budget'] as $budget) {
                [$min, $max] = $budgetRange[$budget];

                if ($product['price'] >= $min && $product['price'] <= $max) {
                    return true;
                }
            }

            return false;
        });
    }

    // 2. Skor kecocokan
    $maxPrice = max(array_column($all, 'price') ?: [1]);
    $maxPrice = $maxPrice > 0 ? $maxPrice : 1; // hindari pembagian dengan nol

    foreach ($products as &$product) {
        $score = 0;

        foreach ($answers['purpose'] as $purpose) {
            if (in_array($product['category_name'], $purposeCategories[$purpose], true)) {
                $score += 3;
            }
        }

        $level = $product['price'] / $maxPrice; // 0..1 (proxy performa)

        if (in_array('demanding', $answers['games'], true)) {
            $score += $level * 3;
        }
        if (in_array('light', $answers['games'], true)) {
            $score += (1 - abs($level - 0.4)) * 2;
        }
        if (in_array('office', $answers['games'], true)) {
            $score += (1 - $level) * 2;
        }
        if (in_array('most', $answers['usage'], true)) {
            $score += (float) $product['rating']; // pemakaian berat -> utamakan rating
        }
        if (in_array('few', $answers['usage'], true)) {
            $score += (float) $product['rating'] * 0.6;
        }

        $product['score'] = $score + (float) $product['rating'] * 0.1;
    }
    unset($product);

    $products = array_values($products);
    usort($products, fn($a, $b) => $b['score'] <=> $a['score']);

    return array_slice($products, 0, $limit);
}
?>
