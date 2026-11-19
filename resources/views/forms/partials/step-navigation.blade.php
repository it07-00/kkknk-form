<div
              class="sticky bottom-2 sm:bottom-4 z-30 bg-surface/95 backdrop-blur-md rounded-2xl border border-borderui p-3 sm:p-4 shadow-elevated flex items-center justify-between gap-2 sm:gap-3"
            >
              <!-- Left: Back Button & Step Hint -->
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="prevStep()"
                  x-show="currentStep > 1"
                  class="group inline-flex items-center px-3.5 sm:px-5 py-2.5 rounded-xl border border-borderui bg-white text-xs sm:text-sm font-semibold text-txsecondary hover:text-[#003c33] hover:border-[#003c33]/30 hover:bg-[#F0F6F3] transition-all whitespace-nowrap cursor-pointer"
                >
                  <i data-lucide="arrow-left" class="w-4 h-4 mr-1 sm:mr-1.5 transition-transform group-hover:-translate-x-0.5"></i>
                  <span>Quay lại</span>
                </button>
                <span class="hidden md:inline-flex items-center text-xs font-medium text-txsecondary">
                  Bước <strong class="mx-1 font-mono text-[#003c33]" x-text="currentStep"></strong> / 7
                </span>
              </div>

              <!-- Right: Save Draft & Next/Submit Buttons -->
              <div class="flex items-center gap-2 sm:gap-2.5">
                <!-- Quick save draft button -->
                <button
                  type="button"
                  @click="saveDraft(true)"
                  class="hidden sm:inline-flex items-center px-4 py-2.5 rounded-xl border border-borderui bg-white text-sm font-medium text-txsecondary hover:text-[#003c33] hover:border-[#003c33]/30 hover:bg-[#F0F6F3] transition-all whitespace-nowrap cursor-pointer"
                >
                  <i
                    data-lucide="save"
                    class="w-4 h-4 mr-1.5 text-txsecondary"
                  ></i>
                  <span>Lưu nháp</span>
                </button>

                <!-- Next Button (Step 1 -> 6) -->
                <button
                  x-show="currentStep < 7"
                  type="submit"
                  class="group inline-flex items-center px-5 sm:px-7 py-2.5 sm:py-3 rounded-xl bg-gradient-to-r from-[#003c33] to-[#085245] hover:from-[#064e43] hover:to-[#0c6656] text-white text-xs sm:text-sm font-semibold shadow-pine active:scale-[0.98] transition-all whitespace-nowrap cursor-pointer"
                >
                  <span>Tiếp tục</span>
                  <svg class="w-4 h-4 ml-1.5 text-[#9fe870] transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </button>

                <!-- Submit Button (Step 7) -->
                <button
                  x-show="currentStep === 7"
                  type="button"
                  @click="openSubmitConfirmationModal()"
                  class="inline-flex items-center px-5 sm:px-8 py-2.5 sm:py-3 rounded-xl bg-gradient-to-r from-[#9fe870] to-[#87de54] hover:from-[#8ee05b] hover:to-[#78d444] text-[#003c33] text-xs sm:text-sm font-extrabold shadow-glow active:scale-[0.98] transition-all whitespace-nowrap cursor-pointer"
                >
                  <svg class="w-4 h-4 mr-2 text-[#003c33]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                  <span>Gửi biểu mẫu</span>
                </button>
              </div>
            </div>
