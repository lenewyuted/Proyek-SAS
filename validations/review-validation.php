<?php

function validateProductReview(int $productId, int $rating, string $comment): array
{
    $errors = [];

    if ($productId <= 0) {
        $errors[] = 'Produk tidak valid.';
    }

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Pilih rating bintang antara 1 sampai 5.';
    }

    $trimmedComment = trim($comment);
    if (empty($trimmedComment)) {
        $errors[] = 'Komentar tidak boleh kosong.';
    } elseif (mb_strlen($trimmedComment) < 5) {
        $errors[] = 'Komentar minimal terdiri dari 5 karakter.';
    } elseif (mb_strlen($trimmedComment) > 1000) {
        $errors[] = 'Komentar maksimal 1000 karakter.';
    }

    return $errors;
}
