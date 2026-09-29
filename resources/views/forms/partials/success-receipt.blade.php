<section x-show="isSubmitted" x-cloak class="max-w-3xl mx-auto py-10">
        <div
          class="bg-surface rounded-3xl border border-borderui p-8 sm:p-12 text-center shadow-card relative overflow-hidden"
        >
          <div
            class="absolute -right-16 -top-16 w-56 h-56 bg-gradient-to-bl from-[#9fe870]/25 via-[#003c33]/10 to-transparent rounded-full blur-2xl pointer-events-none"
          ></div>

          <!-- Tech Lime / Pine Emblem -->
          <div
            class="w-20 h-20 rounded-3xl bg-[#003c33] text-[#9fe870] mx-auto flex items-center justify-center mb-6 shadow-glow border border-[#9fe870]/30"
          >
            <i data-lucide="check" class="w-10 h-10 text-[#9fe870]"></i>
          </div>

          <span
            class="inline-flex items-center space-x-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-[#9fe870]/20 text-[#003c33] border border-[#9fe870]/50 mb-3 tracking-wide uppercase"
          >
            <span class="w-2 h-2 rounded-full bg-[#1a7e4b] animate-ping"></span>
            <span>Hồ sơ đã được tiếp nhận chính thức</span>
          </span>

          <h2 class="text-2xl font-bold text-txprimary mb-3 tracking-tight">
            Đã gửi dữ liệu thành công!
          </h2>
          <p
            class="text-sm text-txsecondary max-w-xl mx-auto mb-8 leading-relaxed"
          >
            Cảm ơn Quý Doanh nghiệp đã chủ động và nghiêm túc phối hợp cung cấp
            số liệu kiểm kê khí nhà kính và kế hoạch giảm nhẹ phục vụ công tác
            quản lý nhà nước.
          </p>

          <!-- Receipt Box with clean tokens -->
          <div
            class="bg-[#F8FAF9] border border-borderui rounded-2xl p-6 max-w-md mx-auto mb-8 text-left text-sm space-y-3 shadow-inner"
          >
            <div
              class="flex justify-between items-center pb-2.5 border-b border-borderui"
            >
              <span class="text-txsecondary font-medium"
                >Mã hồ sơ tiếp nhận:</span
              >
              <span
                class="font-mono font-bold text-[#003c33] text-base px-2 py-0.5 rounded-lg bg-[#9fe870]/20 border border-[#9fe870]/40"
                x-text="submittedDataReceipt.code"
                >GHG-2026-000123</span
              >
            </div>
            <div class="flex justify-between items-center">
              <span class="text-txsecondary font-medium"
                >Doanh nghiệp khai báo:</span
              >
              <span
                class="font-semibold text-txprimary truncate max-w-[220px]"
                x-text="formData.company.name || '---'"
              ></span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-txsecondary font-medium">Mã số thuế:</span>
              <span
                class="font-mono font-semibold text-txprimary"
                x-text="formData.company.tax_code || '---'"
              ></span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-txsecondary font-medium"
                >Thời gian ghi nhận:</span
              >
              <span
                class="font-mono text-txprimary font-semibold"
                x-text="submittedDataReceipt.time"
              ></span>
            </div>
            <div
              class="flex justify-between items-center pt-2.5 border-t border-borderui"
            >
              <span class="text-txsecondary font-medium"
                >Kỳ kiểm kê báo cáo:</span
              >
              <span
                class="font-bold text-[#003c33]"
                x-text="formData.reporting_years.join(' & ') || '---'"
              ></span>
            </div>
          </div>

          <!-- Action buttons -->
          <div
            class="flex flex-col sm:flex-row items-center justify-center gap-3"
          >
            <button
              @click="printReceipt()"
              type="button"
              class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-borderui bg-white text-txprimary hover:bg-[#F0F4F2] font-semibold text-sm transition-all shadow-subtle hover:border-[#003c33]/30"
            >
              <i
                data-lucide="printer"
                class="w-4 h-4 mr-2 text-txsecondary"
              ></i>
              In / Tải bản xác nhận PDF
            </button>
            <a
              x-show="submittedDataReceipt.report_file_url"
              :href="submittedDataReceipt.report_file_url"
              class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-[#003c33] bg-white text-[#003c33] hover:bg-[#F0F6F3] font-semibold text-sm transition-colors shadow-subtle"
              :aria-label="'Tải file báo cáo đã gửi ' + (submittedDataReceipt.report_file_name || '')"
            >
              <i data-lucide="download" class="w-4 h-4 mr-2" aria-hidden="true"></i>
              Tải file đã gửi
            </a>
            <button
              @click="isSubmitted = false; currentStep = 7"
              type="button"
              class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#003c33] text-white hover:bg-[#064e43] font-semibold text-sm transition-all shadow-subtle"
            >
              <i data-lucide="eye" class="w-4 h-4 mr-2 text-[#9fe870]"></i>
              Xem lại thông tin đã gửi
            </button>
            <button
              @click="resetForm()"
              type="button"
              class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-txsecondary hover:text-danger hover:bg-red-50 text-sm font-medium transition-colors"
            >
              Tạo hồ sơ mới
            </button>
          </div>
        </div>
      </section>
