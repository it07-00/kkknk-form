<div x-show="currentStep === 5" x-cloak class="space-y-6">
              <div
                class="bg-surface rounded-3xl border border-borderui p-7 sm:p-9 shadow-card space-y-7"
              >
                <!-- Section Header -->
                <div class="border-b border-borderui pb-5">
                  <div
                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-[#003c33]/10 text-[#003c33] font-bold text-xs uppercase tracking-wider mb-2"
                  >
                    <i data-lucide="bar-chart-3" class="w-3.5 h-3.5"></i>
                    <span>Bước 05/07</span>
                  </div>
                  <h3 class="text-xl font-bold text-txprimary tracking-tight">
                    Kết quả kiểm kê khí nhà kính
                  </h3>
                  <p class="text-sm text-txsecondary mt-1">
                    Cung cấp kết quả tính toán phát thải (nếu cơ sở đã tính toán
                    hoặc có đơn vị tư vấn) và phương thức cung cấp Báo cáo kiểm
                    kê KKKNK.
                  </p>
                </div>

                <!-- Loop for reporting years -->
                <div class="space-y-6">
                  <template x-for="yr in formData.reporting_years" :key="yr">
                    <div
                      class="bg-white rounded-2xl border border-borderui p-6 sm:p-7 space-y-6 shadow-subtle hover:border-[#003c33]/30 transition-all"
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
                            Kết quả kiểm kê Năm <span x-text="yr"></span>
                          </h4>
                        </div>
                      </div>

                      <!-- Scope 1 and Scope 2 Emissions (tCO2e) -->
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Phát thải Phạm vi 1 -->
                        <div>
                          <label
                            :for="'scope1_em_' + yr"
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Phát thải Phạm vi 1 (Scope 1)
                            <span class="text-txsecondary font-normal"
                              >(Nếu có)</span
                            >
                          </label>
                          <div class="relative">
                            <input
                              type="number"
                              :id="'scope1_em_' + yr"
                              step="any"
                              min="0"
                              x-model.number="formData.inventory[yr].scope1_emissions"
                              placeholder="Ví dụ: 2315.42"
                              class="w-full h-12 pl-4 pr-32 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring transition-all"
                            />
                            <span
                              class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium"
                              >tấn CO₂e/năm</span
                            >
                          </div>
                        </div>

                        <!-- Phát thải Phạm vi 2 -->
                        <div>
                          <label
                            :for="'scope2_em_' + yr"
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Phát thải Phạm vi 2 (Scope 2)
                            <span class="text-txsecondary font-normal"
                              >(Nếu có)</span
                            >
                          </label>
                          <div class="relative">
                            <input
                              type="number"
                              :id="'scope2_em_' + yr"
                              step="any"
                              min="0"
                              x-model.number="formData.inventory[yr].scope2_emissions"
                              placeholder="Ví dụ: 1047.27"
                              class="w-full h-12 pl-4 pr-32 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring transition-all"
                            />
                            <span
                              class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium"
                              >tấn CO₂e/năm</span
                            >
                          </div>
                        </div>
                      </div>

                      <!-- Báo cáo kiểm kê khí nhà kính Block -->
                      <div class="pt-4 border-t border-borderui space-y-3.5">
                        <label class="block text-sm font-medium text-txprimary">
                          Hình thức cung cấp Báo cáo kiểm kê khí nhà kính năm
                          <span x-text="yr"></span>
                          <span class="text-danger">*</span>
                        </label>

                        <!-- Segmented Radio Buttons -->
                        <div
                          class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg"
                        >
                          <div
                            @click="formData.inventory[yr].report_method = 'Chưa có báo cáo'"
                            role="radio"
                            :aria-checked="formData.inventory[yr].report_method === 'Chưa có báo cáo'"
                            class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between text-sm font-medium"
                            :class="formData.inventory[yr].report_method === 'Chưa có báo cáo' ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] text-[#003c33] font-bold shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9] text-txprimary'"
                          >
                            <span>Chưa có báo cáo</span>
                            <div
                              class="w-5 h-5 rounded-full border flex items-center justify-center transition-all"
                              :class="formData.inventory[yr].report_method === 'Chưa có báo cáo' ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                            >
                              <i
                                x-show="formData.inventory[yr].report_method === 'Chưa có báo cáo'"
                                data-lucide="check"
                                class="w-3.5 h-3.5"
                              ></i>
                            </div>
                          </div>

                          <div
                            @click="formData.inventory[yr].report_method = 'Dán link báo cáo'"
                            role="radio"
                            :aria-checked="formData.inventory[yr].report_method === 'Dán link báo cáo'"
                            class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between text-sm font-medium"
                            :class="formData.inventory[yr].report_method === 'Dán link báo cáo' ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] text-[#003c33] font-bold shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9] text-txprimary'"
                          >
                            <span>Dán link báo cáo (Drive, OneDrive...)</span>
                            <div
                              class="w-5 h-5 rounded-full border flex items-center justify-center transition-all"
                              :class="formData.inventory[yr].report_method === 'Dán link báo cáo' ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                            >
                              <i
                                x-show="formData.inventory[yr].report_method === 'Dán link báo cáo'"
                                data-lucide="check"
                                class="w-3.5 h-3.5"
                              ></i>
                            </div>
                          </div>
                        </div>

                        <!-- Conditional URL Field -->
                        <div
                          x-show="formData.inventory[yr].report_method === 'Dán link báo cáo'"
                          class="pt-2"
                        >
                          <label
                            :for="'report_url_' + yr"
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Link Báo cáo kiểm kê khí nhà kính năm
                            <span x-text="yr"></span>
                            <span class="text-danger">*</span>
                          </label>
                          <div class="relative">
                            <input
                              type="url"
                              :id="'report_url_' + yr"
                              x-model="formData.inventory[yr].report_url"
                              @input="clearFieldError('inventory.' + yr + '.report_url')"
                              placeholder="https://drive.google.com/file/d/..."
                              class="w-full h-12 pl-11 pr-4 rounded-xl border text-sm font-mono focus-ring transition-all"
                              :class="hasError('inventory.' + yr + '.report_url') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                            />
                            <i
                              data-lucide="link"
                              class="w-4 h-4 text-[#003c33] absolute left-4 top-4"
                            ></i>
                          </div>
                          <template
                            x-if="hasError('inventory.' + yr + '.report_url')"
                          >
                            <p
                              class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                            >
                              <i
                                data-lucide="alert-circle"
                                class="w-3.5 h-3.5"
                              ></i>
                              <span
                                x-text="getErrorMessage('inventory.' + yr + '.report_url')"
                              ></span>
                            </p>
                          </template>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>

            <!-- ================================================================= -->
            <!-- STEP 6: KẾ HOẠCH GIẢM NHẸ                                         -->
            <!-- ================================================================= -->
