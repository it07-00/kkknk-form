<div x-show="currentStep === 7" x-cloak class="space-y-6">
              <div
                class="bg-surface rounded-3xl border border-borderui p-7 sm:p-9 shadow-card space-y-7"
              >
                <!-- Section Header -->
                <div class="border-b border-borderui pb-5">
                  <div
                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-[#003c33]/10 text-[#003c33] font-bold text-xs uppercase tracking-wider mb-2"
                  >
                    <i data-lucide="file-check" class="w-3.5 h-3.5"></i>
                    <span>Bước 07/07</span>
                  </div>
                  <h3 class="text-xl font-bold text-txprimary tracking-tight">
                    Kiểm tra thông tin trước khi gửi
                  </h3>
                  <p class="text-sm text-txsecondary mt-1">
                    Vui lòng rà soát lại toàn bộ thông tin đã kê khai. Bạn có
                    thể bấm nút "Chỉnh sửa" ở từng phần để quay lại cập nhật nếu
                    có sai sót.
                  </p>
                </div>

                <!-- REVIEW SUMMARY CARDS ACCORDION/LIST -->
                <div class="space-y-4">
                  <!-- CARD 1: Thông tin doanh nghiệp -->
                  <div
                    class="border border-borderui rounded-2xl p-5 sm:p-6 bg-white space-y-4 shadow-subtle hover:border-[#003c33]/30 transition-all"
                  >
                    <div
                      class="flex items-center justify-between pb-3 border-b border-borderui"
                    >
                      <div
                        class="flex items-center space-x-2.5 font-bold text-sm text-txprimary"
                      >
                        <div
                          class="w-7 h-7 rounded-xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center"
                        >
                          <i data-lucide="building-2" class="w-4 h-4"></i>
                        </div>
                        <span>1. Thông tin doanh nghiệp</span>
                      </div>
                      <button
                        type="button"
                        @click="currentStep = 1"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold text-[#003c33] bg-[#003c33]/5 hover:bg-[#003c33]/10 flex items-center space-x-1.5 transition-colors"
                      >
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Chỉnh sửa</span>
                      </button>
                    </div>
                    <div
                      class="grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-6 text-sm"
                    >
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Tên cơ sở:</span
                        >
                        <span
                          class="font-semibold text-txprimary ml-1 block sm:inline"
                          x-text="formData.company.name || '---'"
                        ></span>
                      </div>
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Mã số thuế:</span
                        >
                        <span
                          class="font-mono font-semibold text-txprimary ml-1"
                          x-text="formData.company.tax_code || '---'"
                        ></span>
                      </div>
                      <div class="sm:col-span-2">
                        <span class="text-txsecondary font-medium"
                          >Địa chỉ:</span
                        >
                        <span
                          class="font-medium text-txprimary ml-1"
                          x-text="formData.company.address || '---'"
                        ></span>
                      </div>
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Ngành nghề:</span
                        >
                        <span
                          class="font-medium text-txprimary ml-1"
                          x-text="formData.company.industry || '---'"
                        ></span>
                      </div>
                      <div>
                        <span class="text-txsecondary font-medium">Email:</span>
                        <span
                          class="font-medium text-txprimary ml-1"
                          x-text="formData.company.email || '---'"
                        ></span>
                      </div>
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Đại diện pháp luật:</span
                        >
                        <span
                          class="font-medium text-txprimary ml-1"
                          x-text="formData.company.legal_representative.name || '---'"
                        ></span>
                      </div>
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Cán bộ số liệu:</span
                        >
                        <span
                          class="font-medium text-txprimary ml-1"
                          x-text="(formData.company.technical_contact.name || '---') + ' (' + (formData.company.technical_contact.phone || '---') + ')'"
                        ></span>
                      </div>
                    </div>
                  </div>

                  <!-- CARD 2: Năm báo cáo -->
                  <div
                    class="border border-borderui rounded-2xl p-5 sm:p-6 bg-white space-y-4 shadow-subtle hover:border-[#003c33]/30 transition-all"
                  >
                    <div
                      class="flex items-center justify-between pb-3 border-b border-borderui"
                    >
                      <div
                        class="flex items-center space-x-2.5 font-bold text-sm text-txprimary"
                      >
                        <div
                          class="w-7 h-7 rounded-xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center"
                        >
                          <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <span>2. Kỳ kiểm kê</span>
                      </div>
                      <button
                        type="button"
                        @click="currentStep = 2"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold text-[#003c33] bg-[#003c33]/5 hover:bg-[#003c33]/10 flex items-center space-x-1.5 transition-colors"
                      >
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Chỉnh sửa</span>
                      </button>
                    </div>
                    <div class="text-sm flex items-center space-x-3">
                      <span class="text-txsecondary font-medium"
                        >Kỳ báo cáo lựa chọn:</span
                      >
                      <span
                        class="font-bold text-[#003c33] font-mono px-3 py-1 rounded-xl bg-[#9fe870]/20 border border-[#9fe870]/50"
                        x-text="formData.reporting_years.join(' & ') || 'Chưa chọn'"
                      ></span>
                    </div>
                  </div>

                  <!-- CARD 3 & 4 & 5: Dữ liệu theo từng năm (Phạm vi 1, Phạm vi 2, Kết quả kiểm kê) -->
                  <template x-for="yr in formData.reporting_years" :key="yr">
                    <div
                      class="border border-borderui rounded-2xl p-5 sm:p-6 bg-[#F8FAF9] space-y-4 shadow-subtle"
                    >
                      <div
                        class="flex items-center justify-between pb-3 border-b border-borderui"
                      >
                        <div
                          class="flex items-center space-x-2.5 font-bold text-sm text-txprimary"
                        >
                          <span
                            class="w-7 h-7 rounded-xl bg-[#003c33] text-[#9fe870] text-xs font-mono font-extrabold flex items-center justify-center shadow-sm"
                            x-text="yr"
                          ></span>
                          <span x-text="'Chi tiết kiểm kê Năm ' + yr"></span>
                        </div>
                        <div
                          class="flex items-center space-x-3 text-xs font-bold text-[#003c33]"
                        >
                          <button
                            type="button"
                            @click="currentStep = 3; activeScope1Year = yr"
                            class="hover:underline"
                          >
                            Sửa PV1
                          </button>
                          <span>•</span>
                          <button
                            type="button"
                            @click="currentStep = 4"
                            class="hover:underline"
                          >
                            Sửa PV2
                          </button>
                          <span>•</span>
                          <button
                            type="button"
                            @click="currentStep = 5"
                            class="hover:underline"
                          >
                            Sửa Kết quả
                          </button>
                        </div>
                      </div>

                      <div
                        class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs"
                      >
                        <!-- Scope 1 Summary -->
                        <div
                          class="bg-white p-3.5 rounded-xl border border-borderui shadow-sm"
                        >
                          <span
                            class="text-txsecondary block text-xs font-medium"
                            >Phạm vi 1 (Phát thải trực tiếp):</span
                          >
                          <div class="font-semibold text-txprimary mt-1">
                            <span
                              class="text-[#003c33] font-mono font-bold"
                              x-text="formData.inventory[yr].scope1_sources.length + ' nguồn phát thải'"
                            ></span>
                          </div>
                          <template
                            x-if="formData.inventory[yr].scope1_emissions"
                          >
                            <div
                              class="text-xs text-txsecondary mt-1 font-mono"
                            >
                              Kết quả:
                              <strong
                                class="text-txprimary"
                                x-text="formData.inventory[yr].scope1_emissions"
                              ></strong>
                              tCO₂e
                            </div>
                          </template>
                        </div>

                        <!-- Scope 2 Summary -->
                        <div
                          class="bg-white p-3.5 rounded-xl border border-borderui shadow-sm"
                        >
                          <span
                            class="text-txsecondary block text-xs font-medium"
                            >Phạm vi 2 (Điện năng):</span
                          >
                          <div class="text-txprimary mt-1 space-y-0.5">
                            <div>
                              Lưới:
                              <strong
                                class="font-mono"
                                x-text="formatNumber(formData.inventory[yr].grid_electricity_kwh)"
                              ></strong>
                              kWh
                            </div>
                            <div>
                              Mặt trời:
                              <strong
                                class="font-mono"
                                x-text="formatNumber(formData.inventory[yr].solar_electricity_kwh)"
                              ></strong>
                              kWh
                            </div>
                          </div>
                          <template
                            x-if="formData.inventory[yr].scope2_emissions"
                          >
                            <div
                              class="text-xs text-txsecondary mt-1 font-mono"
                            >
                              Kết quả:
                              <strong
                                class="text-txprimary"
                                x-text="formData.inventory[yr].scope2_emissions"
                              ></strong>
                              tCO₂e
                            </div>
                          </template>
                        </div>

                        <!-- Report Method Summary -->
                        <div
                          class="bg-white p-3.5 rounded-xl border border-borderui shadow-sm"
                        >
                          <span
                            class="text-txsecondary block text-xs font-medium"
                            >Báo cáo KKKNK:</span
                          >
                          <div
                            class="font-semibold text-txprimary mt-1"
                            x-text="formData.inventory[yr].report_method || 'Chưa cung cấp'"
                          ></div>
                          <template x-if="formData.inventory[yr].report_url">
                            <a
                              :href="formData.inventory[yr].report_url"
                              target="_blank"
                              class="text-xs text-[#003c33] font-bold hover:underline truncate block mt-1"
                            >
                              Xem link đính kèm ↗
                            </a>
                          </template>
                        </div>
                      </div>

                      <!-- Technical Equipment & TOE Summary -->
                      <div
                        class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1"
                      >
                        <!-- Boiler -->
                        <div
                          class="bg-white p-3.5 rounded-xl border border-borderui shadow-sm"
                        >
                          <span
                            class="text-txsecondary block text-xs font-medium"
                            >Lò hơi (Boiler):</span
                          >
                          <template x-if="formData.inventory[yr].has_boiler">
                            <div class="mt-1 space-y-2 text-txprimary">
                              <template x-for="(boiler, boilerIndex) in formData.inventory[yr].boilers" :key="boiler.id">
                                <div class="border-t border-borderui pt-2 first:border-t-0 first:pt-0">
                                  <div class="font-bold text-[#003c33]" x-text="'Lò hơi #' + (boilerIndex + 1)"></div>
                                  <div>Công suất: <strong class="font-mono" x-text="boiler.capacity || '---'"></strong></div>
                                  <div>Nhiên liệu: <strong x-text="boiler.fuel === 'Khác' ? boiler.fuel_other : boiler.fuel"></strong></div>
                                  <div x-show="boiler.consumption !== null && boiler.consumption !== ''">
                                    Lượng đốt: <strong class="font-mono" x-text="formatNumber(boiler.consumption) + ' ' + boiler.unit"></strong>
                                  </div>
                                </div>
                              </template>
                            </div>
                          </template>
                          <template x-if="!formData.inventory[yr].has_boiler">
                            <div class="text-txsecondary mt-1 italic">
                              Không sử dụng lò hơi
                            </div>
                          </template>
                        </div>

                        <!-- Refrigeration -->
                        <div
                          class="bg-white p-3.5 rounded-xl border border-borderui shadow-sm"
                        >
                          <span
                            class="text-txsecondary block text-xs font-medium"
                            >Hệ thống lạnh (Chiller/HVAC):</span
                          >
                          <template x-if="formData.inventory[yr].has_cooling">
                            <div class="mt-1 space-y-2 text-txprimary">
                              <template x-for="(system, systemIndex) in formData.inventory[yr].refrigeration_systems" :key="system.id">
                                <div class="border-t border-borderui pt-2 first:border-t-0 first:pt-0">
                                  <div class="font-bold text-[#003c33]" x-text="'Hệ thống #' + (systemIndex + 1)"></div>
                                  <div>
                                    Thiết bị: <strong x-text="system.equipment === 'Khác' ? system.equipment_other : system.equipment"></strong>
                                    (<span class="font-mono" x-text="system.capacity"></span>)
                                  </div>
                                  <div>Môi chất: <strong class="font-mono" x-text="system.gas_type === 'Khác' ? system.gas_type_other : system.gas_type"></strong></div>
                                  <div x-show="system.full_charge_kg !== null && system.full_charge_kg !== ''">
                                    Nạp đầy: <strong class="font-mono" x-text="system.full_charge_kg + ' kg'"></strong>
                                  </div>
                                </div>
                              </template>
                            </div>
                          </template>
                          <template x-if="!formData.inventory[yr].has_cooling">
                            <div class="text-txsecondary mt-1 italic">
                              Không sử dụng hệ thống lạnh
                            </div>
                          </template>
                        </div>

                        <!-- TOE -->
                        <div
                          class="bg-white p-3.5 rounded-xl border border-borderui shadow-sm"
                        >
                          <span
                            class="text-txsecondary block text-xs font-medium"
                            >Quy đổi năng lượng (TOE):</span
                          >
                          <div class="mt-1">
                            <template x-if="formData.inventory[yr].energy_toe">
                              <div>
                                <strong
                                  class="text-base font-mono text-[#003c33]"
                                  x-text="formData.inventory[yr].energy_toe"
                                ></strong>
                                <span
                                  class="text-xs text-txsecondary ml-1 font-medium"
                                  >TOE/năm</span
                                >
                              </div>
                            </template>
                            <template x-if="!formData.inventory[yr].energy_toe">
                              <span class="text-txsecondary italic"
                                >Chưa khai báo TOE</span
                              >
                            </template>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>

                  <!-- CARD 6: Giảm nhẹ phát thải -->
                  <div
                    class="border border-borderui rounded-2xl p-5 sm:p-6 bg-white space-y-4 shadow-subtle hover:border-[#003c33]/30 transition-all"
                  >
                    <div
                      class="flex items-center justify-between pb-3 border-b border-borderui"
                    >
                      <div
                        class="flex items-center space-x-2.5 font-bold text-sm text-txprimary"
                      >
                        <div
                          class="w-7 h-7 rounded-xl bg-[#003c33]/10 text-[#003c33] flex items-center justify-center"
                        >
                          <i data-lucide="leaf" class="w-4 h-4"></i>
                        </div>
                        <span>6. Kế hoạch & Kết quả giảm nhẹ phát thải</span>
                      </div>
                      <button
                        type="button"
                        @click="currentStep = 6"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold text-[#003c33] bg-[#003c33]/5 hover:bg-[#003c33]/10 flex items-center space-x-1.5 transition-colors"
                      >
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Chỉnh sửa</span>
                      </button>
                    </div>
                    <div
                      class="grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-6 text-sm"
                    >
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Tình trạng thực hiện:</span
                        >
                        <span
                          class="font-bold ml-1"
                          :class="formData.mitigation.implemented ? 'text-[#003c33]' : 'text-txsecondary'"
                          x-text="formData.mitigation.implemented === true ? 'Đã thực hiện' : (formData.mitigation.implemented === false ? 'Chưa thực hiện' : 'Chưa khai báo')"
                        ></span>
                      </div>
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Cắt giảm dự kiến:</span
                        >
                        <span
                          class="font-mono font-semibold text-txprimary ml-1"
                          x-text="(formData.mitigation.planned_reduction_tco2e || '0') + ' tCO₂e'"
                        ></span>
                      </div>
                      <div>
                        <span class="text-txsecondary font-medium"
                          >Cắt giảm thực tế:</span
                        >
                        <span
                          class="font-mono font-semibold text-txprimary ml-1"
                          x-text="(formData.mitigation.actual_reduction_tco2e || '0') + ' tCO₂e'"
                        ></span>
                      </div>
                      <template x-if="mitigationEfficiencyPercent !== null">
                        <div>
                          <span class="text-txsecondary font-medium"
                            >Hiệu quả giảm nhẹ:</span
                          >
                          <span
                            class="font-mono font-bold px-2.5 py-0.5 rounded-lg text-xs ml-1 inline-flex items-center space-x-1"
                            :class="mitigationEfficiencyPercent >= 100 ? 'bg-[#9fe870] text-[#003c33]' : 'bg-[#003c33]/10 text-[#003c33]'"
                          >
                            <i
                              data-lucide="check-circle-2"
                              class="w-3 h-3"
                              x-show="mitigationEfficiencyPercent >= 100"
                            ></i>
                            <span
                              x-text="mitigationEfficiencyPercent + '% so với kế hoạch'"
                            ></span>
                          </span>
                        </div>
                      </template>
                      <template x-if="formData.mitigation.plan_2026_2030">
                        <div
                          class="sm:col-span-2 bg-[#F8FAF9] p-3 rounded-xl border border-borderui/60 text-xs"
                        >
                          <span
                            class="text-txsecondary font-semibold block mb-0.5"
                            >Biện pháp giai đoạn 2026 – 2030:</span
                          >
                          <span
                            class="font-medium text-txprimary"
                            x-text="formData.mitigation.plan_2026_2030"
                          ></span>
                        </div>
                      </template>
                      <template x-if="formData.mitigation.implemented_measures">
                        <div
                          class="sm:col-span-2 bg-[#F8FAF9] p-3 rounded-xl border border-borderui/60 text-xs"
                        >
                          <span
                            class="text-txsecondary font-semibold block mb-0.5"
                            >Biện pháp đã thực hiện trong năm 2026:</span
                          >
                          <span
                            class="font-medium text-txprimary"
                            x-text="formData.mitigation.implemented_measures"
                          ></span>
                        </div>
                      </template>
                      <template x-if="formData.mitigation.report_url">
                        <div class="sm:col-span-2">
                          <span class="text-txsecondary font-medium"
                            >Link hồ sơ minh chứng:</span
                          >
                          <a
                            :href="formData.mitigation.report_url"
                            target="_blank"
                            class="text-[#003c33] font-bold ml-1 hover:underline"
                            >Xem liên kết đính kèm ↗</a
                          >
                        </div>
                      </template>
                      <template x-if="mitigationReportFile">
                        <div class="sm:col-span-2 flex items-center gap-2 rounded-xl border border-[#003c33]/20 bg-[#F0F6F3] p-3">
                          <i data-lucide="paperclip" class="h-4 w-4 shrink-0 text-[#003c33]" aria-hidden="true"></i>
                          <div class="min-w-0">
                            <span class="text-xs font-semibold text-txsecondary">File báo cáo sẽ gửi:</span>
                            <span class="ml-1 break-all text-sm font-bold text-[#003c33]" x-text="mitigationReportFile.name"></span>
                            <span class="ml-1 text-xs text-txsecondary" x-text="'(' + formatFileSize(mitigationReportFile.size) + ')' "></span>
                          </div>
                        </div>
                      </template>
                    </div>
                  </div>
                </div>

                <!-- CONFIRMATION CHECKBOX -->
                <div class="pt-5 border-t border-borderui">
                  <label
                    class="flex items-start space-x-3.5 cursor-pointer p-5 rounded-2xl border-2 transition-all"
                    :class="hasError('confirmation') ? 'border-danger bg-red-50/20' : (formData.confirmation ? 'border-[#003c33] bg-[#003c33]/5 shadow-sm' : 'border-borderui bg-white hover:bg-[#F8FAF9]')"
                  >
                    <input
                      type="checkbox"
                      x-model="formData.confirmation"
                      @change="clearFieldError('confirmation')"
                      class="w-5 h-5 rounded text-[#003c33] focus:ring-[#9fe870] border-borderui mt-0.5 cursor-pointer"
                    />
                    <div class="text-sm leading-relaxed select-none">
                      <span class="font-extrabold text-txprimary"
                        >Tôi xác nhận các thông tin và số liệu đã cung cấp là
                        chính xác</span
                      >
                      theo hồ sơ, hóa đơn chứng từ và dữ liệu hiện có của doanh
                      nghiệp, đồng thời chịu trách nhiệm hoàn toàn trước pháp
                      luật về tính trung thực của các số liệu này.
                    </div>
                  </label>
                  <template x-if="hasError('confirmation')">
                    <p
                      class="text-xs text-danger mt-2 flex items-center space-x-1"
                    >
                      <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                      <span x-text="getErrorMessage('confirmation')"></span>
                    </p>
                  </template>
                </div>
              </div>
            </div>
