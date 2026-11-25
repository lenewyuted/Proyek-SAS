<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$defaultBackUrl = '../../pages/products/recommendation.php';
$backUrl = $defaultBackUrl;

if (!empty($_SERVER['HTTP_REFERER'])) {
    $refPath = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    if (
        is_string($refPath) &&
        $refPath !== '' &&
        strpos($refPath, '/pages/products/recommendation.php') !== false
    ) {
        $backUrl = $refPath;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $backUrl);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$needMap = [
    'gaming' => 'gaming',
    'design' => 'design & editing',
    'study'  => 'study & work',
];

$appsMap = [
    'heavy'  => 'demanding games',
    'light'  => 'light games',
    'office' => 'office applications',
];

$usageMap = [
    'most' => 'most of the day',
    'few'  => 'a few hours a day',
    'rare' => 'occasionally',
];

$input = [
    'need'   => trim((string) ($_POST['need'] ?? '')),
    'budget' => trim((string) ($_POST['budget'] ?? '')),
    'apps'   => trim((string) ($_POST['apps'] ?? '')),
    'usage'  => trim((string) ($_POST['usage'] ?? '')),
];

$_SESSION['rec_input'] = $input;

$errors = [];
if (!isset($needMap[$input['need']]))                          $errors['need']   = 'Please choose an answer.';
if (!in_array($input['budget'], ['low', 'mid', 'high'], true)) $errors['budget'] = 'Please choose an answer.';
if (!isset($appsMap[$input['apps']]))                          $errors['apps']   = 'Please choose an answer.';
if (!isset($usageMap[$input['usage']]))                        $errors['usage']  = 'Please choose an answer.';

if (!empty($errors)) {
    $_SESSION['rec_errors'] = $errors;
    unset($_SESSION['rec_result']);
    header('Location: ' . $backUrl . '#rec-form');
    exit;
}

unset($_SESSION['rec_errors']);

$params = [];
$matchedProducts = [];
$dbError = false;

try {

    $sql = "SELECT * FROM products WHERE 1=1";

    $sql .= " AND description LIKE :desc_need";
    $params['desc_need'] = '%' . strtolower($needMap[$input['need']]) . '%';

    $sql .= " AND description LIKE :desc_apps";
    $params['desc_apps'] = '%' . strtolower($appsMap[$input['apps']]) . '%';

    $sql .= " AND description LIKE :desc_usage";
    $params['desc_usage'] = '%' . strtolower($usageMap[$input['usage']]) . '%';

    switch ($input['budget']) {
        case 'low':
            $sql .= " AND price < :price_max";
            $params['price_max'] = 5000000;
            break;
        case 'mid':
            $sql .= " AND price >= :price_min AND price <= :price_max";
            $params['price_min'] = 5000000;
            $params['price_max'] = 10000000;
            break;
        case 'high':
            $sql .= " AND price > :price_min";
            $params['price_min'] = 10000000;
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $matchedProducts = $stmt->fetchAll();
} catch (Throwable $ex) {
    $dbError = true;
}

$_SESSION['rec_result'] = [
    'submitted' => true,
    'dbError'   => $dbError,
    'products'  => $matchedProducts,
];

header('Location: ' . $backUrl . '#results');
exit;
