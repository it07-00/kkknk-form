<div
  x-show="Object.keys(errors).length > 0"
  x-cloak
  role="alert"
  aria-live="assertive"
  aria-labelledby="error-summary-title"
  id="error-summary-banner"
  tabindex="-1"
  class="bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5 shadow-subtle space-y-2.5 transition-all focus-ring"
>
  <div class="flex items-center space-x-2 text-danger font-bold text-sm">
    <i
      data-lucide="alert-octagon"
      class="w-5 h-5 flex-shrink-0 text-danger"
      aria-hidden="true"
    ></i>
    <span id="error-summary-title">
      Vui lòng kiểm tra và hoàn thành các thông tin chưa hợp lệ bên dưới:
    </span>
  </div>
  <ul class="list-disc list-inside space-y-1 text-xs text-red-700 pl-1">
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
