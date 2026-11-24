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
if (!isset($needMap[$input['need']]))                           $errors['need']   = 'Please choose an answer.';
if (!in_array($input['budget'], ['low', 'mid', 'high'], true))  $errors['budget'] = 'Please choose an answer.';
if (!isset($appsMap[$input['apps']]))                           $errors['apps']   = 'Please choose an answer.';
if (!isset($usageMap[$input['usage']]))                         $errors['usage']  = 'Please choose an answer.';

if (!empty($errors)) {
    $_SESSION['rec_errors'] = $errors;
    unset($_SESSION['rec_result']);
    header('Location: ' . $backUrl . '#rec-form');
    exit;
}

unset($_SESSION['rec_errors']);

$conditions = [
    "description LIKE :desc_need",
    "description LIKE :desc_apps",
    "description LIKE :desc_usage",
];

$params = [
    ':desc_need'  => '%' . $needMap[$input['need']] . '%',
    ':desc_apps'  => '%' . $appsMap[$input['apps']] . '%',
    ':desc_usage' => '%' . $usageMap[$input['usage']] . '%',
];

if ($input['budget'] === 'low') {
    $conditions[]       = "price < :price_max";
    $params[':price_max'] = 5000000;
} elseif ($input['budget'] === 'mid') {
    $conditions[]       = "price >= :price_min AND price <= :price_max";
    $params[':price_min'] = 5000000;
    $params[':price_max'] = 10000000;
} elseif ($input['budget'] === 'high') {
    $conditions[]       = "price > :price_min";
    $params[':price_min'] = 10000000;
}

$matchedProducts = [];
$dbError = false;

try {
    global $pdo, $conn;
    $db = $pdo ?? $conn ?? null;

    if (!($db instanceof PDO)) {
        foreach (['getConnection', 'getDB', 'getDb', 'db'] as $fn) {
            if (function_exists($fn)) {
                $candidate = $fn();
                if ($candidate instanceof PDO) {
                    $db = $candidate;
                    break;
                }
            }
        }
    }

    if ($db instanceof PDO) {
        $sql  = "SELECT * FROM products WHERE " . implode(' AND ', $conditions);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $matchedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $dbError = true;
    }
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
