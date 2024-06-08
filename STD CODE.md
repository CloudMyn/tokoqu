# Standar Penulisan Sintaks Kode PHP

## A. Penamaan

### 1. Variabel
- **Snake Case** untuk variabel lokal: `$this_is_variable`
- **Camel Case** untuk variabel global: `$thisIsVariable`

### 2. Fungsi
- **Snake Case** untuk penamaan fungsi: `this_is_function`
- Fungsi privat atau internal diawali dengan underscore: `_this_is_private_function`

### 3. Kelas
- **Pascal Case** untuk nama kelas: `ThisIsClass`

### 4. Konstanta
- **Upper Case dengan Underscore** untuk konstanta: `THIS_IS_CONSTANT`

## B. Struktur Fungsi

### 1. Fungsi Umum
```php
/**
 * Deskripsi singkat tentang fungsi.
 *
 * @param tipe $param1 Penjelasan param1
 * @param tipe $param2 Penjelasan param2
 * @return tipe Penjelasan tentang nilai yang dikembalikan
 */
function this_is_function($param1, $param2) {
    // Logika fungsi
    $result = $param1 + $param2;
    return $result;
}
```

## 2. Fungsi Private
```php
/**
 * Deskripsi singkat tentang fungsi privat.
 *
 * @param tipe $param1 Penjelasan param1
 * @param tipe $param2 Penjelasan param2
 * @return tipe Penjelasan tentang nilai yang dikembalikan
 */
function _this_is_private_function($param1, $param2) {
    // Logika fungsi privat
    $result = $param1 * $param2;
    return $result;
}
```
