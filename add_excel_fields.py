import sys
import re

sys.stdout.reconfigure(encoding='utf-8')

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# -------------------------------------------------------------
# 1. Update JS data model in ghgApp()
# -------------------------------------------------------------
# Find inventory object initialization
old_inventory_init = """          inventory: {
            '2024': {
              has_scope1: null,
              scope1_sources: [],
              grid_electricity_kwh: null,
              solar_electricity_kwh: null,
              scope1_emissions: null,
              scope2_emissions: null,
              report_method: 'Chưa có báo cáo',
              report_url: ''
            },
            '2025': {
              has_scope1: null,
              scope1_sources: [],
              grid_electricity_kwh: null,
              solar_electricity_kwh: null,
              scope1_emissions: null,
              scope2_emissions: null,
              report_method: 'Chưa có báo cáo',
              report_url: ''
            }
          },"""

new_inventory_init = """          inventory: {
            '2024': {
              has_scope1: null,
              has_boiler: false,
              boiler: {
                capacity: '',
                fuel: 'Sinh khối',
                fuel_other: '',
                consumption: null,
                unit: 'tấn/năm'
              },
              has_cooling: false,
              refrigeration: {
                equipment: 'Máy lạnh',
                equipment_other: '',
                capacity: '2 HP',
                gas_type: 'R22',
                gas_type_other: '',
                full_charge_kg: null,
                recharge_kg: null
              },
              scope1_sources: [],
              grid_electricity_kwh: null,
              solar_electricity_kwh: null,
              energy_toe: null,
              scope1_emissions: null,
              scope2_emissions: null,
              report_method: 'Chưa có báo cáo',
              report_url: ''
            },
            '2025': {
              has_scope1: null,
              has_boiler: false,
              boiler: {
                capacity: '',
                fuel: 'Sinh khối',
                fuel_other: '',
                consumption: null,
                unit: 'tấn/năm'
              },
              has_cooling: false,
              refrigeration: {
                equipment: 'Máy lạnh',
                equipment_other: '',
                capacity: '2 HP',
                gas_type: 'R22',
                gas_type_other: '',
                full_charge_kg: null,
                recharge_kg: null
              },
              scope1_sources: [],
              grid_electricity_kwh: null,
              solar_electricity_kwh: null,
              energy_toe: null,
              scope1_emissions: null,
              scope2_emissions: null,
              report_method: 'Chưa có báo cáo',
              report_url: ''
            },
            '2026': {
              has_scope1: null,
              has_boiler: false,
              boiler: {
                capacity: '',
                fuel: 'Sinh khối',
                fuel_other: '',
                consumption: null,
                unit: 'tấn/năm'
              },
              has_cooling: false,
              refrigeration: {
                equipment: 'Máy lạnh',
                equipment_other: '',
                capacity: '2 HP',
                gas_type: 'R22',
                gas_type_other: '',
                full_charge_kg: null,
                recharge_kg: null
              },
              scope1_sources: [],
              grid_electricity_kwh: null,
              solar_electricity_kwh: null,
              energy_toe: null,
              scope1_emissions: null,
              scope2_emissions: null,
              report_method: 'Chưa có báo cáo',
              report_url: ''
            }
          },"""

if old_inventory_init in content:
    content = content.replace(old_inventory_init, new_inventory_init)
    print("Updated inventory data model!")
else:
    print("WARNING: old_inventory_init not found exactly, will search with regex")

# Check if replacement worked
print("Inventory check:", "'2026': {" in content)

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)
