<?php

use App\Models\Store;
use App\Models\User;

if (!function_exists('format_rupiah')) {
    /**
     * Format a number as Indonesian Rupiah currency.
     *
     * @param float|int $amount
     * @param bool $prefix
     * @return string
     */
    function format_rupiah($amount, $prefix = true)
    {
        $formatted = number_format($amount, 2, ',', '.');
        return $prefix ? 'Rp ' . $formatted : $formatted;
    }
}


if (!function_exists('get_auth_user')) {
    /**
     * Generate a 6-digit employee code.
     *
     * @param int $number
     * @return \App\Models\User
     */
    function get_auth_user(): User
    {
        $user = auth()->user();

        if ($user instanceof User) return $user;

        abort(403, 'Unauthorized');
    }
}

if (!function_exists('generate_code')) {
    /**
     * Generate a 6-digit employee code.
     *
     * @param int $number
     * @return string
     */
    function generate_code($number, $prefix = "")
    {
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_store_code')) {

    /**
     * Generate a 6-letter store code.
     *
     * @param int $length
     * @return string
     */
    function generate_store_code($length = 4)
    {
        do {

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

            $code = '';

            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }

            // ...
        } while (get_store_by_code($code) instanceof Store);

        return $code;
    }
}


if (!function_exists('generate_phone_verify_code')) {
    /**
     * Generate a numeric phone verification code.
     *
     * @param int $length
     * @return string
     */
    function generate_phone_verify_code($length = 6)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= rand(0, 9);
        }
        return $code;
    }
}



if (!function_exists('get_store_by_code')) {
    /**
     * Dapatkan toko berdasarkan kode toko
     *
     * @param string $code
     * @return \App\Models\Store|bool
     */
    function get_store_by_code(string $code): Store|bool
    {
        $store  =   Store::where('code', $code)->first();

        if ($store instanceof Store) return $store;

        return false;
    }
}


if (!function_exists('get_user_directory')) {
    /**
     * Dapatkan toko berdasarkan kode toko
     *
     * @return \App\Models\Store|bool
     */
    function get_user_directory(string $suffix = null): string
    {
        $user = get_auth_user();

        $prefix =   $user->id;

        $sp = DIRECTORY_SEPARATOR;

        return $prefix . $sp . $suffix;
    }
}


if (!function_exists('get_store_directory')) {
    /**
     * Dapatkan toko berdasarkan kode toko
     *
     * @return \App\Models\Store|bool
     */
    function get_store_directory(Store $store): string
    {
        $user_dir = get_user_directory();

        return $user_dir . $store->code;
    }
}


if (!function_exists('cek_store_role')) {
    /**
     * Dapatkan toko berdasarkan kode toko
     *
     * @return |bool
     */
    function cek_store_role(): bool
    {
        return get_auth_user()->has_role('store_owner');
    }
}


if (!function_exists('cek_admin_role')) {
    /**
     * Dapatkan toko berdasarkan kode toko
     *
     * @return |bool
     */
    function cek_admin_role(): bool
    {
        return get_auth_user()->has_role('admin');
    }
}


if (!function_exists('get_store_list')) {
    /**
     * Dapatkan daftar toko pengguna
     *
     * @return |bool
     */
    function get_store_list(): array
    {
        $auth_user = get_auth_user();

        $store_list = [];

        if (cek_store_role()) {

            foreach ($auth_user?->owner_store?->store ?? [] as  $value) {
                $store_list[$value->code]   =   $value->name;
            }
        } else if (cek_admin_role()) {
            foreach (Store::all() ?? [] as  $value) {
                $store_list[$value->code]   =   $value->name;
            }
        }

        return $store_list;
    }
}


if (!function_exists('get_product_list')) {
    /**
     * Dapatkan product toko pengguna
     *
     * @return
     */
    function get_product_list(bool $get_have_stock = false): array
    {
        $list = [];

        foreach (get_context_store()->products ?? [] as  $value) {

            if ($get_have_stock && intval($value->stock) <= 0) continue;

            $list[$value->id]   =   strtoupper($value->name . " | " . $value->sku . " | Rp. " . number_format($value->sale_price) . " ( $value->stock )");
        }

        return $list;
    }
}


if (!function_exists('get_unit_list')) {
    /**
     * Dapatkan product toko pengguna
     *
     * @return
     */
    function get_unit_list(): array
    {
        $list = [];

        foreach ([
            'carton', 'pack', 'piece', 'box', 'bag',
            'set', 'bottle', 'jar', 'roll', 'case', 'pallet',
            'bundle', 'liter', 'milliliter', 'kilogram', 'gram'
        ] as  $unit) {

            $list[$unit]   =   strtoupper($unit);
        }

        return $list;
    }
}


if (!function_exists('get_context_store')) {
    /**
     * Dapatkan product toko pengguna
     *
     * @return
     */
    function get_context_store(): Store|null
    {
        $auth_user = get_auth_user();

        return  $auth_user->owner_store->store()->first();
    }
}


if (!function_exists('get_chart_type')) {
    /**
     * Dapatkan product toko pengguna
     *
     * @return
     */
    function get_chart_type(): string
    {
        $available_charts   =   ['line', 'bar', 'pie', 'bubble', 'doughnut', 'radar', 'scatter', 'polarArea'];

        return $available_charts[1];
    }
}

/**
 * Ubah angka dengan format rupiah menjadi integer
 *
 * Contoh:
 * - 100,000,000 akan menjadi 100000000
 * - 100000000 juga akan menjadi 100000000
 *
 * @param string|int $angka Angka dengan format rupiah
 * @return int Angka dalam bentuk integer
 */
function ubah_angka_rupiah_ke_int(string|int $angka): int
{
    return intval(str_replace(',', '', $angka ?? ''));
}

/**
 * Ubah angka integer menjadi format rupiah
 *
 * Contoh:
 * - 100000000 akan menjadi 100,000,000
 *
 * @param int $angka Angka dalam bentuk integer
 * @return string Angka dalam bentuk rupiah
 */
function ubah_angka_int_ke_rupiah(int $angka = null): string
{
    return number_format($angka ?? 0, 0, '.', ',');
}
