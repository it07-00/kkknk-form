import sys
import re

sys.stdout.reconfigure(encoding='utf-8')

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# -------------------------------------------------------------
# 1. INTRO CARD: Add Demo Data Fill Button
# -------------------------------------------------------------
intro_target = """              <!-- Dữ liệu cần chuẩn bị box -->
              <div class="bg-[#F8FAF9] border border-borderui rounded-2xl p-5 space-y-3">"""

intro_replacement = """              <!-- Quick Demo Data Action Button (Matching Excel File) -->
              <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-2xl bg-[#003c33]/5 border border-[#003c33]/15">
                <div class="flex items-center space-x-2.5">
                  <span class="w-8 h-8 rounded-xl bg-[#003c33] text-[#9fe870] flex items-center justify-center font-bold text-xs">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                  </span>
                  <div>
                    <span class="text-xs font-bold text-[#003c33]">Dữ liệu mẫu từ Sở Công Thương:</span>
                    <span class="text-xs text-txsecondary block">Bộ số liệu thực tế Công ty TNHH Takigawa Việt Nam (theo file Excel)</span>
                  </div>
                </div>
                <button type="button"
                        @click="fillTakigawaSampleData()"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-[#003c33] bg-[#9fe870] hover:bg-[#8ee05b] shadow-sm transition-all cursor-pointer">
                  <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1.5 text-[#003c33]"></i>
                  <span>Nạp số liệu mẫu tự động</span>
                </button>
              </div>

              <!-- Dữ liệu cần chuẩn bị box -->
              <div class="bg-[#F8FAF9] border border-borderui rounded-2xl p-5 space-y-3">"""

if intro_target in content:
    content = content.replace(intro_target, intro_replacement, 1)
    print("Added demo data button in Intro card!")
else:
    print("WARNING: intro_target not found")

# -------------------------------------------------------------
# 2. STEP 2: Update Reporting Year Cards (Add 2024–2026 Option)
# -------------------------------------------------------------
step2_grid_target = """                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  
                  <!-- Option: 2024 -->"""

step2_grid_replacement = """                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                  
                  <!-- Option: 2024 -->"""

content = content.replace(step2_grid_target, step2_grid_replacement, 1)

# Find the end of Option: Both 2024 & 2025 to insert Option 4 (2024–2026)
step2_both_end = """                    <div class="mt-5">
                      <div class="text-base font-bold text-txprimary">Năm 2024 & 2025</div>
                      <p class="text-xs text-txsecondary mt-1 leading-relaxed">Khai báo liên tục cho cả hai năm. Hệ thống sẽ phân tách tab nhập liệu riêng.</p>
                    </div>
                  </div>

                </div>"""

step2_both_replacement = """                    <div class="mt-5">
                      <div class="text-base font-bold text-txprimary">Năm 2024 & 2025</div>
                      <p class="text-xs text-txsecondary mt-1 leading-relaxed">Khai báo cho cả hai năm. Hệ thống phân tách tab nhập liệu riêng.</p>
                    </div>
                  </div>

                  <!-- Option: Chu kỳ 2024–2026 (Theo mẫu Excel) -->
                  <div @click="selectReportingOption('2024_2026')"
                       role="radio"
                       tabindex="0"
                       :aria-checked="reportingOption === '2024_2026'"
                       @keydown.space.prevent="selectReportingOption('2024_2026')"
                       class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between"
                       :class="reportingOption === '2024_2026' ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9]'">
                    <div class="flex items-center justify-between">
                      <div class="w-11 h-11 rounded-2xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center font-bold">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#003c33] text-[#9fe870] shadow-sm">Mẫu Excel</span>
                        <div class="w-6 h-6 rounded-full border flex items-center justify-center transition-all"
                             :class="reportingOption === '2024_2026' ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'">
                          <i x-show="reportingOption === '2024_2026'" data-lucide="check" class="w-4 h-4"></i>
                        </div>
                      </div>
                    </div>
                    <div class="mt-5">
                      <div class="text-base font-bold text-txprimary">Năm 2024 – 2026</div>
                      <p class="text-xs text-txsecondary mt-1 leading-relaxed">Khai báo chu kỳ 3 năm gồm 2024, 2025 và 2026 (theo mẫu biểu Excel).</p>
                    </div>
                  </div>

                </div>"""

