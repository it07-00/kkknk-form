@extends('layouts.admin')

@section('title', 'Danh sách hồ sơ')

@section('content')
  <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-emerald-700">Trung tâm quản trị</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Hồ sơ doanh nghiệp</h1>
        <p class="mt-2 text-sm text-slate-600">Theo dõi, kiểm tra và cập nhật tiến độ xử lý hồ sơ đã tiếp nhận.</p>
      </div>
      <a href="{{ route('form.index') }}" target="_blank" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-emerald-700 hover:text-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
        Mở biểu mẫu doanh nghiệp
      </a>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tổng hồ sơ</p>
        <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format($totalSubmissions) }}</p>
      </div>
      @foreach ($statuses as $status)
        <a href="{{ route('admin.submissions.index', ['status' => $status->value]) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-700/40 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $status->label() }}</p>
          <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format((int) $statusCounts->get($status->value, 0)) }}</p>
        </a>
      @endforeach
    </div>

    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
      <form method="GET" action="{{ route('admin.submissions.index') }}" class="grid gap-3 border-b border-slate-200 p-4 sm:grid-cols-[minmax(0,1fr)_220px_auto] sm:p-5">
        <div>
          <label for="search" class="sr-only">Tìm hồ sơ</label>
          <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Tìm mã hồ sơ, doanh nghiệp, mã số thuế..." class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10">
        </div>
        <div>
          <label for="status" class="sr-only">Lọc trạng thái</label>
          <select id="status" name="status" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10">
            <option value="">Tất cả trạng thái</option>
            @foreach ($statuses as $status)
              <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="min-h-11 flex-1 rounded-xl bg-emerald-950 px-4 text-sm font-bold text-white transition hover:bg-emerald-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-800 focus-visible:ring-offset-2">Tìm kiếm</button>
          @if (request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.submissions.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Xóa lọc</a>
          @endif
        </div>
      </form>

      @if ($submissions->isEmpty())
        <div class="px-6 py-16 text-center">
          <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6" stroke-width="2"><path d="m21 21-4.3-4.3"/><circle cx="11" cy="11" r="8"/></svg>
          </div>
          <h2 class="mt-4 text-base font-bold text-slate-900">Không tìm thấy hồ sơ phù hợp</h2>
          <p class="mt-1 text-sm text-slate-600">Hãy thử từ khóa khác hoặc xóa bộ lọc hiện tại.</p>
        </div>
      @else
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
              <tr>
                <th class="px-5 py-3 font-semibold">Hồ sơ</th>
                <th class="px-5 py-3 font-semibold">Doanh nghiệp</th>
                <th class="px-5 py-3 font-semibold">Kỳ báo cáo</th>
                <th class="px-5 py-3 font-semibold">Trạng thái</th>
                <th class="px-5 py-3 font-semibold">Tiếp nhận</th>
                <th class="px-5 py-3 text-right font-semibold">Thao tác</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @foreach ($submissions as $submission)
                <tr class="hover:bg-slate-50/80">
                  <td class="whitespace-nowrap px-5 py-4 font-mono text-xs font-bold text-emerald-800">{{ $submission->code }}</td>
                  <td class="min-w-64 px-5 py-4">
                    <p class="font-semibold text-slate-900">{{ $submission->company_name }}</p>
                    <p class="mt-0.5 font-mono text-xs text-slate-500">MST: {{ $submission->tax_id }}</p>
                  </td>
                  <td class="whitespace-nowrap px-5 py-4 text-slate-700">{{ implode(', ', $submission->reporting_years ?? []) }}</td>
                  <td class="whitespace-nowrap px-5 py-4">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $submission->status->badgeClasses() }}">{{ $submission->status->label() }}</span>
                  </td>
                  <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                  <td class="whitespace-nowrap px-5 py-4 text-right">
                    <a href="{{ route('admin.submissions.show', $submission) }}" class="inline-flex min-h-11 items-center rounded-xl px-3 text-sm font-bold text-emerald-800 hover:bg-emerald-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700">Xem chi tiết</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">{{ $submissions->links() }}</div>
      @endif
    </section>
  </div>
@endsection
