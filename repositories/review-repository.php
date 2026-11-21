<?php

function getReviewsByProductId(int $productId): array
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT r.id, r.user_id, r.rating, r.comment, u.name AS user_name
        FROM `products-rating` r
        INNER JOIN users u ON r.user_id = u.id
        WHERE r.product_id = :product_id
        ORDER BY r.id DESC
    ");
    $stmt->execute(['product_id' => $productId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductRatingSummary(int $productId): array
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_reviews,
            COALESCE(AVG(rating), 0) AS average_rating
        FROM `products-rating`
        WHERE product_id = :product_id
    ");
    $stmt->execute(['product_id' => $productId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'total_reviews'  => (int) ($result['total_reviews'] ?? 0),
        'average_rating' => round((float) ($result['average_rating'] ?? 0), 1),
    ];
}

function createProductReview(int $productId, int $userId, int $rating, string $comment): bool
{
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO `products-rating` (product_id, user_id, rating, comment)
        VALUES (:product_id, :user_id, :rating, :comment)
    ");

    return $stmt->execute([
        'product_id' => $productId,
        'user_id'    => $userId,
        'rating'     => $rating,
        'comment'    => trim($comment),
    ]);
}

function deleteProductReview(int $reviewId, int $userId, bool $isAdmin = false): bool
{
    global $pdo;

    if ($isAdmin) {
        $stmt = $pdo->prepare("DELETE FROM `products-rating` WHERE id = :id");
        $stmt->execute(['id' => $reviewId]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM `products-rating` WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'id'      => $reviewId,
            'user_id' => $userId,
        ]);
    }

    return $stmt->rowCount() > 0;
}
