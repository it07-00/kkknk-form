<div x-show="currentStep === 4" x-cloak class="space-y-6">
              <div
                class="bg-surface rounded-3xl border border-borderui p-7 sm:p-9 shadow-card space-y-7"
              >
                <!-- Section Header -->
                <div class="border-b border-borderui pb-5">
                  <div
                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-[#003c33]/10 text-[#003c33] font-bold text-xs uppercase tracking-wider mb-2"
                  >
                    <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                    <span>Bước 04/07</span>
                  </div>
                  <h3 class="text-xl font-bold text-txprimary tracking-tight">
                    Phạm vi 2 — Phát thải gián tiếp từ năng lượng
                  </h3>
                  <p class="text-sm text-txsecondary mt-1">
                    Khai báo lượng điện năng tiêu thụ từ lưới điện quốc gia và
                    các nguồn năng lượng tái tạo (điện mặt trời mái nhà...).
                  </p>
                </div>

                <!-- Information Box Scope 2 -->
                <div
                  class="bg-[#F0F6F3] border border-[#003c33]/15 rounded-2xl p-5 flex items-start space-x-3.5 text-sm text-[#1e3b32]"
                >
                  <div
                    class="w-7 h-7 rounded-xl bg-[#003c33] text-[#9fe870] flex items-center justify-center flex-shrink-0 mt-0.5"
                  >
                    <i data-lucide="info" class="w-4 h-4"></i>
                  </div>
                  <div class="leading-relaxed">
                    <strong class="font-bold text-[#003c33]"
                      >Lưu ý chuyên môn:</strong
                    >
                    Phát thải Phạm vi 2 phát sinh từ lượng điện năng tiêu thụ
                    mua từ lưới điện quốc gia của EVN. Lượng điện này được nhân
                    với Hệ số phát thải lưới điện Việt Nam (EF) do Bộ Tài nguyên
                    và Môi trường / Bộ Công Thương công bố hằng năm để quy đổi
                    ra lượng tấn CO₂ tương đương (tCO₂e).
                  </div>
                </div>

                <!-- Loop for reporting years -->
                <div class="space-y-6">
                  <template x-for="yr in formData.reporting_years" :key="yr">
                    <div
                      class="bg-[#F8FAF9] rounded-2xl border border-borderui p-6 sm:p-7 space-y-6 shadow-sm"
                    >
                      <div
                        class="flex items-center justify-between pb-3.5 border-b border-borderui"
                      >
                        <div class="flex items-center space-x-3">
                          <span
                            class="w-8 h-8 rounded-xl bg-[#003c33] text-[#9fe870] text-xs font-mono font-extrabold flex items-center justify-center shadow-sm"
                            x-text="yr"
                          ></span>
                          <h4 class="text-base font-bold text-txprimary">
                            Điện năng tiêu thụ trong năm
                            <span x-text="yr"></span>
                          </h4>
                        </div>
                        <span
                          class="text-xs font-medium text-txsecondary bg-white px-2.5 py-1 rounded-lg border border-borderui"
                          >Theo hóa đơn 12 tháng</span
                        >
                      </div>

                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Lượng điện lưới tiêu thụ -->
                        <div>
                          <label
                            :for="'grid_elec_' + yr"
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Lượng điện lưới tiêu thụ trong năm
                            <span class="text-danger">*</span>
                          </label>
                          <div class="relative">
                            <input
                              type="number"
                              :id="'grid_elec_' + yr"
                              step="any"
                              min="0"
                              x-model.number="formData.inventory[yr].grid_electricity_kwh"
                              @input="clearFieldError('inventory.' + yr + '.grid_electricity_kwh')"
                              @blur="validateField('inventory.' + yr + '.grid_electricity_kwh')"
                              placeholder="Ví dụ: 1250000"
                              class="w-full h-12 pl-4 pr-24 rounded-xl border text-sm font-mono focus-ring transition-all"
                              :class="hasError('inventory.' + yr + '.grid_electricity_kwh') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                            />
                            <span
                              class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium"
                              >kWh/năm</span
                            >
                          </div>
                          <template
                            x-if="formData.inventory[yr].grid_electricity_kwh !== null && formData.inventory[yr].grid_electricity_kwh !== '' && !isNaN(formData.inventory[yr].grid_electricity_kwh)"
                          >
                            <div
                              class="mt-1.5 flex items-center space-x-1.5 text-xs text-[#003c33] font-semibold bg-[#9fe870]/20 px-2.5 py-1 rounded-lg w-fit"
                            >
                              <i
                                data-lucide="calculator"
                                class="w-3.5 h-3.5 text-[#003c33]"
                              ></i>
                              <span
                                >Định dạng:
                                <strong
                                  class="font-mono text-[#003c33]"
                                  x-text="formatNumber(formData.inventory[yr].grid_electricity_kwh)"
                                ></strong>
                                kWh/năm</span
                              >
                            </div>
                          </template>
                          <template
                            x-if="hasError('inventory.' + yr + '.grid_electricity_kwh')"
                          >
                            <p
                              class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                            >
                              <i
                                data-lucide="alert-circle"
                                class="w-3.5 h-3.5"
                              ></i>
                              <span
                                x-text="getErrorMessage('inventory.' + yr + '.grid_electricity_kwh')"
                              ></span>
                            </p>
                          </template>
                        </div>

                        <!-- Lượng điện mặt trời sử dụng -->
                        <div>
                          <label
                            :for="'solar_elec_' + yr"
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Lượng điện mặt trời sử dụng
                            <span class="text-danger">*</span>
                          </label>
                          <div class="relative">
                            <input
                              type="number"
                              :id="'solar_elec_' + yr"
                              step="any"
                              min="0"
                              x-model.number="formData.inventory[yr].solar_electricity_kwh"
                              @input="clearFieldError('inventory.' + yr + '.solar_electricity_kwh')"
                              @blur="validateField('inventory.' + yr + '.solar_electricity_kwh')"
                              placeholder="Nhập 0 nếu không sử dụng"
                              class="w-full h-12 pl-4 pr-24 rounded-xl border text-sm font-mono focus-ring transition-all"
                              :class="hasError('inventory.' + yr + '.solar_electricity_kwh') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                            />
                            <span
                              class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium"
                              >kWh/năm</span
                            >
                          </div>
                          <template
                            x-if="formData.inventory[yr].solar_electricity_kwh !== null && formData.inventory[yr].solar_electricity_kwh !== '' && !isNaN(formData.inventory[yr].solar_electricity_kwh)"
                          >
                            <div
                              class="mt-1.5 flex items-center space-x-1.5 text-xs text-[#003c33] font-semibold bg-[#9fe870]/20 px-2.5 py-1 rounded-lg w-fit"
                            >
                              <i
                                data-lucide="calculator"
                                class="w-3.5 h-3.5 text-[#003c33]"
                              ></i>
                              <span
                                >Định dạng:
                                <strong
                                  class="font-mono text-[#003c33]"
                                  x-text="formatNumber(formData.inventory[yr].solar_electricity_kwh)"
                                ></strong>
                                kWh/năm</span
                              >
                            </div>
                          </template>
                          <p class="text-xs text-txsecondary mt-1">
                            Nếu doanh nghiệp không sử dụng điện mặt trời, nhập
                            0.
                          </p>
                          <template
                            x-if="hasError('inventory.' + yr + '.solar_electricity_kwh')"
                          >
                            <p
                              class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                            >
                              <i
                                data-lucide="alert-circle"
                                class="w-3.5 h-3.5"
                              ></i>
                              <span
                                x-text="getErrorMessage('inventory.' + yr + '.solar_electricity_kwh')"
                              ></span>
                            </p>
                          </template>
                        </div>

                        <!-- Tổng mức tiêu thụ năng lượng quy đổi (TOE/năm) - Khớp cột R file Excel -->
                        <div
                          class="sm:col-span-2 pt-4 border-t border-borderui"
                        >
                          <div class="flex items-center justify-between mb-1.5">
                            <label
                              :for="'energy_toe_' + yr"
                              class="block text-sm font-medium text-txprimary"
                            >
                              Tổng mức tiêu thụ năng lượng quy đổi trong năm
                              (TOE/năm)
                            </label>
                            <span
                              class="text-xs font-semibold px-2 py-0.5 rounded-md bg-[#003c33]/10 text-[#003c33]"
                              >Cột TOE trong file Excel</span
                            >
                          </div>
                          <div class="relative">
                            <input
                              type="number"
                              :id="'energy_toe_' + yr"
                              step="any"
                              min="0"
                              x-model.number="formData.inventory[yr].energy_toe"
                              placeholder="Ví dụ: 1900 (Nếu chưa có thể ước tính hoặc để trống)"
                              class="w-full h-12 pl-4 pr-28 rounded-xl border border-borderui bg-white text-txprimary text-sm font-mono focus-ring transition-all"
                            />
                            <span
                              class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium"
                              >TOE/năm</span
                            >
                          </div>
                          <p class="text-xs text-txsecondary mt-1.5">
                            <strong>Tấn dầu tương đương (TOE):</strong> Tổng
                            điện và nhiên liệu tiêu thụ quy đổi theo Luật Sử
                            dụng năng lượng tiết kiệm và hiệu quả. Cơ sở sử dụng
                            năng lượng trọng điểm (≥ 1.000 TOE đối với sản xuất
                            công nghiệp, ≥ 500 TOE đối với tòa nhà).
                          </p>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>

            <!-- ================================================================= -->
            <!-- STEP 5: KẾT QUẢ KIỂM KÊ                                           -->
            <!-- ================================================================= -->
