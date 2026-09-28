<div
      x-show="showSubmitModal"
      x-cloak
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="submit-modal-title"
      role="dialog"
      aria-modal="true"
      @keydown.escape.window="if (!isSubmitting) showSubmitModal = false"
    >
      <div
        class="fixed inset-0 bg-[#091710]/50 backdrop-blur-sm transition-opacity"
      ></div>

      <div
        class="flex min-h-full items-end sm:items-center justify-center p-4 text-center sm:p-0"
      >
        <div
          class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-7 space-y-5 border border-borderui"
        >
          <div class="flex items-start space-x-4">
            <div
              class="w-12 h-12 rounded-2xl bg-[#003c33] text-[#9fe870] flex items-center justify-center flex-shrink-0 shadow-sm"
            >
              <i data-lucide="send" class="w-5 h-5 text-[#9fe870]"></i>
            </div>
            <div>
              <h3
                class="text-base font-bold text-txprimary"
                id="submit-modal-title"
              >
                Xác nhận gửi dữ liệu kiểm kê
              </h3>
              <p class="text-sm text-txsecondary mt-1 leading-relaxed">
                Nội dung sau khi gửi sẽ được ghi nhận trực tiếp vào cơ sở dữ
                liệu của cơ quan quản lý. Vui lòng kiểm tra kỹ lưỡng các số liệu
                trước khi xác nhận.
              </p>
            </div>
          </div>

          <div
            class="bg-[#F8FAF9] rounded-2xl p-4 border border-borderui text-xs space-y-2 text-txsecondary"
          >
            <div class="flex justify-between">
              <span class="font-medium">Doanh nghiệp:</span>
              <span
                class="font-bold text-txprimary truncate max-w-[240px]"
                x-text="formData.company.name"
              ></span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">Kỳ kiểm kê:</span>
              <span
                class="font-mono font-bold text-[#003c33]"
                x-text="formData.reporting_years.join(' & ')"
              ></span>
            </div>
          </div>

          <div
            x-show="submissionError"
            x-cloak
            role="alert"
            class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
          >
            <div class="flex items-start gap-2">
              <i data-lucide="circle-alert" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true"></i>
              <span x-text="submissionError"></span>
            </div>
          </div>

          <div
            class="flex items-center justify-end space-x-3 pt-4 border-t border-borderui"
          >
            <button
              x-ref="submitCancelButton"
              type="button"
              @click="showSubmitModal = false"
              :disabled="isSubmitting"
              :class="isSubmitting ? 'cursor-not-allowed opacity-50' : ''"
              class="px-4 py-2.5 rounded-xl border border-borderui text-sm font-medium text-txsecondary hover:text-txprimary hover:bg-[#F0F4F2] transition-colors"
            >
              Kiểm tra lại
            </button>
            <button
              type="button"
              @click="executeSubmit()"
              :disabled="isSubmitting"
              :aria-busy="isSubmitting"
              :class="isSubmitting ? 'cursor-wait opacity-80' : ''"
              class="px-6 py-2.5 rounded-xl bg-[#003c33] hover:bg-[#064e43] text-white text-sm font-semibold transition-all shadow-card flex items-center space-x-2"
            >
              <i
                :data-lucide="isSubmitting ? 'loader-circle' : 'check'"
                :class="isSubmitting ? 'animate-spin' : ''"
                class="w-4 h-4 text-[#9fe870]"
                aria-hidden="true"
              ></i>
              <span x-text="isSubmitting ? 'Đang gửi dữ liệu...' : 'Xác nhận gửi'"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
