<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientela — mini-CRM de clientes e atendimentos</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 antialiased dark:bg-gray-900 dark:text-gray-200">
    {{-- Top bar --}}
    <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
        <div class="flex items-center gap-2 text-lg font-bold">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">C</span>
            Clientela
        </div>
        <nav class="flex items-center gap-2 text-sm font-medium">
            @auth
                <a href="{{ route('clients.index') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Meus clientes</a>
            @else
                <a href="{{ route('login') }}" class="rounded-md px-4 py-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Entrar</a>
                <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Criar conta</a>
            @endauth
        </nav>
    </header>

    {{-- Hero --}}
    <main class="mx-auto max-w-5xl px-6">
        <section class="py-16 text-center sm:py-24">
            <h1 class="mx-auto max-w-2xl text-4xl font-extrabold tracking-tight sm:text-5xl">
                Organize seus clientes e <span class="text-indigo-600 dark:text-indigo-400">atendimentos</span>
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-lg text-gray-600 dark:text-gray-400">
                Um CRM simples e direto: cadastre clientes, acompanhe o status e registre cada
                contato (ligação, e-mail, reunião) em um histórico organizado.
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Criar conta grátis
                </a>
                <a href="{{ route('login') }}" class="rounded-lg border border-gray-300 px-6 py-3 text-base font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Entrar
                </a>
            </div>
            <p class="mt-5 text-sm text-gray-500 dark:text-gray-400">
                Quer testar sem cadastrar? Use a conta demo:
                <span class="font-medium text-gray-700 dark:text-gray-300">demo@clientela.app</span> / <span class="font-medium text-gray-700 dark:text-gray-300">password</span>
            </p>
        </section>

        {{-- Features --}}
        <section class="grid gap-6 pb-24 sm:grid-cols-3">
            @php
                $features = [
                    ['👤', 'Cadastro de clientes', 'Nome, contato, empresa e status (lead, ativo, inativo), com busca e filtro.'],
                    ['🗂️', 'Histórico de atendimentos', 'Registre ligações, e-mails, reuniões e anotações por cliente.'],
                    ['🔒', 'Seus dados, só seus', 'Cada usuário gerencia a própria carteira de clientes com segurança.'],
                ];
            @endphp
            @foreach ($features as [$icon, $title, $desc])
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-xl dark:bg-gray-700">{{ $icon }}</div>
                    <h3 class="font-semibold">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                </div>
            @endforeach
        </section>
    </main>
</body>
</html>
