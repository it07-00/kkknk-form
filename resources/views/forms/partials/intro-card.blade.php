<div
            class="bg-surface rounded-3xl border border-borderui p-6 sm:p-8 shadow-card relative overflow-hidden"
          >
            <div
              class="absolute -right-12 -top-12 w-48 h-48 bg-gradient-to-bl from-[#9fe870]/20 via-[#003c33]/5 to-transparent rounded-full blur-xl pointer-events-none"
            ></div>

            <div class="flex items-start space-x-4">
              <div
                class="w-12 h-12 rounded-2xl bg-[#003c33] text-[#9fe870] flex items-center justify-center flex-shrink-0 shadow-sm mt-0.5"
              >
                <i data-lucide="info" class="w-6 h-6"></i>
              </div>
              <div class="space-y-4 flex-1">
                <div>
                  <h2 class="text-lg font-bold text-txprimary">
                    Kính gửi Quý Doanh nghiệp / Quý Cơ sở
                  </h2>
                  <p class="text-sm text-txsecondary mt-1 leading-relaxed">
                    Biểu mẫu được sử dụng để thu thập thông tin và số liệu hoạt
                    động phục vụ công tác kiểm kê khí nhà kính cấp cơ sở cho năm
                    <strong>2024</strong> và <strong>2025</strong>, đồng thời
                    ghi nhận kế hoạch giảm nhẹ phát thải khí nhà kính của doanh
                    nghiệp theo quy định hiện hành.
                  </p>
                </div>

                <!-- Quick Demo Data Action Button (Matching Excel File) -->
                <div
                  class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-2xl bg-[#003c33]/5 border border-[#003c33]/15"
                >
                  <div class="flex items-center space-x-2.5">
                    <span
                      class="w-8 h-8 rounded-xl bg-[#003c33] text-[#9fe870] flex items-center justify-center font-bold text-xs"
                    >
                      <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    </span>
                    <div>
                      <span class="text-xs font-bold text-[#003c33]"
                        >Dữ liệu mẫu từ Sở Công Thương:</span
                      >
                      <span class="text-xs text-txsecondary block"
                        >Bộ số liệu thực tế Công ty TNHH Takigawa Việt Nam (theo
                        file Excel)</span
                      >
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="fillTakigawaSampleData()"
                    class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-[#003c33] bg-[#9fe870] hover:bg-[#8ee05b] shadow-sm transition-all cursor-pointer"
                  >
                    <i
                      data-lucide="sparkles"
                      class="w-3.5 h-3.5 mr-1.5 text-[#003c33]"
                    ></i>
                    <span>Nạp số liệu mẫu tự động</span>
                  </button>
                </div>

                <!-- Dữ liệu cần chuẩn bị box -->
                <div
                  class="bg-[#F8FAF9] border border-borderui rounded-2xl p-5 space-y-3"
                >
                  <div
                    class="flex items-center space-x-2 text-xs font-bold text-[#003c33] uppercase tracking-wider"
                  >
                    <i
                      data-lucide="clipboard-check"
                      class="w-4 h-4 text-[#003c33]"
                    ></i>
                    <span>Dữ liệu cần chuẩn bị trước khi kê khai:</span>
                  </div>
                  <ul
                    class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-txsecondary"
                  >
                    <li class="flex items-start space-x-2.5">
                      <span
                        class="w-4 h-4 rounded-full bg-[#f1faeb] text-[#1a7e4b] flex items-center justify-center flex-shrink-0 mt-0.5"
                      >
                        <i data-lucide="check" class="w-3 h-3"></i>
                      </span>
                      <span
                        >Hóa đơn / nhật trình tiêu thụ nhiên liệu (dầu DO, FO,
                        LPG, than, xăng, khí tự nhiên, sinh khối...)</span
                      >
                    </li>
                    <li class="flex items-start space-x-2.5">
                      <span
                        class="w-4 h-4 rounded-full bg-[#f1faeb] text-[#1a7e4b] flex items-center justify-center flex-shrink-0 mt-0.5"
                      >
                        <i data-lucide="check" class="w-3 h-3"></i>
                      </span>
                      <span
                        >Lượng nạp bổ sung môi chất lạnh cho hệ thống điều hòa /
                        chiller (R22, R32, R410A...)</span
                      >
                    </li>
                    <li class="flex items-start space-x-2.5">
                      <span
                        class="w-4 h-4 rounded-full bg-[#f1faeb] text-[#1a7e4b] flex items-center justify-center flex-shrink-0 mt-0.5"
                      >
                        <i data-lucide="check" class="w-3 h-3"></i>
                      </span>
                      <span
                        >Hóa đơn tiền điện hoặc biên bản chốt chỉ số công tơ 12
                        tháng trong năm báo cáo.</span
                      >
                    </li>
                    <li class="flex items-start space-x-2.5">
                      <span
                        class="w-4 h-4 rounded-full bg-[#f1faeb] text-[#1a7e4b] flex items-center justify-center flex-shrink-0 mt-0.5"
                      >
                        <i data-lucide="check" class="w-3 h-3"></i>
                      </span>
                      <span
                        >Báo cáo kiểm kê khí nhà kính và kế hoạch giảm nhẹ nếu
                        đơn vị đã thực hiện.</span
                      >
                    </li>
                  </ul>
                  <div
                    class="pt-3 border-t border-borderui text-xs text-txsecondary flex items-center space-x-2"
                  >
                    <i
                      data-lucide="alert-circle"
                      class="w-4 h-4 text-warning flex-shrink-0"
                    ></i>
                    <span
                      >Vui lòng sử dụng số liệu tiêu thụ thực tế trong cả năm và
                      đảm bảo tính chính xác, thống nhất.</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- FORM WRAPPER -->
          <form
            @submit.prevent="handleNext()"
            id="ghgMainForm"
            novalidate
            class="space-y-6"
          >
            <!-- ERROR SUMMARY ALERT BOX (WCAG 3.3.1 & 3.3.3) -->
            <div
              x-show="Object.keys(errors).length > 0"
              x-cloak
              role="alert"
              aria-live="assertive"
              id="error-summary-banner"
              class="bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5 shadow-subtle space-y-2.5 transition-all"
            >
              <div
                class="flex items-center space-x-2 text-danger font-bold text-sm"
              >
                <i
                  data-lucide="alert-octagon"
                  class="w-5 h-5 flex-shrink-0 text-danger"
                ></i>
                <span
                  >Vui lòng kiểm tra và hoàn thành các thông tin chưa hợp lệ bên
                  dưới:</span
                >
              </div>
              <ul
                class="list-disc list-inside space-y-1 text-xs text-red-700 pl-1"
              >
                <template x-for="(msg, key) in errors" :key="key">
                  <li>
                    <button
                      type="button"
                      @click="focusFieldByKey(key)"
                      class="hover:underline text-sm font-medium text-left transition-colors hover:text-red-900 cursor-pointer"
                      x-text="msg"
                    ></button>
                  </li>
                </template>
              </ul>
            </div>

            <!-- ================================================================= -->
            <!-- STEP 1: THÔNG TIN DOANH NGHIỆP                                    -->
            <!-- ================================================================= -->
