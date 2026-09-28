<div x-show="currentStep === 6" x-cloak class="space-y-6">
              <div
                class="bg-surface rounded-3xl border border-borderui p-7 sm:p-9 shadow-card space-y-7"
              >
                <!-- Section Header -->
                <div class="border-b border-borderui pb-5">
                  <div
                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-[#003c33]/10 text-[#003c33] font-bold text-xs uppercase tracking-wider mb-2"
                  >
                    <i data-lucide="leaf" class="w-3.5 h-3.5"></i>
                    <span>Bước 06/07</span>
                  </div>
                  <h3 class="text-xl font-bold text-txprimary tracking-tight">
                    Kế hoạch giảm nhẹ phát thải khí nhà kính
                  </h3>
                  <p class="text-sm text-txsecondary mt-1">
                    Ghi nhận các giải pháp tiết kiệm năng lượng, công nghệ xanh,
                    chuyển đổi năng lượng và mục tiêu giảm phát thải giai đoạn
                    2026–2030.
                  </p>
                </div>

                <!-- Question: Doanh nghiệp đã thực hiện biện pháp giảm nhẹ chưa? -->
                <div>
                  <label class="block text-sm font-medium text-txprimary mb-3">
                    Doanh nghiệp đã thực hiện biện pháp giảm nhẹ phát thải khí
                    nhà kính chưa? <span class="text-danger">*</span>
                  </label>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                    <div
                      @click="formData.mitigation.implemented = true"
                      role="radio"
                      :aria-checked="formData.mitigation.implemented === true"
                      class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between"
                      :class="formData.mitigation.implemented === true ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] text-[#003c33] font-bold shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9] text-txprimary'"
                    >
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-8 h-8 rounded-xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center"
                        >
                          <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm">Đã thực hiện</span>
                      </div>
                      <div
                        class="w-5 h-5 rounded-full border flex items-center justify-center transition-all"
                        :class="formData.mitigation.implemented === true ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                      >
                        <i
                          x-show="formData.mitigation.implemented === true"
                          data-lucide="check"
                          class="w-3.5 h-3.5"
                        ></i>
                      </div>
                    </div>

                    <div
                      @click="formData.mitigation.implemented = false"
                      role="radio"
                      :aria-checked="formData.mitigation.implemented === false"
                      class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between"
                      :class="formData.mitigation.implemented === false ? 'border-[#003c33] bg-[#003c33]/5 ring-1 ring-[#003c33] text-[#003c33] font-bold shadow-card' : 'border-borderui bg-white hover:border-[#003c33]/30 hover:bg-[#F8FAF9] text-txprimary'"
                    >
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-8 h-8 rounded-xl bg-slate-100 text-txsecondary flex items-center justify-center"
                        >
                          <i data-lucide="circle-slash" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm">Chưa thực hiện</span>
                      </div>
                      <div
                        class="w-5 h-5 rounded-full border flex items-center justify-center transition-all"
                        :class="formData.mitigation.implemented === false ? 'border-[#003c33] bg-[#003c33] text-[#9fe870]' : 'border-borderui bg-white'"
                      >
                        <i
                          x-show="formData.mitigation.implemented === false"
                          data-lucide="check"
                          class="w-3.5 h-3.5"
                        ></i>
                      </div>
                    </div>
                  </div>

                  <template x-if="hasError('mitigation.implemented')">
                    <p
                      class="text-xs text-danger mt-2 flex items-center space-x-1"
                    >
                      <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                      <span
                        x-text="getErrorMessage('mitigation.implemented')"
                      ></span>
                    </p>
                  </template>
                </div>

                <!-- Mitigation Details -->
                <div class="space-y-6 pt-4 border-t border-borderui">
                  <!-- Biện pháp giảm nhẹ giai đoạn 2026-2030 -->
                  <div>
                    <label
                      for="plan_2026_2030"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Biện pháp giảm nhẹ phát thải khí nhà kính giai đoạn
                      2026–2030
                      <span class="text-txsecondary font-normal"
                        >(Dự kiến)</span
                      >
                    </label>
                    <textarea
                      id="plan_2026_2030"
                      rows="3"
                      x-model="formData.mitigation.plan_2026_2030"
                      placeholder="Mô tả các biện pháp dự kiến triển khai trong giai đoạn 2026–2030 (ví dụ: lắp đặt điện mặt trời áp mái, thay thế biến tần, thu hồi nhiệt thải lò hơi, tối ưu hóa hệ thống máy nén khí...)"
                      class="w-full p-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring transition-all"
                    ></textarea>
                  </div>

                  <!-- Các biện pháp đã thực hiện -->
                  <div x-show="formData.mitigation.implemented === true">
                    <label
                      for="implemented_measures"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Các biện pháp giảm nhẹ đã thực hiện
                    </label>
                    <textarea
                      id="implemented_measures"
                      rows="3"
                      x-model="formData.mitigation.implemented_measures"
                      placeholder="Ví dụ: sử dụng năng lượng tái tạo, nâng cấp thiết bị tiết kiệm năng lượng, bảo dưỡng định kỳ hệ thống lạnh..."
                      class="w-full p-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring transition-all"
                    ></textarea>
                  </div>

                  <!-- Numbers: Planned and Actual Reduction (tCO2e) -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Lượng dự kiến cắt giảm -->
                    <div>
                      <label
                        for="planned_red"
                        class="block text-sm font-medium text-txprimary mb-1.5"
                      >
                        Lượng phát thải dự kiến cắt giảm theo kế hoạch
                      </label>
                      <div class="relative">
                        <input
                          type="number"
                          id="planned_red"
                          step="any"
                          min="0"
                          x-model.number="formData.mitigation.planned_reduction_tco2e"
                          placeholder="Ví dụ: 350"
                          class="w-full h-12 pl-4 pr-28 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring transition-all"
                        />
                        <span
                          class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium"
                          >tấn CO₂e</span
                        >
                      </div>
                    </div>

                    <!-- Lượng thực tế đã cắt giảm -->
                    <div>
                      <label
                        for="actual_red"
                        class="block text-sm font-medium text-txprimary mb-1.5"
                      >
                        Lượng phát thải thực tế đã cắt giảm
                      </label>
                      <div class="relative">
                        <input
                          type="number"
                          id="actual_red"
                          step="any"
                          min="0"
                          x-model.number="formData.mitigation.actual_reduction_tco2e"
                          placeholder="Ví dụ: 120"
                          class="w-full h-12 pl-4 pr-28 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring transition-all"
                        />
                        <span
                          class="absolute right-4 top-3.5 text-xs text-txsecondary font-medium"
                          >tấn CO₂e</span
                        >
                      </div>
                    </div>

                    <!-- Hiệu quả % giảm nhẹ so với kế hoạch (Tự động tính toán - Khớp cột K file Excel) -->
                    <div
                      class="sm:col-span-2 bg-[#F0F6F3] border border-[#003c33]/20 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-subtle"
                    >
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-10 h-10 rounded-xl bg-[#003c33] text-[#9fe870] flex items-center justify-center font-bold flex-shrink-0"
                        >
                          <i data-lucide="percent" class="w-5 h-5"></i>
                        </div>
                        <div>
                          <div class="text-sm font-bold text-txprimary">
                            Hiệu quả % giảm nhẹ so với kế hoạch (%)
                          </div>
                          <div class="text-xs text-txsecondary">
                            Khớp cột K trong file Excel Sở Công Thương (Công
                            thức: Lượng thực tế ÷ Lượng kế hoạch × 100%)
                          </div>
                        </div>
                      </div>
                      <div class="flex items-center space-x-3 sm:text-right">
                        <template x-if="mitigationEfficiencyPercent !== null">
                          <div class="flex items-center space-x-2">
                            <span
                              class="text-2xl font-mono font-extrabold text-[#003c33]"
                              x-text="mitigationEfficiencyPercent + '%'"
                            ></span>
                            <span
                              class="px-2.5 py-1 rounded-lg text-xs font-bold"
                              :class="mitigationEfficiencyPercent >= 100 ? 'bg-[#9fe870] text-[#003c33]' : 'bg-[#003c33]/15 text-[#003c33]'"
                              x-text="mitigationEfficiencyPercent >= 100 ? 'Đạt chỉ tiêu' : 'Đang triển khai'"
                            ></span>
                          </div>
                        </template>
                        <template x-if="mitigationEfficiencyPercent === null">
                          <span class="text-xs text-txsecondary italic"
                            >Nhập kế hoạch và thực tế để tự động tính %</span
                          >
                        </template>
                      </div>
                    </div>

                    <!-- Danh sách gợi ý giải pháp mẫu từ Sở Công Thương (Bấm để thêm nhanh) -->
                    <div class="sm:col-span-2 pt-2">
                      <span
                        class="text-xs font-semibold text-txsecondary uppercase tracking-wider block mb-2"
                        >Gợi ý các giải pháp giảm nhẹ tiêu biểu (bấm để thêm
                        nhanh vào ô mô tả):</span
                      >
                      <div class="flex flex-wrap gap-1.5">
                        <button
                          type="button"
                          @click="insertMitigationMeasure('- Lắp đặt hệ thống điện mặt trời mái nhà để tự dùng.')"
                          class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all"
                        >
                          + Điện mặt trời mái nhà
                        </button>
                        <button
                          type="button"
                          @click="insertMitigationMeasure('- Cải tiến, nâng cấp lò hơi, lò nung và thiết bị sản xuất.')"
                          class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all"
                        >
                          + Nâng cấp lò hơi, lò nung
                        </button>
                        <button
                          type="button"
                          @click="insertMitigationMeasure('- Thay máy thổi khí cho công trình xử lý nước thải.')"
                          class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all"
                        >
                          + Thay máy thổi khí XLNT
                        </button>
                        <button
                          type="button"
                          @click="insertMitigationMeasure('- Điện khí hóa phương tiện, thay thế xe nâng điện trong cơ sở.')"
                          class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all"
                        >
                          + Thay xe nâng điện
                        </button>
                        <button
                          type="button"
                          @click="insertMitigationMeasure('- Tối ưu hóa quy trình công nghệ và giảm tiêu hao nhiên liệu.')"
                          class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all"
                        >
                          + Tối ưu hóa quy trình công nghệ
                        </button>
                        <button
                          type="button"
                          @click="insertMitigationMeasure('- Giảm rò rỉ nhiên liệu, khí gas và môi chất lạnh.')"
                          class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all"
                        >
                          + Chống rò rỉ môi chất lạnh
                        </button>
                        <button
                          type="button"
                          @click="insertMitigationMeasure('- Tăng cường thu hồi, tái sử dụng nhiệt thải.')"
                          class="px-2.5 py-1 rounded-lg text-xs bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] text-txprimary font-medium transition-all"
                        >
                          + Thu hồi nhiệt thải
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Ghi chú bổ sung về giảm nhẹ -->
                  <div>
                    <label
                      for="mitigation_note"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Ghi chú bổ sung về giảm nhẹ
                      <span class="text-txsecondary font-normal"
                        >(Không bắt buộc)</span
                      >
                    </label>
                    <textarea
                      id="mitigation_note"
                      rows="2"
                      x-model="formData.mitigation.note"
                      placeholder="Ghi nhận các khó khăn, vướng mắc hoặc đề xuất cơ chế hỗ trợ kỹ thuật / tài chính xanh từ cơ quan quản lý..."
                      class="w-full p-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring transition-all"
                    ></textarea>
                  </div>

                  <!-- Link Báo cáo / Kế hoạch / Kết quả giảm nhẹ -->
                  <div>
                    <label
                      for="mitigation_report_url"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Link Báo cáo / Kế hoạch / Kết quả giảm nhẹ phát thải
                      <span class="text-txsecondary font-normal"
                        >(Tùy chọn)</span
                      >
                    </label>
                    <div class="relative">
                      <input
                        type="url"
                        id="mitigation_report_url"
                        x-model="formData.mitigation.report_url"
                        placeholder="https://..."
                        class="w-full h-12 pl-11 pr-4 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring transition-all"
                      />
                      <i
                        data-lucide="link-2"
                        class="w-4 h-4 text-[#003c33] absolute left-4 top-4"
                      ></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ================================================================= -->
            <!-- STEP 7: REVIEW / XÁC NHẬN VÀ GỬI DỮ LIỆU                         -->
            <!-- ================================================================= -->
