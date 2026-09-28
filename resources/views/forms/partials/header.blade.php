<header
      class="sticky top-0 z-40 bg-surface/95 backdrop-blur-md border-b border-borderui/80 shadow-subtle transition-all"
    >
      <!-- Top banner bar (EcoCheck Dark Night) -->
      <div
        class="bg-[#091710] text-white text-[11px] sm:text-xs py-1.5 px-4 sm:px-6 border-b border-white/10"
      >
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
          <!-- Left: Official System Name & Decree -->
          <div class="flex items-center space-x-2.5 min-w-0">
            <span class="relative flex h-2 w-2 flex-shrink-0">
              <span
                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#9fe870] opacity-75"
              ></span>
              <span
                class="relative inline-flex rounded-full h-2 w-2 bg-[#9fe870]"
              ></span>
            </span>
            <span
              class="font-semibold tracking-wide text-white/95 truncate text-xs"
            >
              <span class="sm:hidden">CỔNG DỮ LIỆU KKKNK CƠ SỞ</span>
              <span class="hidden sm:inline"
                >CỔNG TIẾP NHẬN DỮ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH CƠ SỞ</span
              >
            </span>
            <span
              class="hidden lg:inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-[#9fe870]/15 text-[#9fe870] border border-[#9fe870]/30 tracking-wide uppercase flex-shrink-0"
            >
              <i data-lucide="scale" class="w-3 h-3 text-[#9fe870]"></i>
              <span>Nghị định 06/2022/NĐ-CP</span>
            </span>
          </div>

          <!-- Right: Technical Support & Autosave Status -->
          <div class="flex items-center space-x-3.5 flex-shrink-0">
            <div class="hidden md:flex items-center space-x-3 text-white/75 text-xs">
              <a
                href="mailto:hotro@soct.gov.vn"
                class="inline-flex items-center space-x-1 hover:text-[#9fe870] transition-colors"
                title="Gửi email hỗ trợ"
              >
                <i data-lucide="mail" class="w-3 h-3 text-[#9fe870]"></i>
                <span>hotro@soct.gov.vn</span>
              </a>
              <span class="text-white/30">•</span>
              <a
                href="tel:02838296322"
                class="inline-flex items-center space-x-1 hover:text-[#9fe870] transition-colors"
                title="Gọi hotline hỗ trợ"
              >
                <i data-lucide="phone" class="w-3 h-3 text-[#9fe870]"></i>
                <span>(028) 38.296.322</span>
              </a>
            </div>

            <!-- Autosave status indicator in top bar -->
            <div
              class="flex items-center space-x-1.5 text-xs bg-white/[0.08] hover:bg-white/[0.12] transition-colors px-3 py-1 rounded-full border border-white/15 whitespace-nowrap flex-shrink-0 shadow-2xs"
            >
              <template x-if="saveState === 'saving'">
                <div class="flex items-center space-x-1.5 text-sky-200">
                  <i data-lucide="refresh-cw" class="w-3 h-3 animate-spin"></i>
                  <span class="text-xs">Đang lưu...</span>
                </div>
              </template>
              <template x-if="saveState === 'saved'">
                <div class="flex items-center space-x-1.5 text-[#9fe870]">
                  <i
                    data-lucide="check-circle-2"
                    class="w-3.5 h-3.5 text-[#9fe870] flex-shrink-0"
                  ></i>
                  <span class="font-mono text-xs font-semibold">
                    <span class="hidden sm:inline">Đã lưu: </span
                    ><span x-text="lastSavedTime"></span>
                  </span>
                </div>
              </template>
              <template x-if="saveState === 'idle'">
                <div class="flex items-center space-x-1.5 text-white/70">
                  <i data-lucide="cloud" class="w-3 h-3 flex-shrink-0"></i>
                  <span class="text-xs">Tự động lưu nháp</span>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Header Content -->
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between gap-4"
      >
        <!-- Brand & Title Group -->
        <div class="flex items-center space-x-3 sm:space-x-3.5 min-w-0">
          <!-- Logo Emblem -->
          <div class="relative flex-shrink-0">
            <div
              class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-[#003c33] via-[#01473d] to-[#091710] border border-[#9fe870]/35 flex items-center justify-center text-[#9fe870] shadow-md shadow-[#003c33]/20 ring-1 ring-[#003c33]/15 transition-transform duration-200 hover:scale-105"
            >
              <i data-lucide="shield-check" class="w-5 h-5 sm:w-5.5 sm:h-5.5 text-[#9fe870]"></i>
            </div>
            <div
              class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-[#9fe870] border-2 border-white flex items-center justify-center shadow-xs"
            >
              <i data-lucide="leaf" class="w-2 h-2 text-[#003c33]"></i>
            </div>
          </div>

          <!-- Title & Department Info -->
          <div class="min-w-0">
            <!-- Super title + Status Badge inline -->
            <div class="flex items-center flex-wrap gap-2 text-xs mb-0.5">
              <span
                class="font-extrabold uppercase tracking-wider text-[#003c33] text-[11px] sm:text-xs"
              >
                <span class="sm:hidden">SỞ CÔNG THƯƠNG TP.HCM</span>
                <span class="hidden sm:inline"
                  >SỞ CÔNG THƯƠNG TP. HỒ CHÍ MINH</span
                >
              </span>
              <span class="text-[#DCE5E0] hidden sm:inline">•</span>

              <!-- Status Badge (Integrated right into agency row) -->
              <span
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold transition-all shadow-2xs"
                :class="isSubmitted ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-[#EBF5F0] text-[#003c33] border border-[#003c33]/15'"
              >
                <span class="relative flex h-2 w-2">
                  <span
                    x-show="!isSubmitted"
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
                  ></span>
                  <span
                    class="relative inline-flex rounded-full h-2 w-2"
                    :class="isSubmitted ? 'bg-emerald-600' : 'bg-emerald-500'"
                  ></span>
                </span>
                <span
                  x-text="isSubmitted ? 'Đã gửi chính thức' : 'Đang kê khai'"
                ></span>
              </span>
            </div>

            <!-- Document Title -->
            <h1
              class="text-sm sm:text-[15px] lg:text-base font-extrabold text-[#0e271f] tracking-tight leading-snug"
            >
              BẢNG CUNG CẤP SỐ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH VÀ KẾ HOẠCH GIẢM NHẸ
            </h1>
          </div>
        </div>

        <!-- Header Right: Progress & Actions -->
        <div class="flex items-center space-x-2.5 sm:space-x-3 flex-shrink-0">
          <!-- Progress Pill with Inline Percentage & Mini Progress Bar (Desktop & Tablet) -->
          <div
            class="hidden sm:inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-[#F0F6F3] border border-[#003c33]/15 text-xs shadow-2xs"
          >
            <span class="text-txsecondary font-medium hidden md:inline text-[11px]"
              >Tiến độ:</span
            >
            <span
              class="font-extrabold text-[#003c33] font-mono text-xs"
              x-text="completionPercentage + '%'"
            ></span>
            <div
              class="w-12 sm:w-16 h-2 bg-[#DCE5E0] rounded-full overflow-hidden p-0.5"
            >
              <div
                class="h-full bg-gradient-to-r from-[#003c33] to-[#16a34a] rounded-full transition-all duration-300 ease-out"
                :style="'width: ' + completionPercentage + '%'"
              ></div>
            </div>
          </div>

          <!-- Manual Save Draft Button -->
          <button
            @click="saveDraft(true)"
            x-show="!isSubmitted"
            type="button"
            title="Lưu bản ghi vào bộ nhớ tạm trình duyệt"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold rounded-full text-[#003c33] bg-white border border-[#003c33]/25 hover:border-[#003c33] hover:bg-[#F0F6F3] active:scale-95 transition-all duration-200 shadow-subtle cursor-pointer whitespace-nowrap group"
          >
            <i
              data-lucide="save"
              class="w-3.5 h-3.5 text-[#003c33] transition-transform duration-200 group-hover:scale-110"
            ></i>
            <span class="hidden sm:inline">Lưu nháp</span>
          </button>
        </div>
      </div>

      <!-- Mobile Progress Tracker Bar (compact single row on mobile) -->
      <div
        class="md:hidden border-t border-borderui/60 bg-white/95 px-4 py-1.5 flex items-center justify-between text-xs"
      >
        <div class="flex items-center space-x-2 font-medium text-txprimary">
          <span
            class="px-2 py-0.5 rounded-md bg-[#003c33] text-[#9fe870] font-mono font-bold text-xs"
            x-text="'Bước ' + currentStep + '/7'"
          ></span>
          <span
            class="truncate max-w-[200px]"
            x-text="steps[currentStep-1].title"
          ></span>
        </div>
        <span
          class="font-mono font-bold text-[#003c33]"
          x-text="stepProgressPercent + '%'"
        ></span>
      </div>

      <!-- Global Slim Progress Line right under header -->
      <div class="w-full bg-[#E2EAE5] h-1 relative overflow-hidden">
        <div
          class="bg-gradient-to-r from-[#003c33] via-[#0b6b55] to-[#9fe870] h-full rounded-r-full transition-all duration-500 ease-out shadow-[0_0_8px_rgba(159,232,112,0.5)]"
          :style="'width: ' + stepProgressPercent + '%'"
        ></div>
      </div>
    </header>
