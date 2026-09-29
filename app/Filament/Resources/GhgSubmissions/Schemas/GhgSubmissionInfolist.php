<?php

namespace App\Filament\Resources\GhgSubmissions\Schemas;

use App\Models\GhgSubmission;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GhgSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin hồ sơ')
                    ->schema([
                        TextEntry::make('code')->label('Mã hồ sơ')->copyable(),
                        TextEntry::make('status')->label('Trạng thái')->badge(),
                        TextEntry::make('company_name')->label('Doanh nghiệp')->placeholder('-'),
                        TextEntry::make('tax_id')->label('Mã số thuế')->placeholder('-'),
                        TextEntry::make('reporting_years')->label('Kỳ kiểm kê')->badge(),
                        TextEntry::make('created_at')->label('Tiếp nhận lúc')->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                Section::make('Thông tin liên hệ')
                    ->schema([
                        TextEntry::make('data.company.address')->label('Địa chỉ')->placeholder('-'),
                        TextEntry::make('data.company.industry')->label('Ngành nghề')->placeholder('-'),
                        TextEntry::make('data.company.email')->label('Email')->placeholder('-'),
                        TextEntry::make('data.company.legal_representative.name')->label('Người đại diện')->placeholder('-'),
                        TextEntry::make('data.company.legal_representative.phone')->label('Điện thoại người đại diện')->placeholder('-'),
                        TextEntry::make('data.company.technical_contact.name')->label('Cán bộ phụ trách')->placeholder('-'),
                        TextEntry::make('data.company.technical_contact.phone')->label('Điện thoại cán bộ')->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Số liệu kiểm kê')
                    ->schema([
                        TextEntry::make('inventory_summary')
                            ->label('Theo từng năm')
                            ->state(fn (GhgSubmission $record): array => self::inventorySummary($record))
                            ->listWithLineBreaks()
                            ->bulleted(),
                    ]),
                Section::make('Kế hoạch giảm nhẹ')
                    ->schema([
                        IconEntry::make('data.mitigation.implemented')
                            ->label('Đã thực hiện biện pháp')
                            ->boolean(),
                        TextEntry::make('data.mitigation.plan_2026_2030')
                            ->label('Kế hoạch 2026–2030')
                            ->placeholder('-'),
                        TextEntry::make('data.mitigation.implemented_measures')
                            ->label('Biện pháp đã thực hiện')
                            ->placeholder('-'),
                        TextEntry::make('data.mitigation.planned_reduction_tco2e')
                            ->label('Cắt giảm dự kiến (tCO₂e)')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('data.mitigation.actual_reduction_tco2e')
                            ->label('Cắt giảm thực tế (tCO₂e)')
                            ->numeric()
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Tiếp nhận và xử lý')
                    ->schema([
                        TextEntry::make('ip_address')->label('Địa chỉ IP')->placeholder('-'),
                        TextEntry::make('reviewer.name')->label('Người xử lý')->placeholder('Chưa xử lý'),
                        TextEntry::make('reviewed_at')->label('Xử lý lúc')->dateTime('d/m/Y H:i')->placeholder('-'),
                        TextEntry::make('mitigation_report_original_name')->label('File báo cáo đã nộp')->placeholder('Không có'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    /**
     * @return array<int, string>
     */
    private static function inventorySummary(GhgSubmission $submission): array
    {
        $inventories = data_get($submission->data, 'inventory', []);

        return collect($inventories)
            ->map(function (array $inventory, string|int $year): string {
                $gridElectricity = number_format((float) ($inventory['grid_electricity_kwh'] ?? 0), 0, ',', '.');
                $solarElectricity = number_format((float) ($inventory['solar_electricity_kwh'] ?? 0), 0, ',', '.');
                $scopeOne = number_format((float) ($inventory['scope1_emissions'] ?? 0), 2, ',', '.');
                $scopeTwo = number_format((float) ($inventory['scope2_emissions'] ?? 0), 2, ',', '.');

                return "Năm {$year}: điện lưới {$gridElectricity} kWh; điện mặt trời {$solarElectricity} kWh; Phạm vi 1 {$scopeOne} tCO₂e; Phạm vi 2 {$scopeTwo} tCO₂e.";
            })
            ->values()
            ->all();
    }
}
