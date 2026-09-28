@extends('layouts.admin')

@section('title', 'Chi tiết '.$submission->code)

@section('content')
  @php
    $company = data_get($submission->data, 'company', []);
    $inventories = data_get($submission->data, 'inventory', []);
    $mitigation = data_get($submission->data, 'mitigation', []);
  @endphp

  <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
        <a href="{{ route('admin.submissions.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-emerald-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700">← Quay lại danh sách</a>
        <div class="mt-2 flex flex-wrap items-center gap-3">
          <h1 class="font-mono text-2xl font-bold text-slate-950">{{ $submission->code }}</h1>
          <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $submission->status->badgeClasses() }}">{{ $submission->status->label() }}</span>
        </div>
        <p class="mt-2 text-sm text-slate-600">Tiếp nhận lúc {{ $submission->created_at->format('d/m/Y H:i') }} từ IP {{ $submission->ip_address ?? 'Không ghi nhận' }}</p>
      </div>

      <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.submissions.excel.download', $submission) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-950 px-4 text-sm font-bold text-white hover:bg-emerald-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-800 focus-visible:ring-offset-2">Tải Excel</a>
        @if ($submission->mitigation_report_path)
          <a href="{{ route('admin.submissions.report.download', $submission) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-emerald-800 bg-white px-4 text-sm font-bold text-emerald-800 hover:bg-emerald-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">Tải file báo cáo</a>
        @endif
      </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
      <div class="space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
          <h2 class="text-lg font-bold text-slate-950">Thông tin doanh nghiệp</h2>
          <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
            <div class="sm:col-span-2"><dt class="text-slate-500">Tên doanh nghiệp</dt><dd class="mt-1 font-semibold text-slate-900">{{ data_get($company, 'name', '---') }}</dd></div>
            <div><dt class="text-slate-500">Mã số thuế</dt><dd class="mt-1 font-mono font-semibold">{{ data_get($company, 'tax_code', '---') }}</dd></div>
            <div><dt class="text-slate-500">Ngành nghề</dt><dd class="mt-1 font-medium">{{ data_get($company, 'industry', '---') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Địa chỉ</dt><dd class="mt-1 font-medium">{{ data_get($company, 'address', '---') }}</dd></div>
            <div><dt class="text-slate-500">Email</dt><dd class="mt-1 font-medium">{{ data_get($company, 'email', '---') }}</dd></div>
            <div><dt class="text-slate-500">Đại diện pháp luật</dt><dd class="mt-1 font-medium">{{ data_get($company, 'legal_representative.name', '---') }} · {{ data_get($company, 'legal_representative.phone', '---') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Cán bộ phụ trách số liệu</dt><dd class="mt-1 font-medium">{{ data_get($company, 'technical_contact.name', '---') }} · {{ data_get($company, 'technical_contact.phone', '---') }}</dd></div>
          </dl>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
          <h2 class="text-lg font-bold text-slate-950">Số liệu theo kỳ báo cáo</h2>
          <div class="mt-5 space-y-4">
            @forelse ($submission->reporting_years ?? [] as $year)
              @php($inventory = data_get($inventories, (string) $year, []))
              <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="font-bold text-emerald-900">Năm {{ $year }}</h3>
                <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2 xl:grid-cols-3">
                  <div><dt class="text-slate-500">Điện lưới</dt><dd class="mt-1 font-mono font-semibold">{{ number_format((float) data_get($inventory, 'grid_electricity_kwh', 0), 0, ',', '.') }} kWh</dd></div>
                  <div><dt class="text-slate-500">Điện mặt trời</dt><dd class="mt-1 font-mono font-semibold">{{ number_format((float) data_get($inventory, 'solar_electricity_kwh', 0), 0, ',', '.') }} kWh</dd></div>
                  <div><dt class="text-slate-500">Năng lượng quy đổi</dt><dd class="mt-1 font-mono font-semibold">{{ data_get($inventory, 'energy_toe', '---') }} TOE</dd></div>
                  <div><dt class="text-slate-500">Phát thải Phạm vi 1</dt><dd class="mt-1 font-mono font-semibold">{{ data_get($inventory, 'scope1_emissions', '---') }} tCO₂e</dd></div>
                  <div><dt class="text-slate-500">Phát thải Phạm vi 2</dt><dd class="mt-1 font-mono font-semibold">{{ data_get($inventory, 'scope2_emissions', '---') }} tCO₂e</dd></div>
                  <div><dt class="text-slate-500">Hình thức báo cáo</dt><dd class="mt-1 font-semibold">{{ data_get($inventory, 'report_method', '---') }}</dd></div>
                </dl>
                @if (data_get($inventory, 'report_url'))
                  <a href="{{ data_get($inventory, 'report_url') }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex min-h-11 items-center text-sm font-bold text-emerald-800 hover:underline">Mở liên kết báo cáo năm {{ $year }} ↗</a>
                @endif
              </article>
            @empty
              <p class="text-sm text-slate-600">Hồ sơ chưa có kỳ báo cáo.</p>
            @endforelse
          </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
          <h2 class="text-lg font-bold text-slate-950">Kế hoạch và kết quả giảm nhẹ</h2>
          <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-slate-500">Tình trạng thực hiện</dt><dd class="mt-1 font-semibold">{{ data_get($mitigation, 'implemented') === true ? 'Đã thực hiện' : 'Chưa thực hiện' }}</dd></div>
            <div><dt class="text-slate-500">Cắt giảm dự kiến</dt><dd class="mt-1 font-mono font-semibold">{{ data_get($mitigation, 'planned_reduction_tco2e', '---') }} tCO₂e</dd></div>
            <div><dt class="text-slate-500">Cắt giảm thực tế</dt><dd class="mt-1 font-mono font-semibold">{{ data_get($mitigation, 'actual_reduction_tco2e', '---') }} tCO₂e</dd></div>
            <div><dt class="text-slate-500">File báo cáo</dt><dd class="mt-1 font-semibold">{{ $submission->mitigation_report_original_name ?? 'Không đính kèm' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Kế hoạch 2026–2030</dt><dd class="mt-1 whitespace-pre-line font-medium">{{ data_get($mitigation, 'plan_2026_2030', '---') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Biện pháp đã thực hiện</dt><dd class="mt-1 whitespace-pre-line font-medium">{{ data_get($mitigation, 'implemented_measures', '---') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Ghi chú</dt><dd class="mt-1 whitespace-pre-line font-medium">{{ data_get($mitigation, 'note', '---') }}</dd></div>
          </dl>
        </section>
      </div>

      <aside class="space-y-5 lg:sticky lg:top-6 lg:self-start">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 class="text-base font-bold text-slate-950">Cập nhật trạng thái</h2>
          <form method="POST" action="{{ route('admin.submissions.status.update', $submission) }}" class="mt-4 space-y-4">
            @csrf
            @method('PATCH')
            <div>
              <label for="status" class="block text-sm font-semibold text-slate-800">Trạng thái xử lý</label>
              <select id="status" name="status" class="mt-2 h-11 w-full rounded-xl border bg-white px-3 text-sm outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-700/10 {{ $errors->has('status') ? 'border-red-500' : 'border-slate-300' }}" aria-invalid="{{ $errors->has('status') ? 'true' : 'false' }}">
                @foreach ($statuses as $status)
                  <option value="{{ $status->value }}" @selected(old('status', $submission->status->value) === $status->value)>{{ $status->label() }}</option>
                @endforeach
              </select>
              @error('status')<p class="mt-2 text-sm font-medium text-red-600" role="alert">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="min-h-11 w-full rounded-xl bg-emerald-950 px-4 text-sm font-bold text-white hover:bg-emerald-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-800 focus-visible:ring-offset-2">Lưu trạng thái</button>
          </form>
          @if ($submission->reviewer)
            <div class="mt-4 border-t border-slate-200 pt-4 text-xs text-slate-600">
              Cập nhật gần nhất bởi <strong class="text-slate-800">{{ $submission->reviewer->name }}</strong>
              @if ($submission->reviewed_at) lúc {{ $submission->reviewed_at->format('d/m/Y H:i') }} @endif
            </div>
          @endif
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 class="text-base font-bold text-slate-950">Tệp và dữ liệu</h2>
          <dl class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Mã số thuế</dt><dd class="font-mono font-semibold">{{ $submission->tax_id }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Kỳ báo cáo</dt><dd class="font-semibold">{{ implode(', ', $submission->reporting_years ?? []) }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">File đính kèm</dt><dd class="text-right font-semibold">{{ $submission->mitigation_report_original_name ?? 'Không có' }}</dd></div>
            @if ($submission->mitigation_report_size)
              <div class="flex justify-between gap-3"><dt class="text-slate-500">Dung lượng</dt><dd class="font-semibold">{{ \Illuminate\Support\Number::fileSize($submission->mitigation_report_size) }}</dd></div>
            @endif
          </dl>
        </section>
      </aside>
    </div>
  </div>
@endsection
