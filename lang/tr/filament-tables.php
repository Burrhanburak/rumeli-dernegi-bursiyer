<?php

return [
    'columns' => [
        'text' => [
            'more_list_items' => 've :count daha',
        ],
    ],
    'actions' => [
        'modal' => [
            'requires_confirmation_subheading' => 'Bu işlemi onayladığınızdan emin misiniz?',
        ],
    ],
    'empty' => [
        'heading' => 'Başvuru bulunamadı',
        'description' => 'Yeni başvuru eklemek için "Yeni Başvuru" düğmesine tıklayın.',
    ],
    'filters' => [
        'buttons' => [
            'remove' => 'Filtreyi kaldır',
            'remove_all' => 'Tüm filtreleri kaldır',
            'apply' => 'Uygula',
        ],
        'indicator' => 'Aktif filtreler',
        'multi_select' => [
            'placeholder' => 'Tümü',
        ],
        'select' => [
            'placeholder' => 'Tümü',
        ],
        'trashed' => [
            'label' => 'Silinen kayıtlar',
            'only_trashed' => 'Sadece silinen kayıtlar',
            'with_trashed' => 'Silinen kayıtlarla birlikte',
            'without_trashed' => 'Silinen kayıtlar olmadan',
        ],
    ],
    'selection_indicator' => [
        'selected_count' => '1 kayıt seçildi.|:count kayıt seçildi.',
        'actions' => [
            'select_all' => [
                'label' => 'Tümünü seç (:count)',
            ],
            'deselect_all' => [
                'label' => 'Tüm seçimleri kaldır',
            ],
        ],
    ],
    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'Sırala',
            ],
            'direction' => [
                'label' => 'Sıralama yönü',
                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],
            ],
        ],
    ],
    'search' => [
        'placeholder' => 'Ara...',
        'trigger' => 'Ara',
    ],
    'no_records' => 'Başvuru bulunamadı',
    'new_record' => 'Yeni Başvuru',
    'applications' => [
        'empty_heading' => 'Başvuru bulunamadı',
        'empty_description' => 'Yeni bir başvuru eklemek için "Yeni Başvuru" düğmesine tıklayın.',
    ],
]; 