<div x-show="currentStep === 3" x-cloak class="space-y-6">
              <div
                class="bg-surface rounded-3xl border border-borderui p-7 sm:p-9 shadow-card space-y-7"
              >
                <!-- Section Header -->
                <div class="border-b border-borderui pb-5">
                  <div
                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-[#003c33]/10 text-[#003c33] font-bold text-xs uppercase tracking-wider mb-2"
                  >
                    <i data-lucide="flame" class="w-3.5 h-3.5"></i>
                    <span>Bước 03/07</span>
                  </div>
                  <h3 class="text-xl font-bold text-txprimary tracking-tight">
                    Phạm vi 1 — Phát thải trực tiếp
                  </h3>
                  <p class="text-sm text-txsecondary mt-1">
                    Khai báo các nguồn phát thải trực tiếp thuộc quyền sở hữu
                    hoặc kiểm soát của doanh nghiệp (đốt nhiên liệu cố định, di
                    động, rò rỉ môi chất lạnh, quá trình công nghệ...).
                  </p>
                </div>

                <!-- YEAR SELECTION TABS (Modern Segmented Pill Control) -->
                <template x-if="formData.reporting_years.length > 1">
                  <div
                    class="bg-[#EEF3F0] p-1.5 rounded-2xl inline-flex space-x-1 border border-borderui"
                  >
                    <template x-for="yr in formData.reporting_years" :key="yr">
                      <button
                        type="button"
                        @click="activeScope1Year = yr"
                        :class="activeScope1Year === yr ? 'bg-[#003c33] text-white shadow-sm font-bold' : 'text-txsecondary hover:text-txprimary hover:bg-white/60 font-medium'"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center space-x-2"
                      >
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span x-text="'Số liệu Năm ' + yr"></span>
                        <span
                          class="w-2.5 h-2.5 rounded-full"
                          :class="formData.inventory[yr].scope1_sources.length > 0 ? 'bg-[#9fe870]' : 'bg-amber-400'"
                        ></span>
                      </button>
                    </template>
                  </div>
                </template>

                <!-- Current Active Year Container -->
                <div
                  class="space-y-6"
                  x-data="{ currentYear: activeScope1Year }"
                >
                  <!-- DYNAMIC SCOPE 1 REPEATER CARD -->
                  <div class="space-y-6 pt-2">
                    <!-- 1. KHỐI LÒ HƠI / NỒI HƠI (Khớp cột K, L, M file Excel) -->
                    <div
                      class="bg-white rounded-2xl border border-borderui p-5 sm:p-6 shadow-subtle space-y-4"
                    >
                      <div
                        class="flex items-center justify-between pb-3.5 border-b border-borderui"
                      >
                        <div class="flex items-center space-x-3">
                          <div
                            class="w-9 h-9 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold"
                          >
                            <i
                              data-lucide="flame"
                              class="w-5 h-5 text-amber-700"
                            ></i>
                          </div>
                          <div>
                            <div class="text-sm font-bold text-txprimary">
                              1. Lò hơi / Nồi hơi công nghiệp (Boiler)
                            </div>
                            <div class="text-xs text-txsecondary">
                              Thu thập công suất thiết kế, loại nhiên liệu đốt
                              và lượng đốt/năm theo biểu mẫu Sở Công Thương
                            </div>
                          </div>
                        </div>

                        <!-- Toggle Switch -->
                        <label
                          class="relative inline-flex items-center cursor-pointer"
                        >
                          <input
                            type="checkbox"
                            x-model="formData.inventory[activeScope1Year].has_boiler"
                            class="sr-only peer"
                          />
                          <div
                            class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003c33]"
                          ></div>
                          <span
                            class="ml-2.5 text-xs font-semibold"
                            :class="formData.inventory[activeScope1Year].has_boiler ? 'text-[#003c33] font-bold' : 'text-txsecondary'"
                            x-text="formData.inventory[activeScope1Year].has_boiler ? 'Có sử dụng lò hơi' : 'Không có lò hơi'"
                          ></span>
                        </label>
                      </div>

                      <!-- Boiler Form Fields -->
                      <div
                        x-show="formData.inventory[activeScope1Year].has_boiler"
                        x-transition
                        class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1"
                      >
                        <!-- Công suất lò hơi -->
                        <div>
                          <label
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Công suất thiết kế lò hơi
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="text"
                            x-model="formData.inventory[activeScope1Year].boiler.capacity"
                            :aria-label="'Công suất thiết kế lò hơi năm ' + activeScope1Year"
                            placeholder="Ví dụ: 3 tấn hơi/giờ hoặc 3.5"
                            class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          />
                          <span class="text-xs text-txsecondary mt-1 block"
                            >Khớp cột: Công suất (tấn hơi/giờ)</span
                          >
                        </div>

                        <!-- Nhiên liệu đốt lò hơi -->
                        <div>
                          <label
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Nhiên liệu đốt lò hơi
                            <span class="text-danger">*</span>
                          </label>
                          <select
                            x-model="formData.inventory[activeScope1Year].boiler.fuel"
                            :aria-label="'Nhiên liệu đốt lò hơi năm ' + activeScope1Year"
                            class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          >
                            <option value="Sinh khối">
                              Sinh khối (Biomass, trấu, mùn cưa, dăm gỗ...)
                            </option>
                            <option value="Than đá">Than đá / Than cám</option>
                            <option value="Dầu DO">Dầu DO (Diesel)</option>
                            <option value="Dầu FO">Dầu FO (Fuel Oil)</option>
                            <option value="LPG">LPG (Khí hóa lỏng)</option>
                            <option value="Khí tự nhiên">
                              Khí tự nhiên (CNG, LNG)
                            </option>
                            <option value="Củi gỗ">Củi gỗ</option>
                            <option value="Viên nén">
                              Viên nén mùn cưa (Pellets)
                            </option>
                            <option value="Khác">Nhiên liệu khác</option>
                          </select>
                          <input
                            x-show="formData.inventory[activeScope1Year].boiler.fuel === 'Khác'"
                            type="text"
                            x-model="formData.inventory[activeScope1Year].boiler.fuel_other"
                            :aria-label="'Nhiên liệu lò hơi khác năm ' + activeScope1Year"
                            placeholder="Nhập nhiên liệu lò hơi khác..."
                            class="w-full h-11 px-3.5 mt-2 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          />
                          <span class="text-xs text-txsecondary mt-1 block"
                            >Khớp cột: Nhiên liệu đốt lò hơi</span
                          >
                        </div>

                        <!-- Lượng đốt trung bình/năm -->
                        <div>
                          <label
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Lượng đốt trung bình/năm
                            <span class="text-danger">*</span>
                          </label>
                          <div class="flex space-x-2">
                            <input
                              type="number"
                              step="any"
                              min="0"
                              x-model.number="formData.inventory[activeScope1Year].boiler.consumption"
                              :aria-label="'Lượng đốt lò hơi trung bình năm ' + activeScope1Year"
                              placeholder="Ví dụ: 500"
                              class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring"
                            />
                            <select
                              x-model="formData.inventory[activeScope1Year].boiler.unit"
                              :aria-label="'Đơn vị lượng đốt lò hơi năm ' + activeScope1Year"
                              class="w-28 h-11 px-2.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                            >
                              <option value="tấn/năm">tấn/năm</option>
                              <option value="kg/năm">kg/năm</option>
                              <option value="m³/năm">m³/năm</option>
                              <option value="lít/năm">lít/năm</option>
                            </select>
                          </div>
                          <span class="text-xs text-txsecondary mt-1 block"
                            >Khớp cột: Lượng đốt trung bình/năm</span
                          >
                        </div>
                      </div>
                      <template
                        x-if="hasErrorPrefix('inventory.' + activeScope1Year + '.boiler')"
                      >
                        <p class="text-xs text-danger flex items-center space-x-1" role="alert">
                          <i data-lucide="alert-circle" class="w-3.5 h-3.5" aria-hidden="true"></i>
                          <span x-text="getFirstErrorMessage('inventory.' + activeScope1Year + '.boiler')"></span>
                        </p>
                      </template>
                    </div>

                    <!-- 2. KHỐI MÔI CHẤT LẠNH & ĐIỀU HÒA (Khớp cột N, O, P, Q file Excel) -->
                    <div
                      class="bg-white rounded-2xl border border-borderui p-5 sm:p-6 shadow-subtle space-y-4"
                    >
                      <div
                        class="flex items-center justify-between pb-3.5 border-b border-borderui"
                      >
                        <div class="flex items-center space-x-3">
                          <div
                            class="w-9 h-9 rounded-xl bg-sky-50 text-sky-800 flex items-center justify-center font-bold"
                          >
                            <i
                              data-lucide="snowflake"
                              class="w-5 h-5 text-sky-700"
                            ></i>
                          </div>
                          <div>
                            <div class="text-sm font-bold text-txprimary">
                              2. Thiết bị làm lạnh & Môi chất lạnh (HVAC /
                              Chiller / Điều hòa)
                            </div>
                            <div class="text-xs text-txsecondary">
                              Thu thập loại thiết bị, công suất làm lạnh, loại
                              gas và lượng nạp đầy theo biểu mẫu Sở Công Thương
                            </div>
                          </div>
                        </div>

                        <!-- Toggle Switch -->
                        <label
                          class="relative inline-flex items-center cursor-pointer"
                        >
                          <input
                            type="checkbox"
                            x-model="formData.inventory[activeScope1Year].has_cooling"
                            class="sr-only peer"
                          />
                          <div
                            class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003c33]"
                          ></div>
                          <span
                            class="ml-2.5 text-xs font-semibold"
                            :class="formData.inventory[activeScope1Year].has_cooling ? 'text-[#003c33] font-bold' : 'text-txsecondary'"
                            x-text="formData.inventory[activeScope1Year].has_cooling ? 'Có sử dụng hệ thống lạnh' : 'Không có hệ thống lạnh'"
                          ></span>
                        </label>
                      </div>

                      <!-- Cooling Form Fields -->
                      <div
                        x-show="formData.inventory[activeScope1Year].has_cooling"
                        x-transition
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1"
                      >
                        <!-- Thiết bị lạnh sử dụng -->
                        <div>
                          <label
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Thiết bị lạnh sử dụng
                            <span class="text-danger">*</span>
                          </label>
                          <select
                            x-model="formData.inventory[activeScope1Year].refrigeration.equipment"
                            :aria-label="'Thiết bị lạnh sử dụng năm ' + activeScope1Year"
                            class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          >
                            <option value="Máy lạnh">
                              Máy lạnh dân dụng / cục bộ
                            </option>
                            <option value="Chiller">
                              Hệ thống Chiller làm lạnh nước
                            </option>
                            <option value="VRV/VRF">
                              Điều hòa trung tâm VRV / VRF
                            </option>
                            <option value="Kho lạnh">
                              Kho lạnh công nghiệp / Tủ đông
                            </option>
                            <option value="Khác">Thiết bị lạnh khác</option>
                          </select>
                          <input
                            x-show="formData.inventory[activeScope1Year].refrigeration.equipment === 'Khác'"
                            type="text"
                            x-model="formData.inventory[activeScope1Year].refrigeration.equipment_other"
                            :aria-label="'Thiết bị lạnh khác năm ' + activeScope1Year"
                            placeholder="Nhập thiết bị lạnh khác..."
                            class="w-full h-11 px-3.5 mt-2 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          />
                          <span class="text-xs text-txsecondary mt-1 block"
                            >Khớp cột: Thiết bị lạnh sử dụng</span
                          >
                        </div>

                        <!-- Công suất lạnh -->
                        <div>
                          <label
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Công suất lạnh <span class="text-danger">*</span>
                          </label>
                          <input
                            type="text"
                            x-model="formData.inventory[activeScope1Year].refrigeration.capacity"
                            :aria-label="'Công suất lạnh năm ' + activeScope1Year"
                            placeholder="Ví dụ: 2 HP, 50 HP, 60.000 BTU/h"
                            class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          />
                          <span class="text-xs text-txsecondary mt-1 block"
                            >Khớp cột: Công suất lạnh (HP hoặc BTU/giờ)</span
                          >
                        </div>

                        <!-- Loại môi chất lạnh -->
                        <div>
                          <label
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Loại môi chất lạnh (Gas)
                            <span class="text-danger">*</span>
                          </label>
                          <select
                            x-model="formData.inventory[activeScope1Year].refrigeration.gas_type"
                            :aria-label="'Loại môi chất lạnh năm ' + activeScope1Year"
                            class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          >
                            <option value="R22">R22</option>
                            <option value="R410A">R410A</option>
                            <option value="R134a">R134a</option>
                            <option value="R32">R32</option>
                            <option value="R404A">R404A</option>
                            <option value="R407C">R407C</option>
                            <option value="R507A">R507A</option>
                            <option value="Khác">Gas lạnh khác</option>
                          </select>
                          <input
                            x-show="formData.inventory[activeScope1Year].refrigeration.gas_type === 'Khác'"
                            type="text"
                            x-model="formData.inventory[activeScope1Year].refrigeration.gas_type_other"
                            :aria-label="'Môi chất lạnh khác năm ' + activeScope1Year"
                            placeholder="Nhập loại môi chất lạnh khác..."
                            class="w-full h-11 px-3.5 mt-2 rounded-xl border border-borderui bg-white text-sm focus-ring"
                          />
                          <span class="text-xs text-txsecondary mt-1 block"
                            >Khớp cột: Loại môi chất lạnh</span
                          >
                        </div>

                        <!-- Lượng môi chất nạp đầy (kg) -->
                        <div>
                          <label
                            class="block text-sm font-medium text-txprimary mb-1.5"
                          >
                            Lượng gas khi nạp đầy (kg)
                            <span class="text-danger">*</span>
                          </label>
                          <input
                            type="number"
                            step="any"
                            min="0"
                            x-model.number="formData.inventory[activeScope1Year].refrigeration.full_charge_kg"
                            :aria-label="'Lượng gas nạp đầy năm ' + activeScope1Year"
                            placeholder="Ví dụ: 10 (kg)"
                            class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring"
                          />
                          <span class="text-xs text-txsecondary mt-1 block"
                            >Khớp cột: Lượng môi chất nạp đầy</span
                          >
                        </div>
                      </div>
                      <template
                        x-if="hasErrorPrefix('inventory.' + activeScope1Year + '.refrigeration')"
                      >
                        <p class="text-xs text-danger flex items-center space-x-1" role="alert">
                          <i data-lucide="alert-circle" class="w-3.5 h-3.5" aria-hidden="true"></i>
                          <span x-text="getFirstErrorMessage('inventory.' + activeScope1Year + '.refrigeration')"></span>
                        </p>
                      </template>
                    </div>

                    <!-- 3. CÁC NGUỒN PHÁT THẢI TRỰC TIẾP KHÁC -->
                    <div class="border-t border-borderui pt-5">
                      <h4 class="text-base font-bold text-txprimary mb-1">
                        3. Các nguồn phát thải trực tiếp khác (Xe cộ, máy phát
                        điện...)
                      </h4>
                      <p class="text-xs text-txsecondary mb-4">
                        Khai báo phương tiện vận tải công ty (xe nâng, xe tải),
                        máy phát điện dự phòng, xử lý chất thải hoặc các nguồn
                        đốt khác.
                      </p>
                    </div>

                    <div class="flex items-center justify-between">
                      <div>
                        <h4
                          class="text-base font-bold text-txprimary flex items-center space-x-2"
                        >
                          <span>Danh sách nguồn phát thải Phạm vi 1</span>
                          <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-[#9fe870]/25 text-[#003c33]"
                            x-text="formData.inventory[activeScope1Year].scope1_sources.length + ' nguồn'"
                          ></span>
                        </h4>
                        <p class="text-xs text-txsecondary">
                          Khai báo chi tiết các nguồn đốt cố định, di động, rò
                          rỉ chất làm lạnh... Không giới hạn số lượng nguồn.
                        </p>
                      </div>

                      <!-- Add Source Button Top -->
                      <button
                        type="button"
                        :id="'scope1_sources_' + activeScope1Year"
                        @click="addScope1Source(activeScope1Year)"
                        class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-semibold text-[#003c33] bg-[#9fe870]/30 hover:bg-[#9fe870]/50 transition-all shadow-sm cursor-pointer"
                      >
                        <i
                          data-lucide="plus"
                          class="w-3.5 h-3.5 mr-1 text-[#003c33]"
                        ></i>
                        <span>Thêm nguồn phát thải</span>
                      </button>
                    </div>

                    <template
                      x-if="hasErrorPrefix('inventory.' + activeScope1Year + '.scope1_sources')"
                    >
                      <p class="text-xs text-danger flex items-center space-x-1" role="alert">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5" aria-hidden="true"></i>
                        <span x-text="getFirstErrorMessage('inventory.' + activeScope1Year + '.scope1_sources')"></span>
                      </p>
                    </template>

                    <!-- Empty state if 0 sources -->
                    <div
                      x-show="formData.inventory[activeScope1Year].scope1_sources.length === 0"
                      class="text-center py-10 px-4 border-2 border-dashed border-[#003c33]/20 rounded-3xl bg-[#F8FAF9]"
                    >
                      <div
                        class="w-14 h-14 rounded-2xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center mx-auto mb-3"
                      >
                        <i data-lucide="factory" class="w-7 h-7"></i>
                      </div>
                      <p class="text-sm font-bold text-txprimary">
                        Chưa có nguồn phát thải nào được khai báo cho năm
                        <span x-text="activeScope1Year"></span>
                      </p>
                      <p class="text-xs text-txsecondary mt-1 max-w-sm mx-auto">
                        Vui lòng bấm vào nút bên dưới để thêm nguồn phát thải
                        trực tiếp đầu tiên của cơ sở.
                      </p>
                      <button
                        type="button"
                        @click="addScope1Source(activeScope1Year)"
                        class="mt-4 inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#003c33] hover:bg-[#064e43] transition-all shadow-card"
                      >
                        <i
                          data-lucide="plus"
                          class="w-4 h-4 mr-1.5 text-[#9fe870]"
                        ></i>
                        Thêm nguồn phát thải đầu tiên
                      </button>
                    </div>

                    <!-- Source Cards List -->
                    <div class="space-y-4">
                      <template
                        x-for="(source, sIdx) in formData.inventory[activeScope1Year].scope1_sources"
                        :key="source.id || sIdx"
                      >
                        <div
                          class="bg-white rounded-2xl border border-borderui p-5 sm:p-6 shadow-subtle hover:border-[#003c33]/30 transition-all space-y-4 relative"
                        >
                          <!-- Source Card Header -->
                          <div
                            class="flex items-center justify-between pb-3.5 border-b border-borderui"
                          >
                            <div class="flex items-center space-x-2.5">
                              <span
                                class="w-7 h-7 rounded-xl bg-[#003c33] text-[#9fe870] text-xs font-extrabold font-mono flex items-center justify-center shadow-sm"
                                x-text="'#' + (sIdx + 1)"
                              ></span>
                              <span
                                class="text-xs font-bold text-txprimary uppercase tracking-wide"
                              >
                                Nguồn phát thải
                                <span x-text="'#0' + (sIdx + 1)"></span>
                              </span>
                              <template x-if="source.source_type">
                                <span
                                  class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-[#F0F4F2] text-[#003c33] border border-borderui"
                                  x-text="source.source_type === 'Khác' ? (source.source_type_other || 'Nguồn khác') : source.source_type"
                                ></span>
                              </template>
                            </div>

                            <!-- Delete button -->
                            <button
                              type="button"
                              @click="openDeleteSourceModal(activeScope1Year, sIdx)"
                              class="text-xs text-txsecondary hover:text-danger hover:bg-red-50 px-2.5 py-1.5 rounded-xl transition-colors flex items-center space-x-1"
                              title="Xóa nguồn phát thải này"
                            >
                              <i data-lucide="trash-2" class="w-4 h-4"></i>
                              <span class="hidden sm:inline font-medium"
                                >Xóa nguồn</span
                              >
                            </button>
                          </div>

                          <!-- Fields Grid for Source -->
                          <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- A. Loại nguồn phát thải -->
                            <div class="md:col-span-6">
                              <label
                                class="block text-sm font-medium text-txprimary mb-1.5"
                              >
                                A. Loại nguồn phát thải
                                <span class="text-danger">*</span>
                              </label>
                              <select
                                x-model="source.source_type"
                                :aria-label="'Loại nguồn phát thải số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                              >
                                <option value="" disabled selected>
                                  -- Chọn loại nguồn phát thải --
                                </option>
                                <option value="Đốt nhiên liệu cố định">
                                  Đốt nhiên liệu cố định (Nồi hơi, lò nung, máy
                                  phát điện...)
                                </option>
                                <option value="Đốt nhiên liệu di động">
                                  Đốt nhiên liệu di động (Xe nâng, xe tải nội
                                  bộ, ô tô...)
                                </option>
                                <option value="Rò rỉ môi chất lạnh">
                                  Rò rỉ môi chất lạnh (Hệ thống điều hòa,
                                  chiller, kho lạnh...)
                                </option>
                                <option value="Quá trình công nghiệp">
                                  Quá trình công nghiệp (Sản xuất xi măng, hóa
                                  chất...)
                                </option>
                                <option value="Xử lý chất thải">
                                  Xử lý chất thải rắn tại chỗ
                                </option>
                                <option value="Nước thải">
                                  Hệ thống xử lý nước thải yếm khí
                                </option>
                                <option value="Khác">
                                  Khác (Nêu rõ bên dưới)
                                </option>
                              </select>

                              <template x-if="source.source_type === 'Khác'">
                                <input
                                  type="text"
                                  x-model="source.source_type_other"
                                  :aria-label="'Loại nguồn phát thải khác số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                  placeholder="Nhập tên nguồn phát thải khác..."
                                  class="w-full h-11 px-3.5 mt-2 rounded-xl border border-borderui bg-white text-sm focus-ring"
                                />
                              </template>
                            </div>

                            <!-- B. Nhiên liệu / Chất sử dụng -->
                            <div class="md:col-span-6">
                              <label
                                class="block text-sm font-medium text-txprimary mb-1.5"
                              >
                                B. Nhiên liệu / chất sử dụng
                                <span class="text-danger">*</span>
                              </label>
                              <select
                                x-model="source.fuel_type"
                                :aria-label="'Nhiên liệu hoặc chất sử dụng nguồn số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                              >
                                <option value="" disabled selected>
                                  -- Chọn nhiên liệu / chất sử dụng --
                                </option>
                                <option value="Dầu DO">Dầu DO (Diesel)</option>
                                <option value="Dầu FO">
                                  Dầu FO (Fuel Oil)
                                </option>
                                <option value="LPG">LPG (Khí hóa lỏng)</option>
                                <option value="Than">Than đá / Than cám</option>
                                <option value="Khí tự nhiên">
                                  Khí tự nhiên (CNG, LNG, PNG)
                                </option>
                                <option value="Xăng">Xăng RON 95 / E5</option>
                                <option value="Sinh khối">
                                  Sinh khối (Trấu, mùn cưa...)
                                </option>
                                <option value="Củi">Củi gỗ</option>
                                <option value="Viên nén">
                                  Viên nén mùn cưa (Pellets)
                                </option>
                                <option value="Môi chất lạnh R22">
                                  Môi chất lạnh R22
                                </option>
                                <option value="Môi chất lạnh R32">
                                  Môi chất lạnh R32
                                </option>
                                <option value="Môi chất lạnh R410A">
                                  Môi chất lạnh R410A
                                </option>
                                <option value="Khác">
                                  Khác (Nêu rõ bên dưới)
                                </option>
                              </select>

                              <template x-if="source.fuel_type === 'Khác'">
                                <input
                                  type="text"
                                  x-model="source.fuel_type_other"
                                  :aria-label="'Nhiên liệu hoặc chất khác nguồn số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                  placeholder="Nhập tên nhiên liệu / chất khác..."
                                  class="w-full h-11 px-3.5 mt-2 rounded-xl border border-borderui bg-white text-sm focus-ring"
                                />
                              </template>
                            </div>

                            <!-- C. Lượng sử dụng -->
                            <div class="md:col-span-6">
                              <label
                                class="block text-sm font-medium text-txprimary mb-1.5"
                              >
                                C. Lượng sử dụng trong năm
                                <span class="text-danger">*</span>
                              </label>
                              <div class="relative">
                                <input
                                  type="number"
                                  step="any"
                                  min="0"
                                  x-model.number="source.quantity"
                                  :aria-label="'Lượng sử dụng nguồn số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                  placeholder="Ví dụ: 12500 hoặc 45.8"
                                  class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm font-mono focus-ring"
                                />
                              </div>
                              <template
                                x-if="source.quantity !== null && source.quantity !== '' && !isNaN(source.quantity)"
                              >
                                <div
                                  class="mt-1.5 flex items-center space-x-1.5 text-xs text-[#003c33] font-semibold bg-[#9fe870]/20 px-2.5 py-1 rounded-lg w-fit"
                                >
                                  <i
                                    data-lucide="calculator"
                                    class="w-3.5 h-3.5 text-[#003c33]"
                                  ></i>
                                  <span
                                    >Định dạng:
                                    <strong
                                      class="font-mono text-[#003c33]"
                                      x-text="formatNumber(source.quantity)"
                                    ></strong>
                                    <span
                                      x-text="source.unit === 'Khác' ? (source.unit_other || '') : (source.unit || '')"
                                    ></span
                                  ></span>
                                </div>
                              </template>
                            </div>

                            <!-- D. Đơn vị tính -->
                            <div class="md:col-span-6">
                              <label
                                class="block text-sm font-medium text-txprimary mb-1.5"
                              >
                                D. Đơn vị tính
                                <span class="text-danger">*</span>
                              </label>
                              <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-2"
                              >
                                <select
                                  x-model="source.unit"
                                  :aria-label="'Đơn vị nguồn số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                  class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                                >
                                  <option value="" disabled selected>
                                    -- Đơn vị --
                                  </option>
                                  <option value="kg">kg</option>
                                  <option value="tấn">tấn</option>
                                  <option value="lít">lít</option>
                                  <option value="m³">m³</option>
                                  <option value="Nm³">Nm³</option>
                                  <option value="kWh">kWh</option>
                                  <option value="GJ">GJ</option>
                                  <option value="Khác">Khác</option>
                                </select>

                                <template x-if="source.unit === 'Khác'">
                                  <input
                                    type="text"
                                    x-model="source.unit_other"
                                    :aria-label="'Đơn vị khác nguồn số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                    placeholder="Đơn vị khác..."
                                    class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                                  />
                                </template>
                              </div>
                            </div>

                            <!-- E. Ghi chú nguồn phát thải -->
                            <div class="md:col-span-12">
                              <label
                                class="block text-sm font-medium text-txprimary mb-1.5"
                              >
                                E. Ghi chú nguồn phát thải
                                <span class="text-txsecondary font-normal"
                                  >(Không bắt buộc)</span
                                >
                              </label>
                              <textarea
                                rows="2"
                                x-model="source.note"
                                :aria-label="'Ghi chú nguồn số ' + (sIdx + 1) + ' năm ' + activeScope1Year"
                                placeholder="Thông tin bổ sung về nguồn phát thải, thiết bị sử dụng (nồi hơi công suất bao nhiêu), cách xác định số liệu (hóa đơn mua, đo đếm trực tiếp)..."
                                class="w-full p-3.5 rounded-xl border border-borderui bg-white text-sm focus-ring"
                              ></textarea>
                            </div>
                          </div>
                        </div>
                      </template>
                    </div>

                    <!-- Add Source Button Bottom -->
                    <div class="pt-2">
                      <button
                        type="button"
                        @click="addScope1Source(activeScope1Year)"
                        class="w-full py-3.5 rounded-2xl border-2 border-dashed border-[#003c33]/30 text-[#003c33] hover:bg-[#003c33]/5 hover:border-[#003c33] font-semibold text-sm flex items-center justify-center space-x-2 transition-all cursor-pointer"
                      >
                        <i
                          data-lucide="plus-circle"
                          class="w-4 h-4 text-[#003c33]"
                        ></i>
                        <span
                          >Thêm nguồn phát thải khác cho năm
                          <span x-text="activeScope1Year"></span> (Không giới
                          hạn số lượng)</span
                        >
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ================================================================= -->
            <!-- STEP 4: PHẠM VI 2 / ĐIỆN NĂNG                                      -->
            <!-- ================================================================= -->
