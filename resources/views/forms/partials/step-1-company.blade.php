<div x-show="currentStep === 1" x-cloak class="space-y-6">
              <div
                class="bg-surface rounded-3xl border border-borderui p-7 sm:p-9 shadow-card space-y-7"
              >
                <!-- Section Header -->
                <div class="border-b border-borderui pb-5">
                  <div
                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-[#003c33]/10 text-[#003c33] font-bold text-xs uppercase tracking-wider mb-2"
                  >
                    <i data-lucide="building-2" class="w-3.5 h-3.5"></i>
                    <span>Bước 01/07</span>
                  </div>
                  <h3 class="text-xl font-bold text-txprimary tracking-tight">
                    Thông tin doanh nghiệp / cơ sở
                  </h3>
                  <p class="text-sm text-txsecondary mt-1">
                    Vui lòng cung cấp thông tin theo giấy đăng ký kinh doanh và
                    thông tin hiện hành của cơ sở.
                  </p>
                </div>

                <!-- General Company Info Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <!-- 1. Tên doanh nghiệp -->
                  <div class="sm:col-span-2">
                    <label
                      for="company_name"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Tên doanh nghiệp / Cơ sở (Theo ĐKKD)
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      id="company_name"
                      name="company_name"
                      x-model="formData.company.name"
                      @input="clearFieldError('company.name')"
                      @blur="validateField('company.name')"
                      :aria-invalid="hasError('company.name') ? 'true' : 'false'"
                      :aria-describedby="hasError('company.name') ? 'company_name_error' : null"
                      aria-required="true"
                      placeholder="Ví dụ: Công ty TNHH Sản xuất & Thương mại ABC Việt Nam"
                      class="w-full h-11 px-3.5 rounded-xl border text-sm transition-all focus-ring"
                      :class="hasError('company.name') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                    />
                    <template x-if="hasError('company.name')">
                      <p
                        id="company_name_error"
                        class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                      >
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span x-text="getErrorMessage('company.name')"></span>
                      </p>
                    </template>
                  </div>

                  <!-- 2. Mã số thuế -->
                  <div>
                    <label
                      for="company_tax_code"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Mã số thuế doanh nghiệp <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      id="company_tax_code"
                      name="company_tax_code"
                      x-model="formData.company.tax_code"
                      @input="clearFieldError('company.tax_code')"
                      @blur="validateField('company.tax_code')"
                      :aria-invalid="hasError('company.tax_code') ? 'true' : 'false'"
                      :aria-describedby="hasError('company.tax_code') ? 'company_tax_code_error' : null"
                      aria-required="true"
                      placeholder="Nhập mã số thuế (10 hoặc 13 chữ số)"
                      class="w-full h-11 px-3.5 rounded-xl border text-sm font-mono transition-all focus-ring"
                      :class="hasError('company.tax_code') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                    />
                    <template x-if="hasError('company.tax_code')">
                      <p
                        id="company_tax_code_error"
                        class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                      >
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span
                          x-text="getErrorMessage('company.tax_code')"
                        ></span>
                      </p>
                    </template>
                  </div>

                  <!-- 4. Lĩnh vực sản xuất kinh doanh -->
                  <div>
                    <label
                      for="company_industry"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Lĩnh vực / Ngành nghề sản xuất, kinh doanh chính
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      id="company_industry"
                      name="company_industry"
                      x-model="formData.company.industry"
                      @input="clearFieldError('company.industry')"
                      @blur="validateField('company.industry')"
                      :aria-invalid="hasError('company.industry') ? 'true' : 'false'"
                      :aria-describedby="hasError('company.industry') ? 'company_industry_error' : null"
                      aria-required="true"
                      placeholder="Ví dụ: Sản xuất thực phẩm, dệt may, cơ khí..."
                      class="w-full h-11 px-3.5 rounded-xl border text-sm transition-all focus-ring"
                      :class="hasError('company.industry') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                    />
                    <template x-if="hasError('company.industry')">
                      <p
                        id="company_industry_error"
                        class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                      >
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span
                          x-text="getErrorMessage('company.industry')"
                        ></span>
                      </p>
                    </template>
                  </div>

                  <!-- 3. Địa chỉ cơ sở / nhà máy -->
                  <div class="sm:col-span-2">
                    <label
                      for="company_address"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Địa chỉ cơ sở / nhà máy <span class="text-danger">*</span>
                    </label>
                    <textarea
                      id="company_address"
                      name="company_address"
                      rows="2"
                      x-model="formData.company.address"
                      @input="clearFieldError('company.address')"
                      @blur="validateField('company.address')"
                      :aria-invalid="hasError('company.address') ? 'true' : 'false'"
                      :aria-describedby="hasError('company.address') ? 'company_address_error' : null"
                      aria-required="true"
                      placeholder="Số nhà, đường, khu công nghiệp, phường/xã, quận/huyện, tỉnh/thành phố..."
                      class="w-full p-3 rounded-xl border text-sm transition-all focus-ring"
                      :class="hasError('company.address') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                    ></textarea>
                    <p
                      class="text-xs text-txsecondary mt-1 flex items-center space-x-1"
                    >
                      <i data-lucide="info" class="w-3 h-3 text-secondary"></i>
                      <span
                        >Vui lòng cập nhật theo địa giới hành chính hiện hành
                        sau khi sáp nhập/sắp xếp địa phương.</span
                      >
                    </p>
                    <template x-if="hasError('company.address')">
                      <p
                        id="company_address_error"
                        class="text-xs text-danger mt-1 flex items-center space-x-1"
                      >
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span
                          x-text="getErrorMessage('company.address')"
                        ></span>
                      </p>
                    </template>
                  </div>

                  <!-- 5. Email chính thức -->
                  <div class="sm:col-span-2">
                    <label
                      for="company_email"
                      class="block text-sm font-medium text-txprimary mb-1.5"
                    >
                      Email chính thức của doanh nghiệp/cơ sở
                      <span class="text-danger">*</span>
                    </label>
                    <div class="relative">
                      <input
                        type="email"
                        id="company_email"
                        name="company_email"
                        x-model="formData.company.email"
                        @input="clearFieldError('company.email')"
                        @blur="validateField('company.email')"
                        :aria-invalid="hasError('company.email') ? 'true' : 'false'"
                        :aria-describedby="hasError('company.email') ? 'company_email_error' : null"
                        aria-required="true"
                        placeholder="contact@company.com.vn"
                        class="w-full h-11 pl-10 pr-3.5 rounded-xl border text-sm transition-all focus-ring"
                        :class="hasError('company.email') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                      />
                      <i
                        data-lucide="mail"
                        class="w-4 h-4 text-txsecondary absolute left-3.5 top-3.5"
                      ></i>
                    </div>
                    <template x-if="hasError('company.email')">
                      <p
                        id="company_email_error"
                        class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                      >
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span x-text="getErrorMessage('company.email')"></span>
                      </p>
                    </template>
                  </div>
                </div>

                <!-- DIVIDER 1: Người đại diện theo pháp luật -->
                <div class="pt-4 border-t border-borderui">
                  <div
                    class="flex items-center space-x-2 text-sm font-bold text-txprimary mb-4"
                  >
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                    <span>Người đại diện theo pháp luật</span>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- 6. Họ và tên đại diện -->
                    <div>
                      <label
                        for="legal_rep_name"
                        class="block text-sm font-medium text-txprimary mb-1.5"
                      >
                        Họ và tên Người đại diện theo pháp luật
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        id="legal_rep_name"
                        x-model="formData.company.legal_representative.name"
                        @input="clearFieldError('company.legal_representative.name')"
                        @blur="validateField('company.legal_representative.name')"
                        :aria-invalid="hasError('company.legal_representative.name') ? 'true' : 'false'"
                        :aria-describedby="hasError('company.legal_representative.name') ? 'legal_rep_name_error' : null"
                        aria-required="true"
                        placeholder="Ví dụ: Nguyễn Văn A"
                        class="w-full h-11 px-3.5 rounded-xl border text-sm transition-all focus-ring"
                        :class="hasError('company.legal_representative.name') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                      />
                      <template
                        x-if="hasError('company.legal_representative.name')"
                      >
                        <p
                          id="legal_rep_name_error"
                          class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                        >
                          <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                          <span
                            x-text="getErrorMessage('company.legal_representative.name')"
                          ></span>
                        </p>
                      </template>
                    </div>

                    <!-- 7. SĐT đại diện -->
                    <div>
                      <label
                        for="legal_rep_phone"
                        class="block text-sm font-medium text-txprimary mb-1.5"
                      >
                        Số điện thoại Người đại diện
                        <span class="text-txsecondary font-normal"
                          >(Không bắt buộc)</span
                        >
                      </label>
                      <input
                        type="tel"
                        id="legal_rep_phone"
                        x-model="formData.company.legal_representative.phone"
                        placeholder="Ví dụ: 0912 345 678"
                        class="w-full h-11 px-3.5 rounded-xl border border-borderui bg-white text-sm transition-all focus-ring"
                      />
                    </div>
                  </div>
                </div>

                <!-- DIVIDER 2: Cán bộ phụ trách số liệu -->
                <div class="pt-4 border-t border-borderui">
                  <div
                    class="flex items-center space-x-2 text-sm font-bold text-txprimary mb-4"
                  >
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                    <span>Cán bộ / Đầu mối kỹ thuật phụ trách số liệu</span>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- 8. Họ tên cán bộ -->
                    <div>
                      <label
                        for="technical_contact_name"
                        class="block text-sm font-medium text-txprimary mb-1.5"
                      >
                        Họ và tên cán bộ phụ trách số liệu
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        id="technical_contact_name"
                        x-model="formData.company.technical_contact.name"
                        @input="clearFieldError('company.technical_contact.name')"
                        @blur="validateField('company.technical_contact.name')"
                        :aria-invalid="hasError('company.technical_contact.name') ? 'true' : 'false'"
                        :aria-describedby="hasError('company.technical_contact.name') ? 'technical_contact_name_error' : null"
                        aria-required="true"
                        placeholder="Ví dụ: Trần Thị B (Trưởng phòng HSE)"
                        class="w-full h-11 px-3.5 rounded-xl border text-sm transition-all focus-ring"
                        :class="hasError('company.technical_contact.name') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                      />
                      <template
                        x-if="hasError('company.technical_contact.name')"
                      >
                        <p
                          id="technical_contact_name_error"
                          class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                        >
                          <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                          <span
                            x-text="getErrorMessage('company.technical_contact.name')"
                          ></span>
                        </p>
                      </template>
                    </div>

                    <!-- 9. SĐT cán bộ -->
                    <div>
                      <label
                        for="technical_contact_phone"
                        class="block text-sm font-medium text-txprimary mb-1.5"
                      >
                        Số điện thoại cán bộ phụ trách số liệu
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="tel"
                        id="technical_contact_phone"
                        x-model="formData.company.technical_contact.phone"
                        @input="clearFieldError('company.technical_contact.phone')"
                        @blur="validateField('company.technical_contact.phone')"
                        :aria-invalid="hasError('company.technical_contact.phone') ? 'true' : 'false'"
                        :aria-describedby="hasError('company.technical_contact.phone') ? 'technical_contact_phone_error' : null"
                        aria-required="true"
                        placeholder="Ví dụ: 0987 654 321"
                        class="w-full h-11 px-3.5 rounded-xl border text-sm transition-all focus-ring"
                        :class="hasError('company.technical_contact.phone') ? 'border-danger bg-red-50/20 text-danger' : 'border-borderui bg-white text-txprimary'"
                      />
                      <template
                        x-if="hasError('company.technical_contact.phone')"
                      >
                        <p
                          id="technical_contact_phone_error"
                          class="text-xs text-danger mt-1.5 flex items-center space-x-1"
                        >
                          <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                          <span
                            x-text="getErrorMessage('company.technical_contact.phone')"
                          ></span>
                        </p>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ================================================================= -->
            <!-- STEP 2: KỲ KIỂM KÊ                                                -->
            <!-- ================================================================= -->