if step2_both_end in content:
    content = content.replace(step2_both_end, step2_both_replacement, 1)
    print("Added Option 4 (2024-2026) in Step 2!")
else:
    print("WARNING: step2_both_end not found")

# -------------------------------------------------------------
# 3. STEP 3: Add Boiler & Refrigeration Sections in Scope 1
# -------------------------------------------------------------
scope1_container_target = """                <!-- SCOPE 1 = CÓ: DYNAMIC REPEATER CARD -->
                <div x-show="formData.inventory[activeScope1Year].has_scope1 === true" class="space-y-5 pt-2">"""

scope1_boiler_and_cooling = """                <!-- SCOPE 1 = CÓ: DYNAMIC REPEATER CARD -->
                <div x-show="formData.inventory[activeScope1Year].has_scope1 === true" class="space-y-6 pt-2">

                  <!-- 1. KHỐI LÒ HƠI / NỒI HƠI (Khớp cột K, L, M file Excel) -->
                  <div class="bg-white rounded-2xl border border-borderui p-5 sm:p-6 shadow-subtle space-y-4">
                    <div class="flex items-center justify-between pb-3.5 border-b border-borderui">
                      <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                          <i data-lucide="flame" class="w-5 h-5 text-amber-700"></i>
                        </div>
                        <div>
                          <div class="text-sm font-bold text-txprimary">1. Lò hơi / Nồi hơi công nghiệp (Boiler)</div>
                          <div class="text-xs text-txsecondary">Thu thập công suất thiết kế, loại nhiên liệu đốt và lượng đốt/năm theo biểu mẫu Sở Công Thương</div>
                        </div>
                      </div>
                      
                      <!-- Toggle Switch -->
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="formData.inventory[activeScope1Year].has_boiler" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003c33]"></div>
                        <span class="ml-2.5 text-xs font-semibold" :class="formData.inventory[activeScope1Year].has_boiler ? 'text-[#003c33] font-bold' : 'text-txsecondary'" x-text="formData.inventory[activeScope1Year].has_boiler ? 'Có sử dụng lò hơi' : 'Không có lò hơi'"></span>
                      </label>
                    </div>

                    <!-- Boiler Form Fields -->
                    <div x-show="formData.inventory[activeScope1Year].has_boiler" x-transition class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                      <!-- Công suất lò hơi -->
                      <div>
                        <label class="block text-sm font-medium text-txprimary mb-1.5">
                          Công suất thiết kế lò hơi <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               x-model="formData.inventory[activeScope1Year].boiler.capacity"
                               placeholder="Ví dụ: 3 tấn hơi/giờ hoặc 3.5"
                               class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring">
                        <span class="text-xs text-txsecondary mt-1 block">Khớp cột: Công suất (tấn hơi/giờ)</span>
                      </div>

                      <!-- Nhiên liệu đốt lò hơi -->
                      <div>
                        <label class="block text-sm font-medium text-txprimary mb-1.5">
                          Nhiên liệu đốt lò hơi <span class="text-danger">*</span>
                        </label>
                        <select x-model="formData.inventory[activeScope1Year].boiler.fuel"
                                class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring">
                          <option value="Sinh khối">Sinh khối (Biomass, trấu, mùn cưa, dăm gỗ...)</option>
                          <option value="Than đá">Than đá / Than cám</option>
                          <option value="Dầu DO">Dầu DO (Diesel)</option>
                          <option value="Dầu FO">Dầu FO (Fuel Oil)</option>
                          <option value="LPG">LPG (Khí hóa lỏng)</option>
                          <option value="Khí tự nhiên">Khí tự nhiên (CNG, LNG)</option>
                          <option value="Củi gỗ">Củi gỗ</option>
                          <option value="Viên nén">Viên nén mùn cưa (Pellets)</option>
                          <option value="Khác">Nhiên liệu khác</option>
                        </select>
                        <span class="text-xs text-txsecondary mt-1 block">Khớp cột: Nhiên liệu đốt lò hơi</span>
                      </div>

                      <!-- Lượng đốt trung bình/năm -->
                      <div>
                        <label class="block text-sm font-medium text-txprimary mb-1.5">
                          Lượng đốt trung bình/năm <span class="text-danger">*</span>
                        </label>
                        <div class="flex space-x-2">
                          <input type="number"
                                 step="any"
                                 min="0"
                                 x-model.number="formData.inventory[activeScope1Year].boiler.consumption"
                                 placeholder="Ví dụ: 500"
                                 class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring">
                          <select x-model="formData.inventory[activeScope1Year].boiler.unit"
                                  class="w-28 h-11 px-2.5 rounded-xl border border-borderui bg-white text-sm focus-ring">
                            <option value="tấn/năm">tấn/năm</option>
                            <option value="kg/năm">kg/năm</option>
                            <option value="m³/năm">m³/năm</option>
                            <option value="lít/năm">lít/năm</option>
                          </select>
                        </div>
                        <span class="text-xs text-txsecondary mt-1 block">Khớp cột: Lượng đốt trung bình/năm</span>
                      </div>
                    </div>
                  </div>

                  <!-- 2. KHỐI MÔI CHẤT LẠNH & ĐIỀU HÒA (Khớp cột N, O, P, Q file Excel) -->
                  <div class="bg-white rounded-2xl border border-borderui p-5 sm:p-6 shadow-subtle space-y-4">
                    <div class="flex items-center justify-between pb-3.5 border-b border-borderui">
                      <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-800 flex items-center justify-center font-bold">
                          <i data-lucide="snowflake" class="w-5 h-5 text-sky-700"></i>
                        </div>
                        <div>
                          <div class="text-sm font-bold text-txprimary">2. Thiết bị làm lạnh & Môi chất lạnh (HVAC / Chiller / Điều hòa)</div>
                          <div class="text-xs text-txsecondary">Thu thập loại thiết bị, công suất làm lạnh, loại gas và lượng nạp đầy theo biểu mẫu Sở Công Thương</div>
                        </div>
                      </div>
                      
                      <!-- Toggle Switch -->
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="formData.inventory[activeScope1Year].has_cooling" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003c33]"></div>
                        <span class="ml-2.5 text-xs font-semibold" :class="formData.inventory[activeScope1Year].has_cooling ? 'text-[#003c33] font-bold' : 'text-txsecondary'" x-text="formData.inventory[activeScope1Year].has_cooling ? 'Có sử dụng hệ thống lạnh' : 'Không có hệ thống lạnh'"></span>
                      </label>
                    </div>

                    <!-- Cooling Form Fields -->
                    <div x-show="formData.inventory[activeScope1Year].has_cooling" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
                      <!-- Thiết bị lạnh sử dụng -->
                      <div>
                        <label class="block text-sm font-medium text-txprimary mb-1.5">
                          Thiết bị lạnh sử dụng <span class="text-danger">*</span>
                        </label>
                        <select x-model="formData.inventory[activeScope1Year].refrigeration.equipment"
                                class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring">
                          <option value="Máy lạnh">Máy lạnh dân dụng / cục bộ</option>
                          <option value="Chiller">Hệ thống Chiller làm lạnh nước</option>
                          <option value="VRV/VRF">Điều hòa trung tâm VRV / VRF</option>
                          <option value="Kho lạnh">Kho lạnh công nghiệp / Tủ đông</option>
                          <option value="Khác">Thiết bị lạnh khác</option>
                        </select>
                        <span class="text-xs text-txsecondary mt-1 block">Khớp cột: Thiết bị lạnh sử dụng</span>
                      </div>

                      <!-- Công suất lạnh -->
                      <div>
                        <label class="block text-sm font-medium text-txprimary mb-1.5">
                          Công suất lạnh <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               x-model="formData.inventory[activeScope1Year].refrigeration.capacity"
                               placeholder="Ví dụ: 2 HP, 50 HP, 60.000 BTU/h"
                               class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring">
                        <span class="text-xs text-txsecondary mt-1 block">Khớp cột: Công suất lạnh (HP hoặc BTU/giờ)</span>
                      </div>

                      <!-- Loại môi chất lạnh -->
                      <div>
                        <label class="block text-sm font-medium text-txprimary mb-1.5">
                          Loại môi chất lạnh (Gas) <span class="text-danger">*</span>
                        </label>
                        <select x-model="formData.inventory[activeScope1Year].refrigeration.gas_type"
                                class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring">
                          <option value="R22">R22</option>
                          <option value="R410A">R410A</option>
                          <option value="R134a">R134a</option>
                          <option value="R32">R32</option>
                          <option value="R404A">R404A</option>
                          <option value="R407C">R407C</option>
                          <option value="R507A">R507A</option>
                          <option value="Khác">Gas lạnh khác</option>
                        </select>
                        <span class="text-xs text-txsecondary mt-1 block">Khớp cột: Loại môi chất lạnh</span>
                      </div>

                      <!-- Lượng môi chất nạp đầy (kg) -->
                      <div>
                        <label class="block text-sm font-medium text-txprimary mb-1.5">
                          Lượng gas khi nạp đầy (kg) <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               step="any"
                               min="0"
                               x-model.number="formData.inventory[activeScope1Year].refrigeration.full_charge_kg"
                               placeholder="Ví dụ: 10 (kg)"
                               class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring">
                        <span class="text-xs text-txsecondary mt-1 block">Khớp cột: Lượng môi chất nạp đầy</span>
                      </div>
                    </div>
                  </div>

                  <!-- 3. CÁC NGUỒN PHÁT THẢI TRỰC TIẾP KHÁC -->
                  <div class="border-t border-borderui pt-5">
                    <h4 class="text-base font-bold text-txprimary mb-1">3. Các nguồn phát thải trực tiếp khác (Xe cộ, máy phát điện...)</h4>
                    <p class="text-xs text-txsecondary mb-4">Khai báo phương tiện vận tải công ty (xe nâng, xe tải), máy phát điện dự phòng, xử lý chất thải hoặc các nguồn đốt khác.</p>
                  </div>"""

