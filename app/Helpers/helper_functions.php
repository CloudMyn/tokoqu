<?php

use App\Models\Store;
use App\Models\StoreAsset;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

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


        abort(403, 'Pengguna Tidak Terauntentikasi');
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
        if (!auth()->user()) return false;

        return get_auth_user()->has_role('store_owner');
    }
}

if (!function_exists('cek_store_exists')) {
    /**
     * Dapatkan toko berdasarkan kode toko
     *
     * @return |bool
     */
    function cek_store_exists(): bool
    {
        if (!auth()->user()) return false;

        return get_context_store() instanceof Store;
    }
}

if (!function_exists('cek_store_employee_role')) {
    /**
     * Dapatkan toko berdasarkan kode toko
     *
     * @return |bool
     */
    function cek_store_employee_role(): bool
    {
        return get_auth_user()->has_role('store_employee');
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

        foreach (
            [
                'karton',
                'bungkus',
                'pcs',
                'kotak',
                'kantong',
                'set',
                'botol',
                'toples',
                'kardus',
                'pallet',
                'bundle',
                'liter',
                'mililiter',
                'kilogram',
                'gram',
                'lembar',
                'sak',
                'roll',
            ] as  $unit
        ) {

            $list[$unit]   =   strtoupper($unit);
        }

        return $list;
    }
}


if (!function_exists('get_context_store')) {
    /**
     * Dapatkan product toko pengguna dengan cache
     *
     * @return Store|null
     */
    function get_context_store(bool $clear_cache = false): Store|null
    {
        $auth_user  =   get_auth_user();

        // Buat cache key berdasarkan ID pengguna
        if (cek_store_role()) {
            return $auth_user->owner_store->store()->first();
        }

        if (cek_store_employee_role()) {
            return $auth_user->employee?->store()?->first();
        }

        abort(404, 'Perizinan Gagal');
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


if (!function_exists('add_store_asset')) {
    /**
     * Input kase toko
     *
     * @return
     */
    function add_store_asset(
        Store $store = null,
        string  $title,
        string  $message,
        int|null $amount,
        string $type,
    ): StoreAsset|null {

        if ($amount == 0 || !$amount) return null;

        if ($amount < 0) {
            throw new \Exception('Nilai nominal tidak boleh negatif');
        }

        $asset    =   new StoreAsset();

        $asset->store()->associate($store ?? get_context_store());

        $asset->type    =   $type;
        $asset->amount  =   $amount;
        $asset->title   =   $title;
        $asset->message =   $message;

        $asset->save();

        return $asset;
    }
}

if (!function_exists('delete_store_asset')) {
    /**
     * delet store asset
     *
     * @return
     */
    function delete_store_asset(
        string $title
    ) {
        return StoreAsset::where('title', $title)->delete();
    }
}

if (!function_exists('get_suppliers')) {
    /**
     * sync store assets
     *
     * @return array
     */
    function get_suppliers(): array
    {
        $store  =   get_context_store();

        return $store->suppliers()->pluck('name', 'id')->toArray();
    }
}

if (!function_exists('sync_store_assets')) {
    /**
     * sync store assets
     *
     * @return void
     */
    function sync_store_assets()
    {
        $store  =   get_context_store();

        $store->update([
            'assets'   =>  $store->store_assets()->where('type', 'in')->sum('amount') - $store->store_assets()->where('type', 'out')->sum('amount')
        ]);
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
function ubah_angka_int_ke_rupiah(int $angka = null, bool $with_format = true): string
{
    $angka = $angka ?? 0;

    if (!$with_format) {
        return number_format($angka, 0, '.', ',');
    }

    if ($angka >= 1_000_000_000_000) {
        // Jika angka lebih dari atau sama dengan 1 triliun
        $angka_triliun = $angka / 1_000_000_000_000;
        return number_format($angka_triliun, 2, '.', ',') . ' T';
    } elseif ($angka >= 1_000_000_000) {
        // Jika angka lebih dari atau sama dengan 1 miliar
        $angka_miliar = $angka / 1_000_000_000;
        return number_format($angka_miliar, 2, '.', ',') . ' M';
    } elseif ($angka >= 100_000_000) {
        // Jika angka lebih dari atau sama dengan 100 juta
        $angka_juta = $angka / 1_000_000;
        return number_format($angka_juta, 0, '.', ',') . ' JT';
    }

    // Jika angka kurang dari 100 juta
    return number_format($angka, 0, '.', ',');
}
