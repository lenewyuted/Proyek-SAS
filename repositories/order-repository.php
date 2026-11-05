<?php
// Membuat nomor order baru, contoh: TH202611050123
function generateOrderNumber()
{
    return 'TH' . date('Ymd') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
}
?>
