<div x-show="currentStep === 2" x-cloak class="space-y-6">
              <div
                class="bg-surface rounded-3xl border border-borderui p-7 sm:p-9 shadow-card space-y-7"
              >
                <!-- Section Header -->
                <div class="border-b border-borderui pb-5">
                  <div
                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-[#003c33]/10 text-[#003c33] font-bold text-xs uppercase tracking-wider mb-2"
                  >
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                    <span>Bước 02/07</span>
                  </div>
                  <h3 class="text-xl font-bold text-txprimary tracking-tight">
                    Kỳ kiểm kê cần báo cáo số liệu
                  </h3>
                  <p class="text-sm text-txsecondary mt-1">
                    Lựa chọn năm dương lịch mà cơ sở cần cung cấp số liệu hoạt
                    động phát thải khí nhà kính.
                  </p>
                </div>

                <!-- Selectable Cards (2024, 2025, or Both) -->
                <div>
                  <label class="block text-sm font-medium text-txprimary mb-3">
                    Chọn kỳ kiểm kê báo cáo <span class="text-danger">*</span>
                  </label>

                  <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
                  >
                    <!-- Option: 2024 -->
                    <div
                      @click="selectReportingOption('2024')"
                      role="radio"
                      tabindex="0"
                      :aria-checked="reportingOption === '2024'"
                      @keydown.space.prevent="selectReportingOption('2024')"
                      class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between"
                      :class="reportingOption === '2024' ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9]'"
                    >
                      <div class="flex items-start justify-between">
                        <div
                          class="w-11 h-11 rounded-2xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center font-bold"
                        >
                          <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                        <div
                          class="w-6 h-6 rounded-full border flex items-center justify-center transition-all"
                          :class="reportingOption === '2024' ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                        >
                          <i
                            x-show="reportingOption === '2024'"
                            data-lucide="check"
                            class="w-4 h-4"
                          ></i>
                        </div>
                      </div>
                      <div class="mt-5">
                        <div class="text-base font-bold text-txprimary">
                          Năm 2024
                        </div>
                        <p
                          class="text-xs text-txsecondary mt-1 leading-relaxed"
                        >
                          Chỉ khai báo số liệu hoạt động và phát thải cho năm
                          2024.
                        </p>
                      </div>
                    </div>

                    <!-- Option: 2025 -->
                    <div
                      @click="selectReportingOption('2025')"
                      role="radio"
                      tabindex="0"
                      :aria-checked="reportingOption === '2025'"
                      @keydown.space.prevent="selectReportingOption('2025')"
                      class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between"
                      :class="reportingOption === '2025' ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9]'"
                    >
                      <div class="flex items-start justify-between">
                        <div
                          class="w-11 h-11 rounded-2xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center font-bold"
                        >
                          <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                        <div
                          class="w-6 h-6 rounded-full border flex items-center justify-center transition-all"
                          :class="reportingOption === '2025' ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                        >
                          <i
                            x-show="reportingOption === '2025'"
                            data-lucide="check"
                            class="w-4 h-4"
                          ></i>
                        </div>
                      </div>
                      <div class="mt-5">
                        <div class="text-base font-bold text-txprimary">
                          Năm 2025
                        </div>
                        <p
                          class="text-xs text-txsecondary mt-1 leading-relaxed"
                        >
                          Chỉ khai báo số liệu hoạt động và phát thải cho năm
                          2025.
                        </p>
                      </div>
                    </div>

                    <!-- Option: Both 2024 & 2025 -->
                    <div
                      @click="selectReportingOption('both')"
                      role="radio"
                      tabindex="0"
                      :aria-checked="reportingOption === 'both'"
                      @keydown.space.prevent="selectReportingOption('both')"
                      class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between"
                      :class="reportingOption === 'both' ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9]'"
                    >
                      <div class="flex items-center justify-between">
                        <div
                          class="w-11 h-11 rounded-2xl bg-[#9fe870]/30 text-[#003c33] flex items-center justify-center font-bold"
                        >
                          <i data-lucide="calendar-range" class="w-5 h-5"></i>
                        </div>
                        <div class="flex items-center space-x-2">
                          <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-[#9fe870] text-[#003c33] shadow-sm"
                            >Khuyên dùng</span
                          >
                          <div
                            class="w-6 h-6 rounded-full border flex items-center justify-center transition-all"
                            :class="reportingOption === 'both' ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                          >
                            <i
                              x-show="reportingOption === 'both'"
                              data-lucide="check"
                              class="w-4 h-4"
                            ></i>
                          </div>
                        </div>
                      </div>
                      <div class="mt-5">
                        <div class="text-base font-bold text-txprimary">
                          Năm 2024 & 2025
                        </div>
                        <p
                          class="text-xs text-txsecondary mt-1 leading-relaxed"
                        >
                          Khai báo cho cả hai năm. Hệ thống phân tách tab nhập
                          liệu riêng.
                        </p>
                      </div>
                    </div>

                    <!-- Option: Chu kỳ 2024–2026 (Theo mẫu Excel) -->
                    <div
                      @click="selectReportingOption('2024_2026')"
                      role="radio"
                      tabindex="0"
                      :aria-checked="reportingOption === '2024_2026'"
                      @keydown.space.prevent="selectReportingOption('2024_2026')"
                      class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between"
                      :class="reportingOption === '2024_2026' ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9]'"
                    >
                      <div class="flex items-center justify-between">
                        <div
                          class="w-11 h-11 rounded-2xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center font-bold"
                        >
                          <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        <div class="flex items-center space-x-2">
                          <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#003c33] text-[#9fe870] shadow-sm"
                            >Mẫu Excel</span
                          >
                          <div
                            class="w-6 h-6 rounded-full border flex items-center justify-center transition-all"
                            :class="reportingOption === '2024_2026' ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                          >
                            <i
                              x-show="reportingOption === '2024_2026'"
                              data-lucide="check"
                              class="w-4 h-4"
                            ></i>
                          </div>
                        </div>
                      </div>
                      <div class="mt-5">
                        <div class="text-base font-bold text-txprimary">
                          Năm 2024 – 2026
                        </div>
                        <p
                          class="text-xs text-txsecondary mt-1 leading-relaxed"
                        >
                          Khai báo chu kỳ 3 năm gồm 2024, 2025 và 2026 (theo mẫu
                          biểu Excel).
                        </p>
                      </div>
                    </div>
                  </div>

                  <template x-if="hasError('reporting_years')">
                    <p
                      class="text-xs text-danger mt-2 flex items-center space-x-1"
                    >
                      <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                      <span x-text="getErrorMessage('reporting_years')"></span>
                    </p>
                  </template>
                </div>

                <!-- Information box explaining dynamic tabs behavior -->
                <div
                  class="bg-[#F0F6F3] border border-[#003c33]/15 rounded-2xl p-5 flex items-start space-x-3.5 text-sm text-[#1e3b32]"
                >
                  <div
                    class="w-7 h-7 rounded-xl bg-[#003c33] text-[#9fe870] flex items-center justify-center flex-shrink-0 mt-0.5"
                  >
                    <i data-lucide="lightbulb" class="w-4 h-4"></i>
                  </div>
                  <div class="leading-relaxed">
                    <strong class="font-bold text-[#003c33]"
                      >Cơ chế phân tách Tab thông minh:</strong
                    >
                    Khi chọn <strong>"2024 & 2025"</strong>, ở các bước tiếp
                    theo (Phạm vi 1, Phạm vi 2, Kết quả kiểm kê), hệ thống sẽ
                    hiển thị bộ chuyển Tab tiện lợi
                    <span
                      class="font-mono bg-[#9fe870]/30 text-[#003c33] font-bold px-2 py-0.5 rounded-lg"
                      >[Năm 2024]</span
                    >
                    và
                    <span
                      class="font-mono bg-[#9fe870]/30 text-[#003c33] font-bold px-2 py-0.5 rounded-lg"
                      >[Năm 2025]</span
                    >
                    giúp Quý Doanh nghiệp quản lý số liệu minh bạch, tách bạch,
                    tránh nhầm lẫn.
                  </div>
                </div>
              </div>
            </div>

            <!-- ================================================================= -->
            <!-- STEP 3: PHẠM VI 1 — PHÁT THẢI TRỰC TIẾP                           -->
            <!-- ================================================================= -->
