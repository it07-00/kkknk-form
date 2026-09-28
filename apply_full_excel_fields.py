import sys
import re

sys.stdout.reconfigure(encoding='utf-8')

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# -------------------------------------------------------------------------
# 1. ADD COMPUTED PROPERTY & METHODS IN ghgApp()
# -------------------------------------------------------------------------

# Add mitigationEfficiencyPercent computed getter
getter_target = "get stepProgressPercent() {"
getter_code = """        get mitigationEfficiencyPercent() {
          const planned = parseFloat(this.formData.mitigation.planned_reduction_tco2e);
          const actual = parseFloat(this.formData.mitigation.actual_reduction_tco2e);
          if (!planned || planned <= 0 || isNaN(actual)) return null;
          return Math.round((actual / planned) * 1000) / 10;
        },

        get stepProgressPercent() {"""

if getter_target in content and "get mitigationEfficiencyPercent()" not in content:
    content = content.replace(getter_target, getter_code, 1)
    print("Added mitigationEfficiencyPercent getter!")

# Update selectReportingOption to include 2026 and 2024_2026
old_select_reporting = """        selectReportingOption(opt) {
          this.reportingOption = opt;
          if (opt === '2024') {
            this.formData.reporting_years = ['2024'];
            this.activeScope1Year = '2024';
          } else if (opt === '2025') {
            this.formData.reporting_years = ['2025'];
            this.activeScope1Year = '2025';
          } else {
            this.formData.reporting_years = ['2024', '2025'];
            this.activeScope1Year = '2024';
          }
          this.clearFieldError('reporting_years');
        },"""

