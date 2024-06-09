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
