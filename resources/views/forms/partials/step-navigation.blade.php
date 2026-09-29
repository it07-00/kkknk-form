<div
              class="sticky bottom-4 z-30 bg-surface/95 backdrop-blur-md rounded-2xl border border-borderui p-4 shadow-elevated flex items-center justify-between gap-3"
            >
              <!-- Left: Back Button -->
              <div>
                <button
                  type="button"
                  @click="prevStep()"
                  x-show="currentStep > 1"
                  class="inline-flex items-center px-5 py-2.5 rounded-xl border border-borderui text-sm font-medium text-txsecondary hover:text-txprimary hover:bg-[#F0F4F2] transition-colors"
                >
                  <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
                  <span>Quay lại</span>
                </button>
              </div>

              <!-- Right: Save Draft & Next/Submit Buttons -->
              <div class="flex items-center space-x-2.5">
                <!-- Quick save draft button -->
                <button
                  type="button"
                  @click="saveDraft(true)"
                  class="hidden sm:inline-flex items-center px-4 py-2.5 rounded-xl border border-borderui text-sm font-medium text-txsecondary hover:text-txprimary hover:bg-[#F0F4F2] transition-colors"
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
                  class="inline-flex items-center px-7 py-3 rounded-xl bg-[#003c33] hover:bg-[#064e43] text-white text-sm font-semibold shadow-card transition-all"
                >
                  <span>Tiếp tục</span>
                  <svg class="w-4 h-4 ml-1.5 text-[#9fe870]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </button>

                <!-- Submit Button (Step 7) -->
                <button
                  x-show="currentStep === 7"
                  type="button"
                  @click="openSubmitConfirmationModal()"
                  class="inline-flex items-center px-8 py-3 rounded-xl bg-[#9fe870] hover:bg-[#8ee05b] text-[#003c33] text-sm font-bold shadow-glow transition-all"
                >
                  <svg class="w-4 h-4 mr-2 text-[#003c33]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                  </svg>
                  <span>Gửi biểu mẫu</span>
                </button>
              </div>
            </div>
