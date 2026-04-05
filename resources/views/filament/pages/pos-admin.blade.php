<x-filament-panels::page>
    <style>
        /* Menghilangkan padding kiri-kanan pada container utama Filament */
        .fi-main-ctn {
            max-width: none !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Menghilangkan margin atas pada konten agar menempel ke topbar */
        .fi-page-content {
            margin-top: -2rem !important;
        }

        /* Opsional: Membuat area POS lebih tinggi (Full Height) */
        .pos-container {
            min-height: calc(100vh - 4rem);
        }
    </style>

    <div class="pos-container px-4">
        @livewire('pos-admin')
    </div>
</x-filament-panels::page>