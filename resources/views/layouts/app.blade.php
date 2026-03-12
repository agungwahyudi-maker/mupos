<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MuPos - Terminal Kasir' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        body { background-color: #f3f4f6; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    </style>
</head>
<body class="antialiased font-sans">
    
    {{ $slot }} @livewireScripts
</body>
</html>