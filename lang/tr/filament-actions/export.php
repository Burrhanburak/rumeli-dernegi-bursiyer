<?php

return [
    'notifications' => [
        'started' => [
            'title' => 'Dışa aktarma başladı',
            'body' => 'Dışa aktarma işleminiz başladı ve arka planda işlenecek. İşlem tamamlandığında indirme bağlantısıyla bir bildirim alacaksınız.',
        ],
        'completed' => [
            'title' => 'Dışa aktarma tamamlandı',
            'body' => fn (array $data) => 'Dışa aktarmanız tamamlandı ve ' . number_format($data['successfulRowsCount']) . ' satır dışa aktarıldı.',
            'download' => 'İndir',
        ],
    ],
]; 