<?php

return [

    'column_toggle' => [

        'heading' => 'Kolom',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Tampilkan :count kurang',
                'expand_list' => 'Tampilkan :count lebih',
            ],

            'more_list_items' => 'dan :count lebih',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Pilih/desisal semua item untuk aksi massal.',
        ],

        'bulk_select_record' => [
            'label' => 'Pilih/desisal item :key untuk aksi massal.',
        ],

        'bulk_select_group' => [
            'label' => 'Pilih/desisal grup :title untuk aksi massal.',
        ],

        'search' => [
            'label' => 'Pencarian',
            'placeholder' => 'Pencarian',
            'indicator' => 'Pencarian',
        ],

    ],

    'summary' => [

        'heading' => 'Ringkasan',

        'subheadings' => [
            'all' => 'Semua :label',
            'group' => 'Ringkasan :group',
            'page' => 'Halaman ini',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Rata-rata',
            ],

            'count' => [
                'label' => 'Jumlah',
            ],

            'sum' => [
                'label' => 'Jumlah',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Selesai memindahkan data',
        ],

        'enable_reordering' => [
            'label' => 'Memindahkan data',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Grup',
        ],

        'open_bulk_actions' => [
            'label' => 'Aksi massal',
        ],

        'toggle_columns' => [
            'label' => 'Tampilkan/sembunyikan kolom',
        ],

    ],

    'empty' => [

        'heading' => 'Tidak ada :model',

        'description' => 'Buat :model untuk memulai.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Terapkan filter',
            ],

            'remove' => [
                'label' => 'Hapus filter',
            ],

            'remove_all' => [
                'label' => 'Hapus semua filter',
                'tooltip' => 'Hapus semua filter',
            ],

            'reset' => [
                'label' => 'Reset',
            ],

        ],

        'heading' => 'Filter',

        'indicator' => 'Filter aktif',

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [
            'placeholder' => 'Semua',
        ],

        'trashed' => [

            'label' => 'Data terhapus',

            'only_trashed' => 'Hanya data terhapus',

            'with_trashed' => 'Dengan data terhapus',

            'without_trashed' => 'Tanpa data terhapus',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grup',
                'placeholder' => 'Grup',
            ],

            'direction' => [

                'label' => 'Grup arah',

                'options' => [
                    'asc' => 'Naik',
                    'desc' => 'Turun',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Sorot dan drag data untuk mengatur urutan.',

    'selection_indicator' => [

        'selected_count' => '1 data di pilih|:count data di pilih',

        'actions' => [

            'select_all' => [
                'label' => 'Pilih semua :count',
            ],

            'deselect_all' => [
                'label' => 'Desisal semua',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Urutkan',
            ],

            'direction' => [

                'label' => 'Grup arah',

                'options' => [
                    'asc' => 'Naik',
                    'desc' => 'Turun',
                ],

            ],

        ],

    ],

];