new_select_reporting = """        selectReportingOption(opt) {
          this.reportingOption = opt;
          if (opt === '2024') {
            this.formData.reporting_years = ['2024'];
            this.activeScope1Year = '2024';
          } else if (opt === '2025') {
            this.formData.reporting_years = ['2025'];
            this.activeScope1Year = '2025';
          } else if (opt === '2026') {
            this.formData.reporting_years = ['2026'];
            this.activeScope1Year = '2026';
          } else if (opt === '2024_2026') {
            this.formData.reporting_years = ['2024', '2025', '2026'];
            this.activeScope1Year = '2024';
          } else {
            this.formData.reporting_years = ['2024', '2025'];
            this.activeScope1Year = '2024';
          }
          // Ensure year inventory exists
          for (const yr of this.formData.reporting_years) {
            this.ensureYearInventory(yr);
          }
          this.clearFieldError('reporting_years');
        },

        ensureYearInventory(yr) {
          if (!this.formData.inventory[yr]) {
            this.formData.inventory[yr] = {
              has_scope1: null,
              has_boiler: false,
              boiler: { capacity: '', fuel: 'Sinh khối', fuel_other: '', consumption: null, unit: 'tấn/năm' },
              has_cooling: false,
              refrigeration: { equipment: 'Máy lạnh', equipment_other: '', capacity: '2 HP', gas_type: 'R22', gas_type_other: '', full_charge_kg: null, recharge_kg: null },
              scope1_sources: [],
              grid_electricity_kwh: null,
              solar_electricity_kwh: null,
              energy_toe: null,
              scope1_emissions: null,
              scope2_emissions: null,
              report_method: 'Chưa có báo cáo',
              report_url: ''
            };
          }
        },

        insertMitigationMeasure(text) {
          if (!this.formData.mitigation.plan_2026_2030) {
            this.formData.mitigation.plan_2026_2030 = text;
          } else if (!this.formData.mitigation.plan_2026_2030.includes(text)) {
            this.formData.mitigation.plan_2026_2030 += '\\n' + text;
          }
          this.showToast('Đã thêm biện pháp vào kế hoạch');
        },

        fillTakigawaSampleData() {
          this.formData.company.name = 'Công ty TNHH Takigawa Việt Nam';
          this.formData.company.tax_code = '3701858627';
          this.formData.company.address = 'Số 10, đường số 14, khu công nghiệp VSIP II-A, phường Bình Hòa, Thành phố Hồ Chí Minh';
          this.formData.company.industry = 'Sản xuất, in ấn, thiết kế bao bì';
          this.formData.company.email = 'info@takigawa.vn';
          this.formData.company.legal_representative.name = 'Ông Takigawa Hiroshi';
          this.formData.company.legal_representative.phone = '02743841777';
          this.formData.company.technical_contact.name = 'Chị Nguyệt Sương';
          this.formData.company.technical_contact.phone = '0903841777';

          this.selectReportingOption('2024_2026');

          // Year 2024
          const y24 = this.formData.inventory['2024'];
          y24.has_scope1 = true;
          y24.has_boiler = true;
          y24.boiler = {
            capacity: '3 tấn hơi/giờ',
            fuel: 'Sinh khối',
            fuel_other: '',
            consumption: 500,
            unit: 'tấn/năm'
          };
          y24.has_cooling = true;
          y24.refrigeration = {
            equipment: 'Máy lạnh',
            equipment_other: '',
            capacity: '2 HP',
            gas_type: 'R22',
            gas_type_other: '',
            full_charge_kg: 10,
            recharge_kg: 2
          };
          y24.scope1_sources = [
            {
              id: 'src_takigawa_1',
              source_type: 'Đốt nhiên liệu di động',
              source_type_other: '',
              fuel_type: 'Dầu DO',
              fuel_type_other: '',
              quantity: 15000,
              unit: 'lít',
              unit_other: '',
              note: 'Phương tiện vận tải của Công ty'
            }
          ];
          y24.grid_electricity_kwh = 3000000;
          y24.solar_electricity_kwh = 300000;
          y24.energy_toe = 1900;
          y24.scope1_emissions = 2000;
          y24.scope2_emissions = 1200;
          y24.report_method = 'Kê khai trực tiếp theo hóa đơn';

          // Year 2025
          const y25 = this.formData.inventory['2025'];
          y25.has_scope1 = true;
          y25.has_boiler = true;
          y25.boiler = {
            capacity: '3 tấn hơi/giờ',
            fuel: 'Sinh khối',
            fuel_other: '',
            consumption: 500,
            unit: 'tấn/năm'
          };
          y25.has_cooling = true;
          y25.refrigeration = {
            equipment: 'Máy lạnh',
            equipment_other: '',
            capacity: '2 HP',
            gas_type: 'R22',
            gas_type_other: '',
            full_charge_kg: 10,
            recharge_kg: 2
          };
          y25.grid_electricity_kwh = 3400000;
          y25.solar_electricity_kwh = 300000;
          y25.energy_toe = 1850;
          y25.scope1_emissions = 2000;
          y25.scope2_emissions = 1350;
          y25.report_method = 'Kê khai trực tiếp theo hóa đơn';

          // Year 2026
          const y26 = this.formData.inventory['2026'];
          y26.has_scope1 = true;
          y26.has_boiler = true;
          y26.boiler = {
            capacity: '3 tấn hơi/giờ',
            fuel: 'Sinh khối',
            fuel_other: '',
            consumption: 500,
            unit: 'tấn/năm'
          };
          y26.has_cooling = true;
          y26.refrigeration = {
            equipment: 'Máy lạnh',
            equipment_other: '',
            capacity: '2 HP',
            gas_type: 'R22',
            gas_type_other: '',
            full_charge_kg: 10,
            recharge_kg: 2
          };
          y26.grid_electricity_kwh = 3800000;
          y26.solar_electricity_kwh = 290000;
          y26.energy_toe = 1880;
          y26.scope1_emissions = 2000;
          y26.scope2_emissions = 1500;
          y26.report_method = 'Kê khai trực tiếp theo hóa đơn';

          // Mitigation
          this.formData.mitigation.implemented = true;
          this.formData.mitigation.plan_2026_2030 = `- Lắp đặt hệ thống điện mặt trời mái nhà để tự dùng.\\n- Cải tiến, nâng cấp lò hơi, lò nung và thiết bị sản xuất.\\n- Tối ưu hóa quy trình công nghệ và giảm tiêu hao nhiên liệu.\\n- Thay thế thiết bị, động cơ hiệu suất thấp bằng thiết bị hiệu suất cao.\\n- Điện khí hóa phương tiện, thiết bị vận chuyển và bốc xếp trong cơ sở.\\n- Giảm rò rỉ nhiên liệu, khí gas và môi chất lạnh.\\n- Tăng cường thu hồi, tái sử dụng nhiệt thải.`;
          this.formData.mitigation.implemented_measures = `- Gắn pin năng lượng mặt trời mái nhà 1 Mw.\\n- Thay máy thổi khí cho công trình xử lý nước thải;\\n- Thay xe nâng điện`;
          this.formData.mitigation.planned_reduction_tco2e = 1000;
          this.formData.mitigation.actual_reduction_tco2e = 200;

          this.showToast('Đã nạp thành công số liệu mẫu Takigawa từ file Excel!');
          this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },"""

if old_select_reporting in content:
    content = content.replace(old_select_reporting, new_select_reporting, 1)
    print("Updated selectReportingOption & added helpers!")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)
print("Step 1 done!")
