<header
      class="sticky top-0 z-40 bg-surface/95 backdrop-blur-md border-b border-borderui shadow-subtle"
    >
      <!-- Top banner bar (EcoCheck Dark Night) -->
      <div
        class="bg-[#091710] text-white text-[11px] sm:text-xs py-1 px-4 sm:px-6 border-b border-white/5"
      >
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
          <!-- Left: Official System Name -->
          <div class="flex items-center space-x-2 min-w-0">
            <span
              class="inline-block w-2 h-2 rounded-full bg-[#9fe870] shadow-glow animate-pulse flex-shrink-0"
            ></span>
            <span
              class="font-semibold tracking-wide text-white/95 truncate text-xs"
            >
              <span class="sm:hidden">CỔNG DỮ LIỆU KKKNK CƠ SỞ</span>
              <span class="hidden sm:inline"
                >CỔNG TIẾP NHẬN DỮ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH CƠ SỞ</span
              >
            </span>
            <span
              class="hidden lg:inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-[#9fe870]/20 text-[#9fe870] border border-[#9fe870]/30 uppercase flex-shrink-0"
              >Nghị định 06/2022/NĐ-CP</span
            >
          </div>

          <!-- Right: Technical Support & Autosave Status -->
          <div class="flex items-center space-x-3 flex-shrink-0">
            <span class="text-white/70 hidden md:inline text-xs"
              >Hỗ trợ: hotro@soct.gov.vn • (028) 38.296.322</span
            >

            <!-- Autosave status indicator in top bar (Guaranteed single line, never wraps) -->
            <div
              class="flex items-center space-x-1.5 text-xs bg-white/10 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full border border-white/15 whitespace-nowrap flex-shrink-0"
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
                    data-lucide="cloud-check"
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
        class="max-w-7xl mx-auto px-4 sm:px-6 py-2 sm:py-2.5 flex items-center justify-between gap-4"
      >
        <!-- Brand & Title Group -->
        <div class="flex items-center space-x-3 sm:space-x-3.5 min-w-0">
          <!-- Logo Emblem -->
          <div
            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#003c33] border border-[#9fe870]/30 flex items-center justify-center text-[#9fe870] shadow-pine flex-shrink-0"
          >
            <i data-lucide="shield-check" class="w-5 h-5"></i>
          </div>

          <!-- Title & Department Info -->
          <div class="min-w-0">
            <!-- Super title + Status Badge inline -->
            <div class="flex items-center flex-wrap gap-2 text-xs">
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
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold transition-colors"
                :class="isSubmitted ? 'bg-[#f1faeb] text-[#003c33] border border-[#9fe870]/60' : 'bg-[#F0F6F3] text-[#003c33] border border-[#003c33]/20'"
              >
                <span
                  class="w-1.5 h-1.5 rounded-full mr-1.5"
                  :class="isSubmitted ? 'bg-[#1a7e4b]' : 'bg-[#9fe870] ring-2 ring-[#003c33]/20 animate-pulse'"
                ></span>
                <span
                  x-text="isSubmitted ? 'Đã gửi chính thức' : 'Đang kê khai'"
                ></span>
              </span>
            </div>

            <!-- Document Title -->
            <h1
              class="text-sm sm:text-base font-bold text-txprimary tracking-tight leading-snug"
            >
              BẢNG CUNG CẤP SỐ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH VÀ KẾ HOẠCH GIẢM NHẸ
            </h1>
          </div>
        </div>

        <!-- Header Right: Progress & Actions -->
        <div class="flex items-center space-x-2.5 sm:space-x-3 flex-shrink-0">
          <!-- Progress Pill with Inline Percentage & Mini Progress Bar (Desktop & Tablet) -->
          <div
            class="hidden sm:inline-flex items-center space-x-2 px-2.5 py-1 sm:px-3 sm:py-1 rounded-full bg-[#F0F6F3] border border-[#003c33]/15 text-xs shadow-subtle"
          >
            <span class="text-txsecondary font-medium hidden md:inline"
              >Tiến độ:</span
            >
            <span
              class="font-bold text-[#003c33] font-mono"
              x-text="completionPercentage + '%'"
            ></span>
            <div
              class="w-10 sm:w-12 h-1.5 bg-[#DCE5E0] rounded-full overflow-hidden"
            >
              <div
                class="h-full bg-[#003c33] rounded-full transition-all duration-300"
                :style="'width: ' + completionPercentage + '%'"
              ></div>
            </div>
          </div>

          <!-- Manual Save Draft Button -->
          <button
            @click="saveDraft(true)"
            type="button"
            title="Lưu bản ghi vào bộ nhớ tạm trình duyệt"
            class="inline-flex items-center px-3 py-1 sm:px-3.5 sm:py-1 text-xs font-semibold rounded-full text-[#003c33] bg-white border border-borderui hover:border-[#003c33] hover:bg-[#F0F6F3] transition-all shadow-subtle cursor-pointer whitespace-nowrap"
          >
            <i
              data-lucide="save"
              class="w-3.5 h-3.5 sm:mr-1.5 text-[#003c33]"
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
      <div class="w-full bg-[#DCE5E0]/60 h-0.5 overflow-hidden">
        <div
          class="bg-[#003c33] h-full transition-all duration-300 ease-out"
          :style="'width: ' + stepProgressPercent + '%'"
        ></div>
      </div>
    </header>