if scope1_container_target in content:
    content = content.replace(scope1_container_target, scope1_boiler_and_cooling, 1)
    print("Added Boiler and Cooling sections in Step 3!")
else:
    print("WARNING: scope1_container_target not found")

# -------------------------------------------------------------
# 4. STEP 4: Add TOE Field in Scope 2
# -------------------------------------------------------------
step4_solar_end = """                        <template x-if="hasError('inventory.' + yr + '.solar_electricity_kwh')">
                          <p class="text-xs text-danger mt-1.5 flex items-center space-x-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            <span x-text="getErrorMessage('inventory.' + yr + '.solar_electricity_kwh')"></span>
                          </p>
                        </template>
                      </div>

                    </div>"""

step4_toe_replacement = """                        <template x-if="hasError('inventory.' + yr + '.solar_electricity_kwh')">
                          <p class="text-xs text-danger mt-1.5 flex items-center space-x-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            <span x-text="getErrorMessage('inventory.' + yr + '.solar_electricity_kwh')"></span>
                          </p>
                        </template>
                      </div>

                      <!-- Tổng mức tiêu thụ năng lượng quy đổi (TOE/năm) - Khớp cột R file Excel -->
                      <div class="sm:col-span-2 pt-4 border-t border-borderui">
                        <div class="flex items-center justify-between mb-1.5">
                          <label :for="'energy_toe_' + yr" class="block text-sm font-medium text-txprimary">
                            Tổng mức tiêu thụ năng lượng quy đổi trong năm (TOE/năm)
                          </label>
                          <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-[#003c33]/10 text-[#003c33]">Cột TOE trong file Excel</span>
                        </div>
                        <div class="relative">
                          <input type="number"
                                 :id="'energy_toe_' + yr"
                                 step="any"
                                 min="0"
                                 x-model.number="formData.inventory[yr].energy_toe"
                                 placeholder="Ví dụ: 1900 (Nếu chưa có thể ước tính hoặc để trống)"
                                 class="w-full h-12 pl-4 pr-28 rounded-xl border border-borderui bg-white text-txprimary text-sm font-mono focus-ring transition-all">
                          <span class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium">TOE/năm</span>
                        </div>
                        <p class="text-xs text-txsecondary mt-1.5">
                          <strong>Tấn dầu tương đương (TOE):</strong> Tổng điện và nhiên liệu tiêu thụ quy đổi theo Luật Sử dụng năng lượng tiết kiệm và hiệu quả. Cơ sở sử dụng năng lượng trọng điểm (≥ 1.000 TOE đối với sản xuất công nghiệp, ≥ 500 TOE đối với tòa nhà).
                        </p>
                      </div>

                    </div>"""

