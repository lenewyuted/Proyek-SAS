<?php


session_start();

$baseUrl = '/Proyek-SAS';
//index.php

$products = [
    ['id' => 1, 'name' => 'POWER SUPPLY MSI MAG A650BN', 'category' => 'Power Supplies', 'price' => 880000, 'stock' => 12],
    ['id' => 2, 'name' => 'RAM DDR5 16GB',               'category' => 'RAM & Storage',   'price' => 950000, 'stock' => 8],
];

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function rupiah(int $amount): string
{
    return 'Rp.' . number_format($amount, 0, ',', '.');
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Produk - TechHub Admin</title>
  <style>
    body { font-family: sans-serif; margin: 24px; }
    .head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .btn { padding: 6px 14px; border-radius: 4px; border: none; color: #fff; cursor: pointer; text-decoration: none; font-size: 14px; }
    .btn-add { background: #0a4bb5; }
    .btn-edit { background: #e6a100; }
    .btn-delete { background: #c62828; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
    th { background: #e3ecff; }
    .actions { display: flex; gap: 6px; }
    .alert { padding: 8px 12px; border-radius: 4px; margin-bottom: 12px; }
    .alert-success { background: #e6f6ea; color: #14702d; }
    .alert-error { background: #fdeaea; color: #a11a1a; }
  </style>
</head>
<body>

  <?php // require_once __DIR__ . '/../../../components/admin/sidebar.php'; ?>
  <?php // require_once __DIR__ . '/../../../components/admin/topbar.php'; ?>

  <div class="head">
    <h1>Daftar Produk</h1>
    <a href="create.php" class="btn btn-add">+ Tambah Produk</a>
  </div>

  <?php if ($flashSuccess): ?>
    <p class="alert alert-success"><?= e($flashSuccess) ?></p>
  <?php endif; ?>
  <?php if ($flashError): ?>
    <p class="alert alert-error"><?= e($flashError) ?></p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
        <tr><td colspan="6">Belum ada produk.</td></tr>
      <?php else: ?>
        <?php foreach ($products as $i => $product): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= e($product['name']) ?></td>
            <td><?= e($product['category']) ?></td>
            <td><?= rupiah($product['price']) ?></td>
            <td><?= (int) $product['stock'] ?></td>
            <td>
              <div class="actions">
                <a href="edit.php?id=<?= (int) $product['id'] ?>" class="btn btn-edit">Edit</a>

                <form action="<?= $baseUrl ?>/actions/products/destroy.php" method="post"
                      onsubmit="return confirm('Yakin hapus produk ini?');">
                  <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                  <button type="submit" class="btn btn-delete">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>