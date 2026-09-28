<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreGhgSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'formData' => ['required', 'array'],
            'formData.company' => ['required', 'array'],
            'formData.company.name' => ['required', 'string', 'max:255'],
            'formData.company.tax_code' => ['required', 'string', 'max:20'],
            'formData.company.address' => ['required', 'string', 'max:1000'],
            'formData.company.industry' => ['required', 'string', 'max:500'],
            'formData.company.email' => ['required', 'email:rfc', 'max:255'],
            'formData.company.legal_representative' => ['required', 'array'],
            'formData.company.legal_representative.name' => ['required', 'string', 'max:255'],
            'formData.company.legal_representative.phone' => ['nullable', 'string', 'max:30'],
            'formData.company.technical_contact' => ['required', 'array'],
            'formData.company.technical_contact.name' => ['required', 'string', 'max:255'],
            'formData.company.technical_contact.phone' => ['required', 'string', 'max:30'],
            'formData.reporting_years' => ['required', 'array', 'min:1', 'max:3'],
            'formData.reporting_years.*' => ['required', 'string', 'distinct', Rule::in(['2024', '2025', '2026'])],
            'formData.inventory' => ['required', 'array'],
            'formData.inventory.*' => ['array'],
            'formData.inventory.*.has_scope1' => ['nullable', 'boolean'],
            'formData.inventory.*.has_boiler' => ['nullable', 'boolean'],
            'formData.inventory.*.boiler' => ['nullable', 'array'],
            'formData.inventory.*.boiler.capacity' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.boiler.fuel' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.boiler.fuel_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.boiler.consumption' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.boiler.unit' => ['nullable', 'string', 'max:50'],
            'formData.inventory.*.has_cooling' => ['nullable', 'boolean'],
            'formData.inventory.*.refrigeration' => ['nullable', 'array'],
            'formData.inventory.*.refrigeration.equipment' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration.equipment_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration.capacity' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration.gas_type' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration.gas_type_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration.full_charge_kg' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.refrigeration.recharge_kg' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope1_sources' => ['nullable', 'array'],
            'formData.inventory.*.scope1_sources.*' => ['array'],
            'formData.inventory.*.scope1_sources.*.id' => ['nullable', 'string', 'max:100'],
            'formData.inventory.*.scope1_sources.*.source_type' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.scope1_sources.*.source_type_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.scope1_sources.*.fuel_type' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.scope1_sources.*.fuel_type_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.scope1_sources.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope1_sources.*.unit' => ['nullable', 'string', 'max:50'],
            'formData.inventory.*.scope1_sources.*.unit_other' => ['nullable', 'string', 'max:50'],
            'formData.inventory.*.scope1_sources.*.note' => ['nullable', 'string', 'max:2000'],
            'formData.inventory.*.grid_electricity_kwh' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.solar_electricity_kwh' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.energy_toe' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope1_emissions' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope2_emissions' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.report_method' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.report_url' => ['nullable', 'url', 'max:2048'],
            'formData.mitigation' => ['required', 'array'],
            'formData.mitigation.implemented' => ['required', 'boolean'],
            'formData.mitigation.plan_2026_2030' => ['nullable', 'string', 'max:20000'],
            'formData.mitigation.implemented_measures' => ['nullable', 'string', 'max:20000'],
            'formData.mitigation.planned_reduction_tco2e' => ['nullable', 'numeric', 'min:0'],
            'formData.mitigation.actual_reduction_tco2e' => ['nullable', 'numeric', 'min:0'],
            'formData.mitigation.note' => ['nullable', 'string', 'max:5000'],
            'formData.mitigation.report_url' => ['nullable', 'url', 'max:2048'],
            'formData.confirmation' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'formData.company.tax_code.required' => 'Vui lòng nhập mã số thuế doanh nghiệp.',
            'formData.reporting_years.*.in' => 'Kỳ báo cáo chỉ hỗ trợ các năm 2024, 2025 và 2026.',
            'formData.mitigation.implemented.required' => 'Vui lòng xác nhận tình trạng thực hiện biện pháp giảm nhẹ.',
            'formData.confirmation.accepted' => 'Vui lòng xác nhận tính chính xác của dữ liệu trước khi gửi.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $reportingYears = $this->input('formData.reporting_years', []);
            $inventory = $this->input('formData.inventory', []);

            if (! is_array($reportingYears) || ! is_array($inventory)) {
                return;
            }

            foreach ($reportingYears as $year) {
                $yearInventory = $inventory[(string) $year] ?? null;

                if (! is_array($yearInventory)) {
                    $validator->errors()->add(
                        "formData.inventory.{$year}",
                        "Thiếu dữ liệu kiểm kê cho năm {$year}."
                    );

                    continue;
                }

                if (! array_key_exists('has_scope1', $yearInventory) || $yearInventory['has_scope1'] === null) {
                    $validator->errors()->add(
                        "formData.inventory.{$year}.has_scope1",
                        "Vui lòng xác nhận nguồn phát thải Phạm vi 1 cho năm {$year}."
                    );
                }

                foreach (['grid_electricity_kwh', 'solar_electricity_kwh'] as $field) {
                    if (! array_key_exists($field, $yearInventory) || $yearInventory[$field] === null || $yearInventory[$field] === '') {
                        $validator->errors()->add(
                            "formData.inventory.{$year}.{$field}",
                            "Vui lòng cung cấp số liệu điện năng cho năm {$year}."
                        );
                    }
                }
            }
        });
    }
}
