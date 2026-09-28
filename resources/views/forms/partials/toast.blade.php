<div
      x-show="toast.visible"
      x-transition:enter="transition ease-out duration-300 transform"
      x-transition:enter-start="opacity-0 translate-y-2"
      x-transition:enter-end="opacity-100 translate-y-0"
      x-transition:leave="transition ease-in duration-200 transform"
      x-transition:leave-start="opacity-100 translate-y-0"
      x-transition:leave-end="opacity-0 translate-y-2"
      class="fixed bottom-6 right-6 z-50 bg-[#091710] text-white border border-[#9fe870]/30 px-5 py-3.5 rounded-2xl shadow-elevated flex items-center space-x-2.5 text-sm"
      x-cloak
    >
      <i data-lucide="check-circle" class="w-4 h-4 text-[#9fe870]"></i>
      <span x-text="toast.message"></span>
    </div>
