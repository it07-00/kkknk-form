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
                <template x-if="currentStep < 7">
                  <button
                    type="submit"
                    class="inline-flex items-center px-7 py-3 rounded-xl bg-[#003c33] hover:bg-[#064e43] text-white text-sm font-semibold shadow-card transition-all"
                  >
                    <span>Tiếp tục</span>
                    <i
                      data-lucide="arrow-right"
                      class="w-4 h-4 ml-1.5 text-[#9fe870]"
                    ></i>
                  </button>
                </template>

                <!-- Submit Button (Step 7) -->
                <template x-if="currentStep === 7">
                  <button
                    type="button"
                    @click="openSubmitConfirmationModal()"
                    class="inline-flex items-center px-8 py-3 rounded-xl bg-[#9fe870] hover:bg-[#8ee05b] text-[#003c33] text-sm font-bold shadow-glow transition-all"
                  >
                    <i
                      data-lucide="send"
                      class="w-4 h-4 mr-2 text-[#003c33]"
                    ></i>
                    <span>Gửi biểu mẫu</span>
                  </button>
                </template>
              </div>
            </div>
