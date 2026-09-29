<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreGhgSubmissionRequest extends FormRequest
{
    /** @var array<int, string> */
    private const REPORTING_YEARS = ['2024', '2025', '2026'];

    /** @var array<int, string> */
    private const REPORT_METHODS = ['Chưa có báo cáo', 'Dán link báo cáo', 'Kê khai trực tiếp theo hóa đơn'];

    /** @var array<int, string> */
    private const SOURCE_TYPES = ['Đốt nhiên liệu cố định', 'Đốt nhiên liệu di động', 'Rò rỉ môi chất lạnh', 'Quá trình công nghiệp', 'Xử lý chất thải', 'Nước thải', 'Khác'];

    /** @var array<int, string> */
    private const SOURCE_FUELS = ['Dầu DO', 'Dầu FO', 'LPG', 'Than', 'Khí tự nhiên', 'Xăng', 'Sinh khối', 'Củi', 'Viên nén', 'Môi chất lạnh R22', 'Môi chất lạnh R32', 'Môi chất lạnh R410A', 'Khác'];

    /** @var array<int, string> */
    private const SOURCE_UNITS = ['kg', 'tấn', 'lít', 'm³', 'Nm³', 'kWh', 'GJ', 'Khác'];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $formData = $this->input('formData');

        if (is_string($formData)) {
            $decodedFormData = json_decode($formData, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedFormData)) {
                $formData = $decodedFormData;
            }
        }

        if (! is_array($formData)) {
            return;
        }

        foreach (['legal_representative', 'technical_contact'] as $contact) {
            $phone = $formData['company'][$contact]['phone'] ?? null;

            if (! is_string($phone)) {
                continue;
            }

            $normalizedPhone = preg_replace('/[\s.-]+/u', '', trim($phone));

            if (is_string($normalizedPhone) && str_starts_with($normalizedPhone, '+84')) {
                $normalizedPhone = '0'.substr($normalizedPhone, 3);
            }

            $formData['company'][$contact]['phone'] = $normalizedPhone;
        }

        $inventory = $formData['inventory'] ?? null;

        if (is_array($inventory)) {
            foreach ($inventory as $year => $yearInventory) {
                if (is_array($yearInventory)) {
                    if (! array_key_exists('boilers', $yearInventory)) {
                        $legacyBoiler = $yearInventory['boiler'] ?? null;
                        $yearInventory['boilers'] = is_array($legacyBoiler) ? [$legacyBoiler] : [];
                    }

                    if (! array_key_exists('refrigeration_systems', $yearInventory)) {
                        $legacyRefrigeration = $yearInventory['refrigeration'] ?? null;
                        $yearInventory['refrigeration_systems'] = is_array($legacyRefrigeration)
                            ? [$legacyRefrigeration]
                            : [];
                    }

                    unset($yearInventory['boiler'], $yearInventory['refrigeration']);
                    $yearInventory['has_scope1'] = true;
                    $formData['inventory'][$year] = $yearInventory;
                }
            }
        }

        $this->merge(['formData' => $formData]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'formData' => ['required', 'array:company,reporting_years,inventory,mitigation,confirmation'],
            'formData.company' => ['required', 'array:name,tax_code,address,industry,email,legal_representative,technical_contact'],
            'formData.company.name' => ['required', 'string', 'max:255'],
            'formData.company.tax_code' => ['required', 'string', 'min:8', 'max:20'],
            'formData.company.address' => ['required', 'string', 'max:1000'],
            'formData.company.industry' => ['required', 'string', 'max:500'],
            'formData.company.email' => ['required', 'email:rfc', 'max:255'],
            'formData.company.legal_representative' => ['required', 'array:name,phone'],
            'formData.company.legal_representative.name' => ['required', 'string', 'max:255'],
            'formData.company.legal_representative.phone' => ['required', 'string', 'max:30', 'regex:/^0(?:3|5|7|8|9)[0-9]{8}$/'],
            'formData.company.technical_contact' => ['required', 'array:name,phone'],
            'formData.company.technical_contact.name' => ['required', 'string', 'max:255'],
            'formData.company.technical_contact.phone' => ['required', 'string', 'max:30', 'regex:/^0(?:3|5|7|8|9)[0-9]{8}$/'],
            'formData.reporting_years' => ['required', 'array', 'min:1', 'max:3'],
            'formData.reporting_years.*' => ['required', 'string', 'distinct', Rule::in(self::REPORTING_YEARS)],
            'formData.inventory' => ['required', 'array:2024,2025,2026'],
            'formData.inventory.*' => ['array:has_scope1,has_boiler,boilers,has_cooling,refrigeration_systems,scope1_sources,grid_electricity_kwh,solar_electricity_kwh,energy_toe,scope1_emissions,scope2_emissions,report_method,report_url'],
            'formData.inventory.*.has_scope1' => ['required', 'boolean'],
            'formData.inventory.*.has_boiler' => ['nullable', 'boolean'],
            'formData.inventory.*.boilers' => ['nullable', 'array', 'max:50'],
            'formData.inventory.*.boilers.*' => ['array:id,capacity,fuel,fuel_other,consumption,unit'],
            'formData.inventory.*.boilers.*.id' => ['nullable', 'string', 'max:100'],
            'formData.inventory.*.boilers.*.capacity' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.boilers.*.fuel' => ['nullable', 'string', Rule::in(['Sinh khối', 'Than đá', 'Dầu DO', 'Dầu FO', 'LPG', 'Khí tự nhiên', 'Củi gỗ', 'Viên nén', 'Khác'])],
            'formData.inventory.*.boilers.*.fuel_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.boilers.*.consumption' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.boilers.*.unit' => ['nullable', 'string', Rule::in(['tấn/năm', 'kg/năm', 'm³/năm', 'lít/năm'])],
            'formData.inventory.*.has_cooling' => ['nullable', 'boolean'],
            'formData.inventory.*.refrigeration_systems' => ['nullable', 'array', 'max:50'],
            'formData.inventory.*.refrigeration_systems.*' => ['array:id,equipment,equipment_other,capacity,gas_type,gas_type_other,full_charge_kg,recharge_kg'],
            'formData.inventory.*.refrigeration_systems.*.id' => ['nullable', 'string', 'max:100'],
            'formData.inventory.*.refrigeration_systems.*.equipment' => ['nullable', 'string', Rule::in(['Máy lạnh', 'Chiller', 'VRV/VRF', 'Kho lạnh', 'Khác'])],
            'formData.inventory.*.refrigeration_systems.*.equipment_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration_systems.*.capacity' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration_systems.*.gas_type' => ['nullable', 'string', Rule::in(['R22', 'R410A', 'R134a', 'R32', 'R404A', 'R407C', 'R507A', 'Khác'])],
            'formData.inventory.*.refrigeration_systems.*.gas_type_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.refrigeration_systems.*.full_charge_kg' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.refrigeration_systems.*.recharge_kg' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope1_sources' => ['nullable', 'array'],
            'formData.inventory.*.scope1_sources.*' => ['array:id,source_type,source_type_other,fuel_type,fuel_type_other,quantity,unit,unit_other,note'],
            'formData.inventory.*.scope1_sources.*.id' => ['nullable', 'string', 'max:100'],
            'formData.inventory.*.scope1_sources.*.source_type' => ['nullable', 'string', Rule::in(self::SOURCE_TYPES)],
            'formData.inventory.*.scope1_sources.*.source_type_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.scope1_sources.*.fuel_type' => ['nullable', 'string', Rule::in(self::SOURCE_FUELS)],
            'formData.inventory.*.scope1_sources.*.fuel_type_other' => ['nullable', 'string', 'max:255'],
            'formData.inventory.*.scope1_sources.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope1_sources.*.unit' => ['nullable', 'string', Rule::in(self::SOURCE_UNITS)],
            'formData.inventory.*.scope1_sources.*.unit_other' => ['nullable', 'string', 'max:50'],
            'formData.inventory.*.scope1_sources.*.note' => ['nullable', 'string', 'max:2000'],
            'formData.inventory.*.grid_electricity_kwh' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.solar_electricity_kwh' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.energy_toe' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope1_emissions' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.scope2_emissions' => ['nullable', 'numeric', 'min:0'],
            'formData.inventory.*.report_method' => ['nullable', 'string', Rule::in(self::REPORT_METHODS)],
            'formData.inventory.*.report_url' => ['nullable', 'url', 'regex:/^https?:\/\//i', 'max:2048'],
            'formData.mitigation' => ['required', 'array:implemented,plan_2026_2030,implemented_measures,planned_reduction_tco2e,actual_reduction_tco2e,note,report_url'],
            'formData.mitigation.implemented' => ['required', 'boolean'],
            'formData.mitigation.plan_2026_2030' => ['nullable', 'string', 'max:20000'],
            'formData.mitigation.implemented_measures' => ['nullable', 'string', 'max:20000'],
            'formData.mitigation.planned_reduction_tco2e' => ['nullable', 'numeric', 'min:0'],
            'formData.mitigation.actual_reduction_tco2e' => ['nullable', 'numeric', 'min:0'],
            'formData.mitigation.note' => ['nullable', 'string', 'max:5000'],
            'formData.mitigation.report_url' => ['nullable', 'url', 'regex:/^https?:\/\//i', 'max:2048'],
            'formData.confirmation' => ['accepted'],
            'mitigation_report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'extensions:pdf,doc,docx,xls,xlsx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'formData.company.tax_code.required' => 'Vui lòng nhập mã số thuế doanh nghiệp.',
            'formData.company.tax_code.min' => 'Mã số thuế phải có ít nhất 8 ký tự.',
            'formData.company.legal_representative.phone.required' => 'Vui lòng nhập số điện thoại Người đại diện.',
            'formData.company.legal_representative.phone.regex' => 'Số điện thoại Người đại diện phải là số di động Việt Nam hợp lệ.',
            'formData.company.technical_contact.phone.required' => 'Vui lòng nhập số điện thoại cán bộ phụ trách số liệu.',
            'formData.company.technical_contact.phone.regex' => 'Số điện thoại cán bộ phụ trách phải là số di động Việt Nam hợp lệ.',
            'formData.reporting_years.*.in' => 'Kỳ báo cáo chỉ hỗ trợ các năm 2024, 2025 và 2026.',
            'formData.mitigation.implemented.required' => 'Vui lòng xác nhận tình trạng thực hiện biện pháp giảm nhẹ.',
            'formData.confirmation.accepted' => 'Vui lòng xác nhận tính chính xác của dữ liệu trước khi gửi.',
            'formData.inventory.*.report_url.url' => 'Đường dẫn báo cáo kiểm kê không đúng định dạng URL.',
            'formData.inventory.*.report_url.regex' => 'Đường dẫn báo cáo kiểm kê phải bắt đầu bằng http:// hoặc https://.',
            'formData.mitigation.report_url.url' => 'Đường dẫn báo cáo giảm nhẹ không đúng định dạng URL.',
            'formData.mitigation.report_url.regex' => 'Đường dẫn báo cáo giảm nhẹ phải bắt đầu bằng http:// hoặc https://.',
            'formData.inventory.*.scope1_sources.*.source_type.in' => 'Loại nguồn phát thải đã chọn không hợp lệ.',
            'formData.inventory.*.scope1_sources.*.fuel_type.in' => 'Nhiên liệu hoặc chất sử dụng đã chọn không hợp lệ.',
            'formData.inventory.*.scope1_sources.*.unit.in' => 'Đơn vị nguồn phát thải đã chọn không hợp lệ.',
            'formData.inventory.*.scope1_sources.*.quantity.numeric' => 'Lượng sử dụng phải là một số hợp lệ.',
            'formData.inventory.*.scope1_sources.*.quantity.min' => 'Lượng sử dụng không được nhỏ hơn 0.',
            'mitigation_report_file.file' => 'File báo cáo tải lên không hợp lệ.',
            'mitigation_report_file.mimes' => 'File báo cáo chỉ chấp nhận định dạng PDF, Word hoặc Excel.',
            'mitigation_report_file.extensions' => 'File báo cáo chỉ chấp nhận định dạng PDF, Word hoặc Excel.',
            'mitigation_report_file.max' => 'File báo cáo không được lớn hơn 10 MB.',
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
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

                $this->validateScopeOne($validator, (string) $year, $yearInventory);

                foreach (['grid_electricity_kwh', 'solar_electricity_kwh'] as $field) {
                    if (! array_key_exists($field, $yearInventory) || $yearInventory[$field] === null || $yearInventory[$field] === '') {
                        $validator->errors()->add(
                            "formData.inventory.{$year}.{$field}",
                            "Vui lòng cung cấp số liệu điện năng cho năm {$year}."
                        );
                    }
                }

                $reportMethod = $yearInventory['report_method'] ?? null;

                if (blank($reportMethod)) {
                    $validator->errors()->add(
                        "formData.inventory.{$year}.report_method",
                        "Vui lòng chọn hình thức cung cấp báo cáo kiểm kê cho năm {$year}."
                    );
                }

                if ($reportMethod === 'Dán link báo cáo' && blank($yearInventory['report_url'] ?? null)) {
                    $validator->errors()->add(
                        "formData.inventory.{$year}.report_url",
                        "Vui lòng cung cấp đường dẫn báo cáo kiểm kê cho năm {$year}."
                    );
                }
            }

            if (($this->input('formData.mitigation.implemented')) === true
                && blank($this->input('formData.mitigation.implemented_measures'))) {
                $validator->errors()->add(
                    'formData.mitigation.implemented_measures',
                    'Vui lòng mô tả các biện pháp giảm nhẹ đã thực hiện.'
                );
            }
        }];
    }

    /**
     * @param  array<string, mixed>  $yearInventory
     */
    private function validateScopeOne(Validator $validator, string $year, array $yearInventory): void
    {
        $basePath = "formData.inventory.{$year}";
        $sources = $yearInventory['scope1_sources'] ?? null;

        if (! is_array($sources) || $sources === []) {
            $validator->errors()->add(
                "{$basePath}.scope1_sources",
                "Vui lòng khai báo ít nhất một nguồn phát thải Phạm vi 1 cho năm {$year}."
            );
        } else {
            foreach ($sources as $index => $source) {
                if (! is_array($source)) {
                    continue;
                }

                $sourcePath = "{$basePath}.scope1_sources.{$index}";
                $this->requireValue($validator, "{$sourcePath}.source_type", $source['source_type'] ?? null, 'Vui lòng chọn loại nguồn phát thải.');
                $this->requireValue($validator, "{$sourcePath}.fuel_type", $source['fuel_type'] ?? null, 'Vui lòng chọn nhiên liệu hoặc chất sử dụng.');
                $this->requireValue($validator, "{$sourcePath}.quantity", $source['quantity'] ?? null, 'Vui lòng nhập lượng sử dụng trong năm.');
                $this->requireValue($validator, "{$sourcePath}.unit", $source['unit'] ?? null, 'Vui lòng chọn đơn vị tính.');

                if (($source['source_type'] ?? null) === 'Khác') {
                    $this->requireValue($validator, "{$sourcePath}.source_type_other", $source['source_type_other'] ?? null, 'Vui lòng nêu rõ loại nguồn phát thải khác.');
                }

                if (($source['fuel_type'] ?? null) === 'Khác') {
                    $this->requireValue($validator, "{$sourcePath}.fuel_type_other", $source['fuel_type_other'] ?? null, 'Vui lòng nêu rõ nhiên liệu hoặc chất sử dụng khác.');
                }

                if (($source['unit'] ?? null) === 'Khác') {
                    $this->requireValue($validator, "{$sourcePath}.unit_other", $source['unit_other'] ?? null, 'Vui lòng nêu rõ đơn vị tính khác.');
                }
            }
        }

        if (($yearInventory['has_boiler'] ?? false) === true) {
            $boilers = is_array($yearInventory['boilers'] ?? null) ? $yearInventory['boilers'] : [];

            if ($boilers === []) {
                $validator->errors()->add("{$basePath}.boilers", 'Vui lòng khai báo ít nhất một lò hơi.');
            }

            foreach ($boilers as $index => $boiler) {
                if (! is_array($boiler)) {
                    continue;
                }

                $boilerPath = "{$basePath}.boilers.{$index}";
                $this->requireValue($validator, "{$boilerPath}.capacity", $boiler['capacity'] ?? null, 'Vui lòng nhập công suất thiết kế lò hơi.');
                $this->requireValue($validator, "{$boilerPath}.fuel", $boiler['fuel'] ?? null, 'Vui lòng chọn nhiên liệu đốt lò hơi.');
                $this->requireValue($validator, "{$boilerPath}.consumption", $boiler['consumption'] ?? null, 'Vui lòng nhập lượng đốt trung bình trong năm.');
                $this->requireValue($validator, "{$boilerPath}.unit", $boiler['unit'] ?? null, 'Vui lòng chọn đơn vị lượng đốt lò hơi.');

                if (($boiler['fuel'] ?? null) === 'Khác') {
                    $this->requireValue($validator, "{$boilerPath}.fuel_other", $boiler['fuel_other'] ?? null, 'Vui lòng nêu rõ nhiên liệu đốt lò hơi khác.');
                }
            }
        }

        if (($yearInventory['has_cooling'] ?? false) === true) {
            $refrigerationSystems = is_array($yearInventory['refrigeration_systems'] ?? null)
                ? $yearInventory['refrigeration_systems']
                : [];

            if ($refrigerationSystems === []) {
                $validator->errors()->add("{$basePath}.refrigeration_systems", 'Vui lòng khai báo ít nhất một hệ thống lạnh.');
            }

            foreach ($refrigerationSystems as $index => $refrigeration) {
                if (! is_array($refrigeration)) {
                    continue;
                }

                $refrigerationPath = "{$basePath}.refrigeration_systems.{$index}";
                $this->requireValue($validator, "{$refrigerationPath}.equipment", $refrigeration['equipment'] ?? null, 'Vui lòng chọn thiết bị lạnh sử dụng.');
                $this->requireValue($validator, "{$refrigerationPath}.capacity", $refrigeration['capacity'] ?? null, 'Vui lòng nhập công suất lạnh.');
                $this->requireValue($validator, "{$refrigerationPath}.gas_type", $refrigeration['gas_type'] ?? null, 'Vui lòng chọn loại môi chất lạnh.');
                $this->requireValue($validator, "{$refrigerationPath}.full_charge_kg", $refrigeration['full_charge_kg'] ?? null, 'Vui lòng nhập lượng gas khi nạp đầy.');

                if (($refrigeration['equipment'] ?? null) === 'Khác') {
                    $this->requireValue($validator, "{$refrigerationPath}.equipment_other", $refrigeration['equipment_other'] ?? null, 'Vui lòng nêu rõ thiết bị lạnh khác.');
                }

                if (($refrigeration['gas_type'] ?? null) === 'Khác') {
                    $this->requireValue($validator, "{$refrigerationPath}.gas_type_other", $refrigeration['gas_type_other'] ?? null, 'Vui lòng nêu rõ môi chất lạnh khác.');
                }
            }
        }
    }

    private function requireValue(Validator $validator, string $path, mixed $value, string $message): void
    {
        if (blank($value)) {
            $validator->errors()->add($path, $message);
        }
    }
}
