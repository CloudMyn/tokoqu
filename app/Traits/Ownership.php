<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Ownership
{
    public static function getEloquentQuery(): Builder
    {
        parent::getEloquentQuery();

        if (cek_admin_role()) return parent::getEloquentQuery();

        $store   =   get_context_store();

        if (cek_store_role() && (self::$ownership_column_name ?? null) == 'owner_id')
            return parent::getEloquentQuery()
                ->where('owner_id', get_auth_user()?->owner_store?->id);

        return parent::getEloquentQuery()->where('store_code', $store?->code);
    }
}