if step4_solar_end in content:
    content = content.replace(step4_solar_end, step4_toe_replacement, 1)
    print("Added TOE field in Step 4!")
else:
    print("WARNING: step4_solar_end not found")

# -------------------------------------------------------------
# 5. STEP 6: Add Efficiency % and Quick-Insert Suggestions
# -------------------------------------------------------------
step6_numbers_end = """                      <span class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium">tấn CO₂e</span>
                    </div>
                  </div>

                </div>"""

step6_efficiency_and_suggestions = """                      <span class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium">tấn CO₂e</span>
                    </div>
                  </div>

                  <!-- Hiệu quả % giảm nhẹ so với kế hoạch (Tự động tính toán - Khớp cột K file Excel) -->
                  <div class="sm:col-span-2 bg-[#F0F6F3] border border-[#003c33]/20 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-subtle">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 rounded-xl bg-[#003c33] text-[#9fe870] flex items-center justify-center font-bold flex-shrink-0">
                        <i data-lucide="percent" class="w-5 h-5"></i>
                      </div>
                      <div>
                        <div class="text-sm font-bold text-txprimary">Hiệu quả % giảm nhẹ so với kế hoạch (%)</div>
                        <div class="text-xs text-txsecondary">Khớp cột K trong file Excel Sở Công Thương (Công thức: Lượng thực tế ÷ Lượng kế hoạch × 100%)</div>
                      </div>
                    </div>
                    <div class="flex items-center space-x-3 sm:text-right">
                      <template x-if="mitigationEfficiencyPercent !== null">
                        <div class="flex items-center space-x-2">
                          <span class="text-2xl font-mono font-extrabold text-[#003c33]" x-text="mitigationEfficiencyPercent + '%'"></span>
                          <span class="px-2.5 py-1 rounded-lg text-xs font-bold"
                                :class="mitigationEfficiencyPercent >= 100 ? 'bg-[#9fe870] text-[#003c33]' : 'bg-[#003c33]/15 text-[#003c33]'"
                                x-text="mitigationEfficiencyPercent >= 100 ? 'Đạt chỉ tiêu' : 'Đang triển khai'"></span>
                        </div>
                      </template>
                      <template x-if="mitigationEfficiencyPercent === null">
                        <span class="text-xs text-txsecondary italic">Nhập kế hoạch và thực tế để tự động tính %</span>
                      </template>
                    </div>
                  </div>

                  <!-- Danh sách gợi ý giải pháp mẫu từ Sở Công Thương (Bấm để thêm nhanh) -->
                  <div class="sm:col-span-2 pt-2">
                    <span class="text-xs font-semibold text-txsecondary uppercase tracking-wider block mb-2">Gợi ý các giải pháp giảm nhẹ tiêu biểu (bấm để thêm nhanh vào ô mô tả):</span>
                    <div class="flex flex-wrap gap-1.5">
                      <button type="button" @click="insertMitigationMeasure('- Lắp đặt hệ thống điện mặt trời mái nhà để tự dùng.')" class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all">+ Điện mặt trời mái nhà</button>
                      <button type="button" @click="insertMitigationMeasure('- Cải tiến, nâng cấp lò hơi, lò nung và thiết bị sản xuất.')" class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all">+ Nâng cấp lò hơi, lò nung</button>
                      <button type="button" @click="insertMitigationMeasure('- Thay máy thổi khí cho công trình xử lý nước thải.')" class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all">+ Thay máy thổi khí XLNT</button>
                      <button type="button" @click="insertMitigationMeasure('- Điện khí hóa phương tiện, thay thế xe nâng điện trong cơ sở.')" class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all">+ Thay xe nâng điện</button>
                      <button type="button" @click="insertMitigationMeasure('- Tối ưu hóa quy trình công nghệ và giảm tiêu hao nhiên liệu.')" class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all">+ Tối ưu hóa quy trình công nghệ</button>
                      <button type="button" @click="insertMitigationMeasure('- Giảm rò rỉ nhiên liệu, khí gas và môi chất lạnh.')" class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all">+ Chống rò rỉ môi chất lạnh</button>
                      <button type="button" @click="insertMitigationMeasure('- Tăng cường thu hồi, tái sử dụng nhiệt thải.')" class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all">+ Thu hồi nhiệt thải</button>
                    </div>
                  </div>

                </div>"""

if step6_numbers_end in content:
    content = content.replace(step6_numbers_end, step6_efficiency_and_suggestions, 1)
    print("Added Efficiency % and Quick-Insert Suggestions in Step 6!")
else:
    print("WARNING: step6_numbers_end not found")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("HTML Template updates completed!")
