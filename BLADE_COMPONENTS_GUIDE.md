# HƯỚNG DẪN KIẾN TRÚC COMPONENT & TÍCH HỢP LARAVEL BLADE

Biểu mẫu: **"BẢNG CUNG CẤP SỐ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH VÀ KẾ HOẠCH GIẢM NHẸ"**  
Phong cách thiết kế: **Government / Enterprise Environmental Data Collection Portal**

---

## 1. SƠ ĐỒ PHÂN TÁCH COMPONENT (COMPONENT ARCHITECTURE)

```
resources/views/
└── forms/
    ├── greenhouse-gas.blade.php          (Trang chính chứa Alpine container, header, footer nav)
    └── partials/
        ├── header.blade.php              (Logo Sở Công Thương, Autosave indicator, Badge trạng thái)
        ├── intro-card.blade.php          (Thông báo gửi DN & Box dữ liệu cần chuẩn bị)
        ├── progress-stepper.blade.php    (Sidebar desktop 7 bước + Mobile progress tracker)
        ├── step-1-company.blade.php      (Bước 1: Thông tin DN, ĐKKD, Người đại diện, Cán bộ số liệu)
        ├── step-2-reporting-period.blade.php (Bước 2: Selectable cards 2024, 2025, Cả hai năm)
        ├── step-3-scope-1.blade.php      (Bước 3: Phát thải trực tiếp, Year tabs, Dynamic Repeater)
        ├── step-4-scope-2.blade.php      (Bước 4: Điện lưới, điện mặt trời, hướng dẫn phát thải)
        ├── step-5-inventory-results.blade.php (Bước 5: Phát thải Scope 1 & 2, link báo cáo)
        ├── step-6-mitigation.blade.php   (Bước 6: Kế hoạch giảm nhẹ 2026-2030, lượng tCO2e cắt giảm)
        ├── step-7-review.blade.php       (Bước 7: Bảng rà soát tổng hợp, cam kết dữ liệu)
        ├── step-navigation.blade.php     (Sticky navigation bottom: Quay lại, Lưu nháp, Tiếp tục/Gửi)
        ├── success-receipt.blade.php     (Màn hình hoàn tất: Mã hồ sơ GHG-2026-xxxx, tải PDF)
        └── modals/
            ├── delete-source-modal.blade.php
            └── confirm-submit-modal.blade.php
```

Các reusable atom components trong `resources/views/components/form/`:
- `<x-form.input name="..." label="..." required ... />`
- `<x-form.select name="..." label="..." ... />`
- `<x-form.radio-card name="..." ... />`
- `<x-form.source-emission-card :source="$source" ... />`

---

## 2. BẢN MẪU COMPONENT BLADE TIÊU BIỂU

### A. Component Thẻ Nguồn Phát Thải: `source-emission-card.blade.php`

