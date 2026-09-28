@extends('layouts.admin')

@section('title', 'Đăng nhập quản trị')

@section('content')
  <div class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6">
    <div class="w-full max-w-md">
      <div class="mb-6 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-950 text-lime-300 shadow-lg" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-8 w-8" stroke-width="2">
            <rect width="18" height="11" x="3" y="11" rx="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
          </svg>
        </div>
        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-950">Đăng nhập quản trị</h1>
        <p class="mt-2 text-sm text-slate-600">Quản lý hồ sơ kiểm kê khí nhà kính và kế hoạch giảm nhẹ.</p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-emerald-950/5 sm:p-8">
        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5" novalidate>
          @csrf

          <div>
            <label for="email" class="block text-sm font-semibold text-slate-800">Email quản trị</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus
              class="mt-2 h-12 w-full rounded-xl border px-3 text-sm outline-none transition focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10 {{ $errors->has('email') ? 'border-red-500' : 'border-slate-300' }}"
              aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}">
            @error('email')
              <p id="email-error" class="mt-2 text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="password" class="block text-sm font-semibold text-slate-800">Mật khẩu</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required
              class="mt-2 h-12 w-full rounded-xl border px-3 text-sm outline-none transition focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10 {{ $errors->has('password') ? 'border-red-500' : 'border-slate-300' }}"
              aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
            @error('password')
              <p class="mt-2 text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
            @enderror
          </div>

          <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm text-slate-700">
            <input name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-700">
            <span>Ghi nhớ đăng nhập trên thiết bị này</span>
          </label>

          <button type="submit" class="min-h-12 w-full rounded-xl bg-emerald-950 px-4 text-sm font-bold text-white shadow-lg shadow-emerald-950/15 transition-colors hover:bg-emerald-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-800 focus-visible:ring-offset-2">
            Đăng nhập
          </button>
        </form>
      </div>

      <p class="mt-6 text-center text-xs text-slate-500">Khu vực dành riêng cho cán bộ được phân quyền.</p>
    </div>
  </div>
@endsection
