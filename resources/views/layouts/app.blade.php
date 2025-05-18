<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title') - MyCP Platform</title>

    <!-- Tailwind CDN for demo (replace with proper build in prod) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Monaco Editor CDN for code editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs/loader.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">MyCP Platform</h1>
        <nav>
            <a href="#" class="text-blue-600 hover:underline ml-4">Problems</a>
            <a href="#" class="text-blue-600 hover:underline ml-4">Contests</a>
            <a href="#" class="text-blue-600 hover:underline ml-4">Profile</a>
        </nav>
    </header>

    <main class="flex-grow container mx-auto p-4">
        @yield('content')
    </main>

    <footer class="bg-white text-center p-4 text-sm text-gray-500">
        &copy; {{ date('Y') }} MyCP Platform. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>