```blade
@props(['year', 'index'])

<div class="bg-white rounded-2xl border border-borderui p-5 shadow-subtle hover:border-slate-300 transition-all space-y-4">
    <!-- Card Header -->
    <div class="flex items-center justify-between pb-3 border-b border-borderui">
        <div class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-lg bg-primary text-white text-xs font-bold font-mono flex items-center justify-center"
                  x-text="'#' + ({{ $index }} + 1)"></span>
            <span class="text-xs font-bold text-txprimary uppercase tracking-wide">
                Nguồn phát thải <span x-text="'#0' + ({{ $index }} + 1)"></span>
            </span>
            <template x-if="source.source_type">
                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-txsecondary border border-slate-200"
                      x-text="source.source_type === 'Khác' ? (source.source_type_other || 'Nguồn khác') : source.source_type"></span>
            </template>
        </div>

        <button type="button"
                @click="openDeleteSourceModal({{ $year }}, {{ $index }})"
                class="text-xs text-txsecondary hover:text-danger hover:bg-red-50 p-1.5 rounded-lg transition-colors flex items-center space-x-1">
            <i data-lucide="trash-2" class="w-4 h-4 text-txsecondary hover:text-danger"></i>
            <span class="hidden sm:inline">Xóa nguồn</span>
        </button>
    </div>

    <!-- Grid Fields -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <!-- Loại nguồn -->
        <div class="md:col-span-6">
            <label class="block text-xs font-semibold text-txprimary mb-1">
                A. Loại nguồn phát thải <span class="text-danger">*</span>
            </label>
            <select x-model="source.source_type" class="w-full h-10 px-3 rounded-xl border border-borderui bg-white text-xs sm:text-sm focus-ring">
                <option value="" disabled selected>-- Chọn loại nguồn phát thải --</option>
                <option value="Đốt nhiên liệu cố định">Đốt nhiên liệu cố định (Nồi hơi, lò nung, máy phát điện...)</option>
                <option value="Đốt nhiên liệu di động">Đốt nhiên liệu di động (Xe nâng, xe tải nội bộ, ô tô...)</option>
                <option value="Rò rỉ môi chất lạnh">Rò rỉ môi chất lạnh (Hệ thống điều hòa, chiller, kho lạnh...)</option>
                <option value="Quá trình công nghiệp">Quá trình công nghiệp (Sản xuất xi măng, hóa chất...)</option>
                <option value="Xử lý chất thải">Xử lý chất thải rắn tại chỗ</option>
                <option value="Nước thải">Hệ thống xử lý nước thải yếm khí</option>
                <option value="Khác">Khác (Nêu rõ bên dưới)</option>
            </select>
            <template x-if="source.source_type === 'Khác'">
                <input type="text" x-model="source.source_type_other" placeholder="Nhập tên nguồn phát thải khác..." class="w-full h-9 px-3 mt-2 rounded-xl border border-borderui bg-white text-xs focus-ring">
            </template>
        </div>

        <!-- Nhiên liệu -->
        <div class="md:col-span-6">
            <label class="block text-xs font-semibold text-txprimary mb-1">
                B. Nhiên liệu / chất sử dụng <span class="text-danger">*</span>
            </label>
            <select x-model="source.fuel_type" class="w-full h-10 px-3 rounded-xl border border-borderui bg-white text-xs sm:text-sm focus-ring">
                <option value="" disabled selected>-- Chọn nhiên liệu / chất sử dụng --</option>
                <option value="Dầu DO">Dầu DO (Diesel)</option>
                <option value="Dầu FO">Dầu FO (Fuel Oil)</option>
                <option value="LPG">LPG (Khí hóa lỏng)</option>
                <option value="Than">Than đá / Than cám</option>
                <option value="Khí tự nhiên">Khí tự nhiên (CNG, LNG, PNG)</option>
                <option value="Xăng">Xăng RON 95 / E5</option>
                <option value="Sinh khối">Sinh khối (Trấu, mùn cưa...)</option>
                <option value="Củi">Củi gỗ</option>
                <option value="Viên nén">Viên nén mùn cưa (Pellets)</option>
                <option value="Môi chất lạnh R22">Môi chất lạnh R22</option>
                <option value="Môi chất lạnh R32">Môi chất lạnh R32</option>
                <option value="Môi chất lạnh R410A">Môi chất lạnh R410A</option>
                <option value="Khác">Khác (Nêu rõ bên dưới)</option>
            </select>
            <template x-if="source.fuel_type === 'Khác'">
                <input type="text" x-model="source.fuel_type_other" placeholder="Nhập tên nhiên liệu / chất khác..." class="w-full h-9 px-3 mt-2 rounded-xl border border-borderui bg-white text-xs focus-ring">
            </template>
        </div>

        <!-- Lượng sử dụng -->
        <div class="md:col-span-6">
            <label class="block text-xs font-semibold text-txprimary mb-1">
                C. Lượng sử dụng trong năm <span class="text-danger">*</span>
            </label>
            <input type="number" step="any" min="0" x-model.number="source.quantity" placeholder="Ví dụ: 12500" class="w-full h-10 px-3 rounded-xl border border-borderui bg-white text-xs sm:text-sm font-mono focus-ring">
        </div>

        <!-- Đơn vị tính -->
        <div class="md:col-span-6">
            <label class="block text-xs font-semibold text-txprimary mb-1">
                D. Đơn vị tính <span class="text-danger">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <select x-model="source.unit" class="w-full h-10 px-3 rounded-xl border border-borderui bg-white text-xs sm:text-sm focus-ring">
                    <option value="" disabled selected>-- Đơn vị --</option>
                    <option value="kg">kg</option>
                    <option value="tấn">tấn</option>
                    <option value="lít">lít</option>
                    <option value="m³">m³</option>
                    <option value="Nm³">Nm³</option>
                    <option value="kWh">kWh</option>
                    <option value="GJ">GJ</option>
                    <option value="Khác">Khác</option>
                </select>
                <template x-if="source.unit === 'Khác'">
                    <input type="text" x-model="source.unit_other" placeholder="Đơn vị khác..." class="w-full h-10 px-3 rounded-xl border border-borderui bg-white text-xs focus-ring">
                </template>
            </div>
        </div>

        <!-- Ghi chú -->
        <div class="md:col-span-12">
            <label class="block text-xs font-semibold text-txprimary mb-1">E. Ghi chú nguồn phát thải</label>
            <textarea rows="2" x-model="source.note" placeholder="Thông tin bổ sung..." class="w-full p-2.5 rounded-xl border border-borderui bg-white text-xs focus-ring"></textarea>
        </div>
    </div>
</div>
```

