<!doctype html>
<html lang="vi" class="h-full bg-slate-50">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị hồ sơ khí nhà kính')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-full bg-slate-50 font-sans text-slate-900 antialiased">
    @auth
      <header class="border-b border-emerald-950/10 bg-emerald-950 text-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
          <a href="{{ route('admin.submissions.index') }}" class="flex min-w-0 items-center gap-3 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lime-300 focus-visible:ring-offset-2 focus-visible:ring-offset-emerald-950">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-lime-300 text-emerald-950" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5" stroke-width="2">
                <path d="M12 22c4-2 7-5.5 7-10V5l-7-3-7 3v7c0 4.5 3 8 7 10Z" />
                <path d="m9 12 2 2 4-4" />
              </svg>
            </span>
            <span class="min-w-0">
              <span class="block truncate text-sm font-bold sm:text-base">Quản trị hồ sơ KKKNK</span>
              <span class="hidden text-xs text-emerald-100 sm:block">Sở Công Thương TP. Hồ Chí Minh</span>
            </span>
          </a>

          <div class="flex items-center gap-3">
            <div class="hidden text-right sm:block">
              <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
              <p class="text-xs text-emerald-100">{{ auth()->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button type="submit" class="min-h-11 rounded-xl border border-white/20 px-4 text-sm font-semibold text-white transition-colors hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lime-300 focus-visible:ring-offset-2 focus-visible:ring-offset-emerald-950">
                Đăng xuất
              </button>
            </form>
          </div>
        </div>
      </header>
    @endauth

    @if (session('status'))
      <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6 lg:px-8" role="status">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
          {{ session('status') }}
        </div>
      </div>
    @endif

    <main>
      @yield('content')
    </main>
  </body>
</html>
