function ghgApp() {
        return {
          // Steps definition
          currentStep: 1,
          completedSteps: [],
          steps: [
            {
              number: 1,
              title: "Thông tin DN",
              desc: "ĐKKD, địa chỉ, người liên hệ",
            },
            {
              number: 2,
              title: "Kỳ kiểm kê",
              desc: "Chọn năm 2024, 2025 hoặc cả hai",
            },
            {
              number: 3,
              title: "Phạm vi 1",
              desc: "Phát thải trực tiếp & nhiên liệu",
            },
            {
              number: 4,
              title: "Phạm vi 2",
              desc: "Điện lưới & điện mặt trời",
            },
            {
              number: 5,
              title: "Báo cáo KKKNK",
              desc: "Kết quả phát thải & link báo cáo",
            },
            {
              number: 6,
              title: "Giảm nhẹ",
              desc: "Kế hoạch giai đoạn 2026–2030",
            },
            {
              number: 7,
              title: "Xác nhận & Gửi",
              desc: "Kiểm tra tổng hợp & ký nộp",
            },
          ],

          // Reporting option in Step 2: '2024', '2025', 'both'
          reportingOption: "both",
          activeScope1Year: "2024",

          // Autosave status
          saveState: "idle", // 'idle' | 'saving' | 'saved'
          lastSavedTime: "",
          debounceTimer: null,

          // Modal states
          showDeleteModal: false,
          pendingDeleteYear: null,
          pendingDeleteIndex: null,
          showSubmitModal: false,
          isSubmitted: false,
          isSubmitting: false,
          submissionError: "",
          submittedDataReceipt: {
            code: "",
            time: "",
            excel_url: "",
          },

          // Errors map: fieldKey -> message
          errors: {},

          // Toast feedback
          toast: {
            visible: false,
            message: "",
          },

          // Main data structure matching enterprise requirements
          formData: {
            company: {
              name: "",
              tax_code: "",
              address: "",
              industry: "",
              email: "",
              legal_representative: {
                name: "",
                phone: "",
              },
              technical_contact: {
                name: "",
                phone: "",
              },
            },

            reporting_years: ["2024", "2025"],

            inventory: {
              2024: {
                has_scope1: null,
                has_boiler: false,
                boiler: {
                  capacity: "",
                  fuel: "Sinh khối",
                  fuel_other: "",
                  consumption: null,
                  unit: "tấn/năm",
                },
                has_cooling: false,
                refrigeration: {
                  equipment: "Máy lạnh",
                  equipment_other: "",
                  capacity: "2 HP",
                  gas_type: "R22",
                  gas_type_other: "",
                  full_charge_kg: null,
                  recharge_kg: null,
                },
                scope1_sources: [],
                grid_electricity_kwh: null,
                solar_electricity_kwh: null,
                energy_toe: null,
                scope1_emissions: null,
                scope2_emissions: null,
                report_method: "Chưa có báo cáo",
                report_url: "",
              },
              2025: {
                has_scope1: null,
                has_boiler: false,
                boiler: {
                  capacity: "",
                  fuel: "Sinh khối",
                  fuel_other: "",
                  consumption: null,
                  unit: "tấn/năm",
                },
                has_cooling: false,
                refrigeration: {
                  equipment: "Máy lạnh",
                  equipment_other: "",
                  capacity: "2 HP",
                  gas_type: "R22",
                  gas_type_other: "",
                  full_charge_kg: null,
                  recharge_kg: null,
                },
                scope1_sources: [],
                grid_electricity_kwh: null,
                solar_electricity_kwh: null,
                energy_toe: null,
                scope1_emissions: null,
                scope2_emissions: null,
                report_method: "Chưa có báo cáo",
                report_url: "",
              },
              2026: {
                has_scope1: null,
                has_boiler: false,
                boiler: {
                  capacity: "",
                  fuel: "Sinh khối",
                  fuel_other: "",
                  consumption: null,
                  unit: "tấn/năm",
                },
                has_cooling: false,
                refrigeration: {
                  equipment: "Máy lạnh",
                  equipment_other: "",
                  capacity: "2 HP",
                  gas_type: "R22",
                  gas_type_other: "",
                  full_charge_kg: null,
                  recharge_kg: null,
                },
                scope1_sources: [],
                grid_electricity_kwh: null,
                solar_electricity_kwh: null,
                energy_toe: null,
                scope1_emissions: null,
                scope2_emissions: null,
                report_method: "Chưa có báo cáo",
                report_url: "",
              },
            },

            mitigation: {
              implemented: null,
              plan_2026_2030: "",
              implemented_measures: "",
              planned_reduction_tco2e: null,
              actual_reduction_tco2e: null,
              note: "",
              report_url: "",
            },

            confirmation: false,
          },

          // Computed Progress Percentage
          get mitigationEfficiencyPercent() {
            const planned = parseFloat(
              this.formData.mitigation.planned_reduction_tco2e,
            );
            const actual = parseFloat(
              this.formData.mitigation.actual_reduction_tco2e,
            );
            if (!planned || planned <= 0 || isNaN(actual)) return null;
            return Math.round((actual / planned) * 1000) / 10;
          },

          get stepProgressPercent() {
            return Math.round((this.currentStep / 7) * 100);
          },

          get completionPercentage() {
            // Weighted calculation based on filled key fields
            let total = 0;
            let filled = 0;

            // Company
            const c = this.formData.company;
            total += 7;
            if (c.name) filled++;
            if (c.tax_code) filled++;
            if (c.address) filled++;
            if (c.industry) filled++;
            if (c.email) filled++;
            if (c.legal_representative.name) filled++;
            if (c.technical_contact.name && c.technical_contact.phone) filled++;

            // Years
            total += 1;
            if (this.formData.reporting_years.length > 0) filled++;

            // For each year
            for (const yr of this.formData.reporting_years) {
              total += 3;
              const inv = this.formData.inventory[yr];
              if (inv.has_scope1 !== null) filled++;
              if (
                inv.grid_electricity_kwh !== null &&
                inv.grid_electricity_kwh !== ""
              )
                filled++;
              if (inv.report_method) filled++;
            }

            // Mitigation
            total += 1;
            if (this.formData.mitigation.implemented !== null) filled++;

            return Math.min(100, Math.round((filled / total) * 100));
          },

          // App Initializer
          initApp() {
            // Check for existing draft in localStorage
            this.loadDraft();

            // Initialize active Scope 1 year
            if (this.formData.reporting_years.length > 0) {
              this.activeScope1Year = this.formData.reporting_years[0];
            }

            // Deep watch formData for autosave
            this.$watch(
              "formData",
              () => {
                this.triggerAutosave();
              },
              { deep: true },
            );

            // Re-render Lucide icons on any step change or dynamic render
            this.$watch("currentStep", () => {
              this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
              });
            });

            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
            });
          },

          // Autosave logic
          triggerAutosave() {
            this.saveState = "saving";
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
              this.saveDraft(false);
            }, 800);
          },

          saveDraft(showNotice = false) {
            try {
              const payload = {
                currentStep: this.currentStep,
                completedSteps: this.completedSteps,
                reportingOption: this.reportingOption,
                formData: this.formData,
                savedAt: new Date().toISOString(),
              };
              localStorage.setItem(
                "ghg_inventory_form_draft",
                JSON.stringify(payload),
              );

              const now = new Date();
              this.lastSavedTime = now.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
              });
              this.saveState = "saved";

              if (showNotice) {
                this.showToast(
                  "Đã lưu bản nháp thành công (" + this.lastSavedTime + ")",
                );
              }
            } catch (e) {
              console.error("Failed to autosave to localStorage:", e);
              this.saveState = "idle";
            }
          },

          loadDraft() {
            try {
              const raw = localStorage.getItem("ghg_inventory_form_draft");
              if (raw) {
                const data = JSON.parse(raw);
                if (data.formData) {
                  this.formData = Object.assign(this.formData, data.formData);
                  if (data.reportingOption)
                    this.reportingOption = data.reportingOption;
                  if (data.completedSteps)
                    this.completedSteps = data.completedSteps;
                  if (data.savedAt) {
                    const d = new Date(data.savedAt);
                    this.lastSavedTime = d.toLocaleTimeString([], {
                      hour: "2-digit",
                      minute: "2-digit",
                    });
                    this.saveState = "saved";
                  }
                }
              }
            } catch (e) {
              console.warn("Draft not loaded or invalid format:", e);
            }
          },

          showToast(msg) {
            this.toast.message = msg;
            this.toast.visible = true;
            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
            });
            setTimeout(() => {
              this.toast.visible = false;
            }, 3000);
          },

          // Reporting Year Selection (Step 2)
          selectReportingOption(opt) {
            this.reportingOption = opt;
            if (opt === "2024") {
              this.formData.reporting_years = ["2024"];
              this.activeScope1Year = "2024";
            } else if (opt === "2025") {
              this.formData.reporting_years = ["2025"];
              this.activeScope1Year = "2025";
            } else if (opt === "2026") {
              this.formData.reporting_years = ["2026"];
              this.activeScope1Year = "2026";
            } else if (opt === "2024_2026") {
              this.formData.reporting_years = ["2024", "2025", "2026"];
              this.activeScope1Year = "2024";
            } else {
              this.formData.reporting_years = ["2024", "2025"];
              this.activeScope1Year = "2024";
            }
            // Ensure year inventory exists
            for (const yr of this.formData.reporting_years) {
              this.ensureYearInventory(yr);
            }
            this.clearFieldError("reporting_years");
          },

          ensureYearInventory(yr) {
            if (!this.formData.inventory[yr]) {
              this.formData.inventory[yr] = {
                has_scope1: null,
                has_boiler: false,
                boiler: {
                  capacity: "",
                  fuel: "Sinh khối",
                  fuel_other: "",
                  consumption: null,
                  unit: "tấn/năm",
                },
                has_cooling: false,
                refrigeration: {
                  equipment: "Máy lạnh",
                  equipment_other: "",
                  capacity: "2 HP",
                  gas_type: "R22",
                  gas_type_other: "",
                  full_charge_kg: null,
                  recharge_kg: null,
                },
                scope1_sources: [],
                grid_electricity_kwh: null,
                solar_electricity_kwh: null,
                energy_toe: null,
                scope1_emissions: null,
                scope2_emissions: null,
                report_method: "Chưa có báo cáo",
                report_url: "",
              };
            }
          },

          insertMitigationMeasure(text) {
            if (!this.formData.mitigation.plan_2026_2030) {
              this.formData.mitigation.plan_2026_2030 = text;
            } else if (
              !this.formData.mitigation.plan_2026_2030.includes(text)
            ) {
              this.formData.mitigation.plan_2026_2030 += "\n" + text;
            }
            this.showToast("Đã thêm biện pháp vào kế hoạch");
          },

          fillTakigawaSampleData() {
            this.formData.company.name = "Công ty TNHH Takigawa Việt Nam";
            this.formData.company.tax_code = "3701858627";
            this.formData.company.address =
              "Số 10, đường số 14, khu công nghiệp VSIP II-A, phường Bình Hòa, Thành phố Hồ Chí Minh";
            this.formData.company.industry = "Sản xuất, in ấn, thiết kế bao bì";
            this.formData.company.email = "info@takigawa.vn";
            this.formData.company.legal_representative.name =
              "Ông Takigawa Hiroshi";
            this.formData.company.legal_representative.phone = "02743841777";
            this.formData.company.technical_contact.name = "Chị Nguyệt Sương";
            this.formData.company.technical_contact.phone = "0903841777";

            this.selectReportingOption("2024_2026");

            // Year 2024
            const y24 = this.formData.inventory["2024"];
            y24.has_scope1 = true;
            y24.has_boiler = true;
            y24.boiler = {
              capacity: "3 tấn hơi/giờ",
              fuel: "Sinh khối",
              fuel_other: "",
              consumption: 500,
              unit: "tấn/năm",
            };
            y24.has_cooling = true;
            y24.refrigeration = {
              equipment: "Máy lạnh",
              equipment_other: "",
              capacity: "2 HP",
              gas_type: "R22",
              gas_type_other: "",
              full_charge_kg: 10,
              recharge_kg: 2,
            };
            y24.scope1_sources = [
              {
                id: "src_takigawa_1",
                source_type: "Đốt nhiên liệu di động",
                source_type_other: "",
                fuel_type: "Dầu DO",
                fuel_type_other: "",
                quantity: 15000,
                unit: "lít",
                unit_other: "",
                note: "Phương tiện vận tải của Công ty",
              },
            ];
            y24.grid_electricity_kwh = 3000000;
            y24.solar_electricity_kwh = 300000;
            y24.energy_toe = 1900;
            y24.scope1_emissions = 2000;
            y24.scope2_emissions = 1200;
            y24.report_method = "Kê khai trực tiếp theo hóa đơn";

            // Year 2025
            const y25 = this.formData.inventory["2025"];
            y25.has_scope1 = true;
            y25.has_boiler = true;
            y25.boiler = {
              capacity: "3 tấn hơi/giờ",
              fuel: "Sinh khối",
              fuel_other: "",
              consumption: 500,
              unit: "tấn/năm",
            };
            y25.has_cooling = true;
            y25.refrigeration = {
              equipment: "Máy lạnh",
              equipment_other: "",
              capacity: "2 HP",
              gas_type: "R22",
              gas_type_other: "",
              full_charge_kg: 10,
              recharge_kg: 2,
            };
            y25.scope1_sources = [
              {
                id: "src_takigawa_2",
                source_type: "Đốt nhiên liệu di động",
                source_type_other: "",
                fuel_type: "Dầu DO",
                fuel_type_other: "",
                quantity: 15000,
                unit: "lít",
                unit_other: "",
                note: "Phương tiện vận tải của Công ty",
              },
            ];
            y25.grid_electricity_kwh = 3400000;
            y25.solar_electricity_kwh = 300000;
            y25.energy_toe = 1850;
            y25.scope1_emissions = 2000;
            y25.scope2_emissions = 1350;
            y25.report_method = "Kê khai trực tiếp theo hóa đơn";

            // Year 2026
            const y26 = this.formData.inventory["2026"];
            y26.has_scope1 = true;
            y26.has_boiler = true;
            y26.boiler = {
              capacity: "3 tấn hơi/giờ",
              fuel: "Sinh khối",
              fuel_other: "",
              consumption: 500,
              unit: "tấn/năm",
            };
            y26.has_cooling = true;
            y26.refrigeration = {
              equipment: "Máy lạnh",
              equipment_other: "",
              capacity: "2 HP",
              gas_type: "R22",
              gas_type_other: "",
              full_charge_kg: 10,
              recharge_kg: 2,
            };
            y26.scope1_sources = [
              {
                id: "src_takigawa_3",
                source_type: "Đốt nhiên liệu di động",
                source_type_other: "",
                fuel_type: "Dầu DO",
                fuel_type_other: "",
                quantity: 15000,
                unit: "lít",
                unit_other: "",
                note: "Phương tiện vận tải của Công ty",
              },
            ];
            y26.grid_electricity_kwh = 3800000;
            y26.solar_electricity_kwh = 290000;
            y26.energy_toe = 1880;
            y26.scope1_emissions = 2000;
            y26.scope2_emissions = 1500;
            y26.report_method = "Kê khai trực tiếp theo hóa đơn";

            // Mitigation
            this.formData.mitigation.implemented = true;
            this.formData.mitigation.plan_2026_2030 = `- Lắp đặt hệ thống điện mặt trời mái nhà để tự dùng.\n- Cải tiến, nâng cấp lò hơi, lò nung và thiết bị sản xuất.\n- Tối ưu hóa quy trình công nghệ và giảm tiêu hao nhiên liệu.\n- Thay thế thiết bị, động cơ hiệu suất thấp bằng thiết bị hiệu suất cao.\n- Điện khí hóa phương tiện, thiết bị vận chuyển và bốc xếp trong cơ sở.\n- Giảm rò rỉ nhiên liệu, khí gas và môi chất lạnh.\n- Tăng cường thu hồi, tái sử dụng nhiệt thải.`;
            this.formData.mitigation.implemented_measures = `- Gắn pin năng lượng mặt trời mái nhà 1 Mw.\n- Thay máy thổi khí cho công trình xử lý nước thải;\n- Thay xe nâng điện`;
            this.formData.mitigation.planned_reduction_tco2e = 1000;
            this.formData.mitigation.actual_reduction_tco2e = 200;

            this.showToast(
              "Đã nạp thành công số liệu mẫu Takigawa từ file Excel!",
            );
            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
            });
          },

          // Scope 1 Helpers (Step 3)
          setScope1Presence(year, hasIt) {
            this.formData.inventory[year].has_scope1 = hasIt;
            if (
              hasIt &&
              this.formData.inventory[year].scope1_sources.length === 0
            ) {
              this.addScope1Source(year);
            }
            this.clearFieldError("inventory." + year + ".has_scope1");
            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
            });
          },

          addScope1Source(year) {
            this.formData.inventory[year].scope1_sources.push({
              id:
                "src_" +
                Date.now() +
                "_" +
                Math.random().toString(36).substr(2, 4),
              source_type: "",
              source_type_other: "",
              fuel_type: "",
              fuel_type_other: "",
              quantity: null,
              unit: "",
              unit_other: "",
              note: "",
            });
            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
            });
          },

          openDeleteSourceModal(year, index) {
            if (
              this.formData.inventory[year].scope1_sources.length <= 1 &&
              this.formData.inventory[year].has_scope1 === true
            ) {
              alert(
                'Không thể xóa nguồn phát thải duy nhất khi đã chọn "Có phát sinh nguồn phát thải". Nếu không có nguồn nào, vui lòng chọn lại tùy chọn "Không có nguồn phát thải".',
              );
              return;
            }
            this.pendingDeleteYear = year;
            this.pendingDeleteIndex = index;
            this.showDeleteModal = true;
            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
            });
          },

          confirmDeleteSource() {
            if (this.pendingDeleteYear && this.pendingDeleteIndex !== null) {
              this.formData.inventory[
                this.pendingDeleteYear
              ].scope1_sources.splice(this.pendingDeleteIndex, 1);
              this.pendingDeleteYear = null;
              this.pendingDeleteIndex = null;
              this.showDeleteModal = false;
              this.showToast("Đã xóa nguồn phát thải.");
              this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
              });
            }
          },

          // Navigation & Stepper Rules
          canNavigateTo(stepNumber) {
            return true;
          },

          jumpToStep(stepNumber) {
            if (this.canNavigateTo(stepNumber)) {
              this.currentStep = stepNumber;
              window.scrollTo({ top: 0, behavior: "smooth" });
            }
          },

          prevStep() {
            if (this.currentStep > 1) {
              this.currentStep--;
              window.scrollTo({ top: 0, behavior: "smooth" });
            }
          },

          handleNext() {
            if (this.validateCurrentStep()) {
              if (!this.completedSteps.includes(this.currentStep)) {
                this.completedSteps.push(this.currentStep);
              }
              if (this.currentStep < 7) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: "smooth" });
              }
            }
          },

          // Validation Engine
          hasError(fieldKey) {
            return !!this.errors[fieldKey];
          },

          getErrorMessage(fieldKey) {
            return this.errors[fieldKey] || "";
          },

          clearFieldError(fieldKey) {
            if (this.errors[fieldKey]) {
              delete this.errors[fieldKey];
            }
          },

          validateField(fieldKey) {
            if (fieldKey === "company.name") {
              if (
                !this.formData.company.name ||
                !this.formData.company.name.trim()
              ) {
                this.errors["company.name"] =
                  "Vui lòng nhập tên doanh nghiệp / cơ sở theo ĐKKD.";
              } else {
                delete this.errors["company.name"];
              }
            } else if (fieldKey === "company.tax_code") {
              const tc = this.formData.company.tax_code;
              if (!tc || !tc.trim()) {
                this.errors["company.tax_code"] = "Vui lòng nhập mã số thuế.";
              } else if (tc.trim().length < 8) {
                this.errors["company.tax_code"] =
                  "Mã số thuế không hợp lệ (tối thiểu 8 ký tự).";
              } else {
                delete this.errors["company.tax_code"];
              }
            } else if (fieldKey === "company.address") {
              if (
                !this.formData.company.address ||
                !this.formData.company.address.trim()
              ) {
                this.errors["company.address"] =
                  "Vui lòng cung cấp địa chỉ cơ sở / nhà máy.";
              } else {
                delete this.errors["company.address"];
              }
            } else if (fieldKey === "company.industry") {
              if (
                !this.formData.company.industry ||
                !this.formData.company.industry.trim()
              ) {
                this.errors["company.industry"] =
                  "Vui lòng nhập ngành nghề sản xuất, kinh doanh chính.";
              } else {
                delete this.errors["company.industry"];
              }
            } else if (fieldKey === "company.email") {
              const em = this.formData.company.email;
              if (!em || !em.trim()) {
                this.errors["company.email"] =
                  "Vui lòng nhập email chính thức.";
              } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.trim())) {
                this.errors["company.email"] = "Email không đúng định dạng.";
              } else {
                delete this.errors["company.email"];
              }
            } else if (fieldKey === "company.legal_representative.name") {
              if (
                !this.formData.company.legal_representative.name ||
                !this.formData.company.legal_representative.name.trim()
              ) {
                this.errors["company.legal_representative.name"] =
                  "Vui lòng nhập họ và tên Người đại diện theo pháp luật.";
              } else {
                delete this.errors["company.legal_representative.name"];
              }
            } else if (fieldKey === "company.technical_contact.name") {
              if (
                !this.formData.company.technical_contact.name ||
                !this.formData.company.technical_contact.name.trim()
              ) {
                this.errors["company.technical_contact.name"] =
                  "Vui lòng nhập họ và tên cán bộ phụ trách số liệu.";
              } else {
                delete this.errors["company.technical_contact.name"];
              }
            } else if (fieldKey === "company.technical_contact.phone") {
              if (
                !this.formData.company.technical_contact.phone ||
                !this.formData.company.technical_contact.phone.trim()
              ) {
                this.errors["company.technical_contact.phone"] =
                  "Vui lòng nhập số điện thoại cán bộ phụ trách số liệu.";
              } else {
                delete this.errors["company.technical_contact.phone"];
              }
            } else if (fieldKey.includes(".grid_electricity_kwh")) {
              const yr = fieldKey.split(".")[1];
              const val = this.formData.inventory[yr]?.grid_electricity_kwh;
              if (val === null || val === "" || isNaN(val) || Number(val) < 0) {
                this.errors[fieldKey] =
                  "Vui lòng nhập lượng điện lưới tiêu thụ (>= 0) cho năm " + yr;
              } else {
                delete this.errors[fieldKey];
              }
            } else if (fieldKey.includes(".solar_electricity_kwh")) {
              const yr = fieldKey.split(".")[1];
              const val = this.formData.inventory[yr]?.solar_electricity_kwh;
              if (val === null || val === "" || isNaN(val) || Number(val) < 0) {
                this.errors[fieldKey] =
                  "Vui lòng nhập lượng điện mặt trời (>= 0) cho năm " + yr;
              } else {
                delete this.errors[fieldKey];
              }
            }
          },

          focusFieldByKey(key) {
            let elemId = null;
            if (key === "company.name") elemId = "company_name";
            else if (key === "company.tax_code") elemId = "company_tax_code";
            else if (key === "company.address") elemId = "company_address";
            else if (key === "company.industry") elemId = "company_industry";
            else if (key === "company.email") elemId = "company_email";
            else if (key === "company.legal_representative.name")
              elemId = "legal_rep_name";
            else if (key === "company.technical_contact.name")
              elemId = "technical_contact_name";
            else if (key === "company.technical_contact.phone")
              elemId = "technical_contact_phone";
            else if (key.includes("grid_electricity_kwh"))
              elemId = key
                .replace("inventory.", "grid_elec_")
                .replace(".grid_electricity_kwh", "");
            else if (key.includes("solar_electricity_kwh"))
              elemId = key
                .replace("inventory.", "solar_elec_")
                .replace(".solar_electricity_kwh", "");
            else if (key.includes("report_url"))
              elemId = key
                .replace("inventory.", "report_url_")
                .replace(".report_url", "");

            if (elemId) {
              const el = document.getElementById(elemId);
              if (el) {
                el.scrollIntoView({ behavior: "smooth", block: "center" });
                el.focus();
              }
            }
          },

          validateCurrentStep() {
            this.errors = {};
            let isValid = true;
            let firstErrorElement = null;

            const markError = (key, msg, elemId) => {
              this.errors[key] = msg;
              isValid = false;
              if (!firstErrorElement && elemId) {
                firstErrorElement = document.getElementById(elemId);
              }
            };

            // STEP 1 VALIDATION
            if (this.currentStep === 1) {
              const c = this.formData.company;
              if (!c.name || !c.name.trim())
                markError(
                  "company.name",
                  "Vui lòng nhập tên doanh nghiệp / cơ sở theo ĐKKD.",
                  "company_name",
                );
              if (!c.tax_code || !c.tax_code.trim()) {
                markError(
                  "company.tax_code",
                  "Vui lòng nhập mã số thuế.",
                  "company_tax_code",
                );
              } else if (c.tax_code.trim().length < 8) {
                markError(
                  "company.tax_code",
                  "Mã số thuế không hợp lệ.",
                  "company_tax_code",
                );
              }
              if (!c.address || !c.address.trim())
                markError(
                  "company.address",
                  "Vui lòng cung cấp địa chỉ cơ sở / nhà máy.",
                  "company_address",
                );
              if (!c.industry || !c.industry.trim())
                markError(
                  "company.industry",
                  "Vui lòng nhập ngành nghề sản xuất, kinh doanh chính.",
                  "company_industry",
                );

              if (!c.email || !c.email.trim()) {
                markError(
                  "company.email",
                  "Vui lòng nhập email chính thức.",
                  "company_email",
                );
              } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c.email.trim())) {
                markError(
                  "company.email",
                  "Email không đúng định dạng.",
                  "company_email",
                );
              }

              if (
                !c.legal_representative.name ||
                !c.legal_representative.name.trim()
              ) {
                markError(
                  "company.legal_representative.name",
                  "Vui lòng nhập họ và tên Người đại diện theo pháp luật.",
                  "legal_rep_name",
                );
              }

              if (
                !c.technical_contact.name ||
                !c.technical_contact.name.trim()
              ) {
                markError(
                  "company.technical_contact.name",
                  "Vui lòng nhập họ và tên cán bộ phụ trách số liệu.",
                  "technical_contact_name",
                );
              }

              if (
                !c.technical_contact.phone ||
                !c.technical_contact.phone.trim()
              ) {
                markError(
                  "company.technical_contact.phone",
                  "Vui lòng nhập số điện thoại cán bộ phụ trách số liệu.",
                  "technical_contact_phone",
                );
              }
            }

            // STEP 2 VALIDATION
            if (this.currentStep === 2) {
              if (
                !this.formData.reporting_years ||
                this.formData.reporting_years.length === 0
              ) {
                markError(
                  "reporting_years",
                  "Vui lòng chọn kỳ kiểm kê cần báo cáo số liệu.",
                );
              }
            }

            // STEP 3 VALIDATION
            if (this.currentStep === 3) {
              for (const yr of this.formData.reporting_years) {
                const inv = this.formData.inventory[yr];
                if (inv.has_scope1 === null) {
                  markError(
                    "inventory." + yr + ".has_scope1",
                    "Vui lòng xác nhận có phát sinh nguồn phát thải Phạm vi 1 trong năm " +
                      yr +
                      " hay không.",
                  );
                } else if (inv.has_scope1 === true) {
                  if (!inv.scope1_sources || inv.scope1_sources.length === 0) {
                    markError(
                      "inventory." + yr + ".has_scope1",
                      "Vui lòng thêm ít nhất một nguồn phát thải cho năm " +
                        yr +
                        ".",
                    );
                  } else {
                    // Check sources details
                    for (let i = 0; i < inv.scope1_sources.length; i++) {
                      const src = inv.scope1_sources[i];
                      if (!src.source_type) {
                        markError(
                          "inventory." + yr + ".scope1_sources",
                          "Vui lòng chọn loại nguồn phát thải ở nguồn #" +
                            (i + 1),
                        );
                        break;
                      }
                      if (!src.fuel_type) {
                        markError(
                          "inventory." + yr + ".scope1_sources",
                          "Vui lòng chọn loại nhiên liệu / chất sử dụng ở nguồn #" +
                            (i + 1),
                        );
                        break;
                      }
                      if (
                        src.quantity === null ||
                        src.quantity === "" ||
                        isNaN(src.quantity) ||
                        Number(src.quantity) < 0
                      ) {
                        markError(
                          "inventory." + yr + ".scope1_sources",
                          "Lượng sử dụng ở nguồn #" +
                            (i + 1) +
                            " phải là số lớn hơn hoặc bằng 0.",
                        );
                        break;
                      }
                      if (!src.unit) {
                        markError(
                          "inventory." + yr + ".scope1_sources",
                          "Vui lòng chọn đơn vị tính ở nguồn #" + (i + 1),
                        );
                        break;
                      }
                    }
                  }
                }
              }
            }

            // STEP 4 VALIDATION
            if (this.currentStep === 4) {
              for (const yr of this.formData.reporting_years) {
                const inv = this.formData.inventory[yr];
                if (
                  inv.grid_electricity_kwh === null ||
                  inv.grid_electricity_kwh === "" ||
                  isNaN(inv.grid_electricity_kwh) ||
                  Number(inv.grid_electricity_kwh) < 0
                ) {
                  markError(
                    "inventory." + yr + ".grid_electricity_kwh",
                    "Vui lòng nhập lượng điện lưới tiêu thụ (>= 0) cho năm " +
                      yr,
                    "grid_elec_" + yr,
                  );
                }
                if (
                  inv.solar_electricity_kwh === null ||
                  inv.solar_electricity_kwh === "" ||
                  isNaN(inv.solar_electricity_kwh) ||
                  Number(inv.solar_electricity_kwh) < 0
                ) {
                  markError(
                    "inventory." + yr + ".solar_electricity_kwh",
                    "Vui lòng nhập lượng điện mặt trời (nhập 0 nếu không sử dụng) cho năm " +
                      yr,
                    "solar_elec_" + yr,
                  );
                }
              }
            }

            // STEP 5 VALIDATION
            if (this.currentStep === 5) {
              for (const yr of this.formData.reporting_years) {
                const inv = this.formData.inventory[yr];
                if (inv.report_method === "Dán link báo cáo") {
                  if (!inv.report_url || !inv.report_url.trim()) {
                    markError(
                      "inventory." + yr + ".report_url",
                      "Vui lòng dán đường dẫn (URL) báo cáo cho năm " + yr,
                      "report_url_" + yr,
                    );
                  } else if (!/^https?:\/\/.+/i.test(inv.report_url.trim())) {
                    markError(
                      "inventory." + yr + ".report_url",
                      "Đường dẫn phải bắt đầu bằng http:// hoặc https://",
                      "report_url_" + yr,
                    );
                  }
                }
              }
            }

            // STEP 6 VALIDATION
            if (this.currentStep === 6) {
              if (this.formData.mitigation.implemented === null) {
                markError(
                  "mitigation.implemented",
                  "Vui lòng chọn tình trạng thực hiện biện pháp giảm nhẹ.",
                );
              }
            }

            // STEP 7 VALIDATION
            if (this.currentStep === 7) {
              if (!this.formData.confirmation) {
                markError(
                  "confirmation",
                  "Vui lòng tích chọn cam kết xác nhận tính chính xác của dữ liệu trước khi gửi.",
                );
              }
            }

            // Auto-scroll and focus first error if any
            if (!isValid) {
              this.$nextTick(() => {
                if (firstErrorElement) {
                  firstErrorElement.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                  });
                  firstErrorElement.focus();
                } else {
                  window.scrollTo({ top: 300, behavior: "smooth" });
                }
              });
            }

            return isValid;
          },

          // Submission Modal and Action
          openSubmitConfirmationModal() {
            if (this.validateCurrentStep()) {
              this.submissionError = "";
              this.showSubmitModal = true;
              this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
              });
            }
          },

          async executeSubmit() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;
            this.submissionError = "";

            // Send to Laravel Backend
            try {
              const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
              const response = await fetch('/api/submissions', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({ formData: this.formData })
              });

              const result = await response.json();

              if (!response.ok) {
                const serverErrors = result.errors || {};
                this.errors = Object.fromEntries(
                  Object.entries(serverErrors).map(([key, messages]) => [
                    key.replace(/^formData\./, ""),
                    Array.isArray(messages) ? messages[0] : messages,
                  ]),
                );

                throw new Error(
                  response.status === 422
                    ? "Dữ liệu chưa hợp lệ. Vui lòng đóng hộp thoại và kiểm tra lại các trường được đánh dấu."
                    : "Hệ thống chưa thể tiếp nhận hồ sơ. Vui lòng thử lại.",
                );
              }

              this.submittedDataReceipt = result.receipt;
              this.showSubmitModal = false;
              this.isSubmitted = true;
              this.saveDraft(false);

              window.scrollTo({ top: 0, behavior: "smooth" });
              this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
              });
            } catch (e) {
              console.error("Không thể gửi hồ sơ:", e);
              this.submissionError =
                e instanceof Error
                  ? e.message
                  : "Hệ thống chưa thể tiếp nhận hồ sơ. Vui lòng thử lại.";
              this.showToast(this.submissionError);
            } finally {
              this.isSubmitting = false;
            }
          },

          printReceipt() {
            window.print();
          },

          resetForm() {
            if (
              confirm(
                "Bạn có chắc muốn tạo hồ sơ mới? Bản nháp hiện tại sẽ được làm mới.",
              )
            ) {
              localStorage.removeItem("ghg_inventory_form_draft");
              location.reload();
            }
          },

          formatNumber(val) {
            if (val === null || val === undefined || val === "") return "0";
            return new Intl.NumberFormat("vi-VN").format(val);
          },
        };
      }