---

## 3. LARAVEL CONTROLLER & FORM REQUEST TƯƠNG THÍCH

### `app/Http/Requests/StoreGhgInventoryRequest.php`:

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGhgInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company.name' => 'required|string|max:255',
            'company.tax_code' => 'required|string|min:8|max:20',
            'company.address' => 'required|string|max:500',
            'company.industry' => 'required|string|max:255',
            'company.email' => 'required|email|max:255',
            'company.legal_representative.name' => 'required|string|max:255',
            'company.legal_representative.phone' => 'nullable|string|max:20',
            'company.technical_contact.name' => 'required|string|max:255',
            'company.technical_contact.phone' => 'required|string|max:20',

            'reporting_years' => 'required|array|min:1',
            'reporting_years.*' => 'in:2024,2025',

            'inventory' => 'required|array',
            'inventory.*.has_scope1' => 'required|boolean',
            'inventory.*.scope1_sources' => 'nullable|array|max:8',
            'inventory.*.scope1_sources.*.source_type' => 'required_with:inventory.*.scope1_sources|string',
            'inventory.*.scope1_sources.*.fuel_type' => 'required_with:inventory.*.scope1_sources|string',
            'inventory.*.scope1_sources.*.quantity' => 'required_with:inventory.*.scope1_sources|numeric|min:0',
            'inventory.*.scope1_sources.*.unit' => 'required_with:inventory.*.scope1_sources|string',
            
            'inventory.*.grid_electricity_kwh' => 'required|numeric|min:0',
            'inventory.*.solar_electricity_kwh' => 'required|numeric|min:0',
            'inventory.*.scope1_emissions' => 'nullable|numeric|min:0',
            'inventory.*.scope2_emissions' => 'nullable|numeric|min:0',
            'inventory.*.report_method' => 'required|string',
            'inventory.*.report_url' => 'nullable|url',

            'mitigation.implemented' => 'required|boolean',
            'mitigation.plan_2026_2030' => 'nullable|string',
            'mitigation.implemented_measures' => 'nullable|string',
            'mitigation.planned_reduction_tco2e' => 'nullable|numeric|min:0',
            'mitigation.actual_reduction_tco2e' => 'nullable|numeric|min:0',

            'confirmation' => 'accepted',
        ];
    }
}
```

### `app/Http/Controllers/GhgInventoryController.php`:

```php
namespace App\Http\Controllers;

use App\Http\Requests\StoreGhgInventoryRequest;
use App\Models\GhgInventorySubmission;
use Illuminate\Http\JsonResponse;

class GhgInventoryController extends Controller
{
    public function showForm()
    {
        return view('forms.greenhouse-gas');
    }

    public function store(StoreGhgInventoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $submission = GhgInventorySubmission::create([
            'dossier_code' => 'GHG-2026-' . str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'company_info' => $validated['company'],
            'reporting_years' => $validated['reporting_years'],
            'inventory_data' => $validated['inventory'],
            'mitigation_plan' => $validated['mitigation'],
            'submitted_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hồ sơ đã được tiếp nhận thành công',
            'receipt' => [
                'code' => $submission->dossier_code,
                'time' => $submission->submitted_at->format('d/m/Y H:i'),
            ]
        ]);
    }
}
```
