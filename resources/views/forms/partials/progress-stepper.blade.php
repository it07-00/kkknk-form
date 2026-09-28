<aside
          class="hidden lg:block lg:col-span-4 xl:col-span-3 sticky top-28 space-y-6"
        >
          <!-- STEPPER CARD (EcoCheck Style) -->
          <div
            class="bg-surface rounded-3xl border border-borderui p-5 shadow-card space-y-3"
          >
            <div
              class="flex items-center justify-between pb-3 border-b border-borderui"
            >
              <div>
                <span
                  class="text-xs uppercase tracking-wider text-txsecondary font-bold"
                  >Quy trình kê khai</span
                >
                <div class="text-sm font-extrabold text-txprimary">
                  Các bước thực hiện
                </div>
              </div>
              <span
                class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-[#003c33] text-[#9fe870] shadow-sm"
                x-text="currentStep + ' / 7'"
              ></span>
            </div>

            <!-- Stepper List -->
            <nav aria-label="Progress" class="space-y-1.5">
              <template x-for="(st, idx) in steps" :key="st.number">
                <button
                  @click="jumpToStep(st.number)"
                  type="button"
                  :class="{
                        'bg-[#003c33] text-white shadow-pine': currentStep === st.number,
                        'bg-[#F0F6F3] text-[#003c33] hover:bg-[#E4EFEA] border border-[#003c33]/15': completedSteps.includes(st.number) && currentStep !== st.number,
                        'text-txprimary hover:bg-[#F8FAF9] hover:text-[#003c33]': !completedSteps.includes(st.number) && currentStep !== st.number
                      }"
                  class="w-full text-left flex items-center space-x-3 text-sm transition-all rounded-2xl p-2.5 group relative cursor-pointer"
                >
                  <!-- Status icon circle -->
                  <div
                    class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-xs font-bold transition-all"
                    :class="{
                       'bg-[#003c33] text-[#9fe870] shadow-sm': completedSteps.includes(st.number) && currentStep !== st.number,
                       'bg-[#9fe870] text-[#003c33] shadow-glow font-mono': currentStep === st.number,
                       'bg-slate-100 text-txsecondary border border-borderui group-hover:border-[#003c33]/40 group-hover:text-[#003c33] group-hover:bg-white': !completedSteps.includes(st.number) && currentStep !== st.number
                     }"
                  >
                    <template
                      x-if="completedSteps.includes(st.number) && currentStep !== st.number"
                    >
                      <i data-lucide="check" class="w-4 h-4"></i>
                    </template>
                    <template
                      x-if="!completedSteps.includes(st.number) || currentStep === st.number"
                    >
                      <span x-text="st.number"></span>
                    </template>
                  </div>

                  <!-- Step Info -->
                  <div class="flex-1 min-w-0">
                    <div
                      class="truncate text-sm font-semibold"
                      :class="currentStep === st.number ? 'text-white font-bold' : (completedSteps.includes(st.number) ? 'text-[#003c33] font-bold' : 'text-txprimary font-semibold group-hover:text-[#003c33]')"
                      x-text="st.title"
                    ></div>
                    <div
                      class="text-xs truncate"
                      :class="currentStep === st.number ? 'text-[#9fe870]' : 'text-txsecondary'"
                      x-text="st.desc"
                    ></div>
                  </div>

                  <!-- Current Arrow Indicator -->
                  <template x-if="currentStep === st.number">
                    <i
                      data-lucide="chevron-right"
                      class="w-4 h-4 text-[#9fe870] flex-shrink-0"
                    ></i>
                  </template>
                </button>
              </template>
            </nav>
          </div>

          <!-- HELPDESK & PREPARATION MINI CARD (EcoCheck Dark Mode Banner) -->
          <div
            class="bg-gradient-to-br from-[#003c33] to-[#091710] text-white rounded-3xl p-5 shadow-card relative overflow-hidden text-xs space-y-3"
          >
            <div
              class="absolute -right-8 -top-8 w-28 h-28 bg-[#9fe870]/10 rounded-full blur-xl pointer-events-none"
            ></div>
            <div
              class="flex items-center space-x-2 text-[#9fe870] font-bold tracking-wide uppercase"
            >
              <i data-lucide="book-open" class="w-4 h-4 text-[#9fe870]"></i>
              <span>Căn cứ pháp lý áp dụng</span>
            </div>
            <p class="text-white/80 leading-relaxed text-xs">
              Áp dụng theo Luật Bảo vệ Môi trường 2020, Nghị định số
              06/2022/NĐ-CP và Quyết định 01/2022/QĐ-TTg của Thủ tướng Chính phủ
              về danh mục cơ sở phát thải khí nhà kính phải kiểm kê.
            </p>
            <div
              class="pt-2 border-t border-white/10 flex items-center justify-between text-xs text-white/70"
            >
              <span>Bảo mật dữ liệu 100%</span>
              <span class="text-[#9fe870] font-semibold flex items-center">
                <i data-lucide="lock" class="w-3 h-3 mr-1 text-[#9fe870]"></i>
                An toàn
              </span>
            </div>
          </div>
        </aside>
