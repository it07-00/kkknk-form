<?php

namespace Database\Factories;

use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GhgSubmission>
 */
class GhgSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'GHG-'.now()->format('Y').'-'.Str::upper(Str::random(10)),
            'company_name' => fake()->company(),
            'tax_id' => fake()->unique()->numerify('03########'),
            'reporting_years' => ['2024'],
            'data' => fn (array $attributes): array => [
                'company' => [
                    'name' => $attributes['company_name'],
                    'tax_code' => $attributes['tax_id'],
                    'address' => '123 Nguyễn Huệ, TP.HCM',
                    'industry' => 'Sản xuất xanh',
                    'email' => 'contact@example.com',
                    'legal_representative' => ['name' => 'Nguyễn Văn A', 'phone' => '0901000000'],
                    'technical_contact' => ['name' => 'Trần Thị B', 'phone' => '0902000000'],
                ],
                'reporting_years' => ['2024'],
                'inventory' => [
                    '2024' => [
                        'has_scope1' => false,
                        'grid_electricity_kwh' => 50000,
                        'solar_electricity_kwh' => 0,
                        'report_method' => 'Chưa có báo cáo',
                    ],
                ],
                'mitigation' => ['implemented' => false],
                'confirmation' => true,
            ],
            'status' => GhgSubmissionStatus::Submitted,
            'ip_address' => '127.0.0.1',
        ];
    }
}
