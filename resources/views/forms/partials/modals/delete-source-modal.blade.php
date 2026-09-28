<div
      x-show="showDeleteModal"
      x-cloak
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
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
              class="w-11 h-11 rounded-2xl bg-red-100 text-danger flex items-center justify-center flex-shrink-0"
            >
              <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
              <h3 class="text-base font-bold text-txprimary" id="modal-title">
                Xóa nguồn phát thải?
              </h3>
              <p class="text-sm text-txsecondary mt-1 leading-relaxed">
                Dữ liệu đã nhập cho nguồn phát thải này sẽ bị xóa khỏi hồ sơ
                kiểm kê của năm
                <span
                  class="font-bold font-mono text-[#003c33]"
                  x-text="pendingDeleteYear"
                ></span
                >. Bạn có chắc chắn muốn xóa?
              </p>
            </div>
          </div>

          <div
            class="flex items-center justify-end space-x-3 pt-4 border-t border-borderui"
          >
            <button
              type="button"
              @click="showDeleteModal = false"
              class="px-4 py-2.5 rounded-xl border border-borderui text-sm font-medium text-txsecondary hover:text-txprimary hover:bg-[#F0F4F2] transition-colors"
            >
              Hủy
            </button>
            <button
              type="button"
              @click="confirmDeleteSource()"
              class="px-5 py-2.5 rounded-xl bg-danger hover:bg-red-700 text-white text-sm font-semibold transition-colors shadow-sm"
            >
              Xóa nguồn
            </button>
          </div>
        </div>
      </div>
    </div>
