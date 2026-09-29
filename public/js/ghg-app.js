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
              desc: "Chọn kỳ báo cáo trong giai đoạn 2024–2026",
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

          // Reporting option in Step 2
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
            report_file_url: "",
            report_file_name: "",
          },
          mitigationReportFile: null,

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
                has_scope1: true,
                has_boiler: false,
                boilers: [],
                has_cooling: false,
                refrigeration_systems: [],
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
                has_scope1: true,
                has_boiler: false,
                boilers: [],
                has_cooling: false,
                refrigeration_systems: [],
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
                has_scope1: true,
                has_boiler: false,
                boilers: [],
                has_cooling: false,
                refrigeration_systems: [],
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
              if (inv.scope1_sources.length > 0) filled++;
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
            this.initializeScopeOneData();

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
              if (showNotice) {
                this.showToast(
                  "Không thể lưu bản nháp trên trình duyệt này. Vui lòng thử lại.",
                );
              }
            }
          },

          loadDraft() {
            try {
              const raw = localStorage.getItem("ghg_inventory_form_draft");
              if (raw) {
                const data = JSON.parse(raw);
                if (data.formData) {
                  this.formData = this.normalizeDraftFormData(data.formData);
                  this.reportingOption = this.reportingOptionForYears(
                    this.formData.reporting_years,
                  );
                  this.currentStep = Number.isInteger(data.currentStep)
                    ? Math.min(7, Math.max(1, data.currentStep))
                    : 1;
                  this.completedSteps = Array.isArray(data.completedSteps)
                    ? data.completedSteps.filter(
                        (step) => Number.isInteger(step) && step >= 1 && step <= 7,
                      )
                    : [];
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

          mergeKnownDraftShape(defaultValue, draftValue) {
            if (Array.isArray(defaultValue)) {
              return Array.isArray(draftValue) ? draftValue : defaultValue;
            }

            if (
              defaultValue !== null &&
              typeof defaultValue === "object" &&
              !Array.isArray(defaultValue)
            ) {
              const draftObject =
                draftValue !== null && typeof draftValue === "object"
                  ? draftValue
                  : {};

              return Object.fromEntries(
                Object.entries(defaultValue).map(([key, value]) => [
                  key,
                  this.mergeKnownDraftShape(value, draftObject[key]),
                ]),
              );
            }

            if (defaultValue === null) {
              return draftValue === null ||
                typeof draftValue === "string" ||
                typeof draftValue === "number" ||
                typeof draftValue === "boolean"
                ? draftValue
                : null;
            }

            return typeof draftValue === typeof defaultValue
              ? draftValue
              : defaultValue;
          },

          normalizeDraftFormData(draftFormData) {
            const draftInventory = draftFormData?.inventory;

            if (draftInventory && typeof draftInventory === "object") {
              for (const year of ["2024", "2025", "2026"]) {
                const yearInventory = draftInventory[year];

                if (!yearInventory || typeof yearInventory !== "object") {
                  continue;
                }

                if (
                  !Array.isArray(yearInventory.boilers) &&
                  yearInventory.boiler &&
                  typeof yearInventory.boiler === "object"
                ) {
                  yearInventory.boilers = [yearInventory.boiler];
                }

                if (
                  !Array.isArray(yearInventory.refrigeration_systems) &&
                  yearInventory.refrigeration &&
                  typeof yearInventory.refrigeration === "object"
                ) {
                  yearInventory.refrigeration_systems = [
                    yearInventory.refrigeration,
                  ];
                }
              }
            }

            const normalized = this.mergeKnownDraftShape(
              this.formData,
              draftFormData,
            );
            const allowedYears = ["2024", "2025", "2026"];
            const reportingYears = Array.isArray(normalized.reporting_years)
              ? [...new Set(normalized.reporting_years.map(String))].filter(
                  (year) => allowedYears.includes(year),
                )
              : [];

            normalized.reporting_years =
              reportingYears.length > 0 ? reportingYears : ["2024", "2025"];

            const sourceDefaults = {
              id: "",
              source_type: "",
              source_type_other: "",
              fuel_type: "",
              fuel_type_other: "",
              quantity: null,
              unit: "",
              unit_other: "",
              note: "",
            };
            const boilerDefaults = this.newBoiler();
            const refrigerationDefaults = this.newRefrigerationSystem();

            for (const year of allowedYears) {
              normalized.inventory[year].has_scope1 = true;
              const sources = normalized.inventory[year].scope1_sources;
              normalized.inventory[year].scope1_sources = Array.isArray(sources)
                ? sources
                    .filter(
                      (source) => source !== null && typeof source === "object",
                    )
                    .map((source) =>
                      this.mergeKnownDraftShape(sourceDefaults, source),
                    )
                : [];

              const boilers = normalized.inventory[year].boilers;
              normalized.inventory[year].boilers = Array.isArray(boilers)
                ? boilers
                    .filter(
                      (boiler) => boiler !== null && typeof boiler === "object",
                    )
                    .map((boiler) =>
                      this.mergeKnownDraftShape(
                        { ...boilerDefaults, id: this.newEquipmentId("boiler") },
                        boiler,
                      ),
                    )
                : [];

              const refrigerationSystems =
                normalized.inventory[year].refrigeration_systems;
              normalized.inventory[year].refrigeration_systems = Array.isArray(
                refrigerationSystems,
              )
                ? refrigerationSystems
                    .filter(
                      (system) => system !== null && typeof system === "object",
                    )
                    .map((system) =>
                      this.mergeKnownDraftShape(
                        {
                          ...refrigerationDefaults,
                          id: this.newEquipmentId("cooling"),
                        },
                        system,
                      ),
                    )
                : [];

              if (
                normalized.inventory[year].has_boiler &&
                normalized.inventory[year].boilers.length === 0
              ) {
                normalized.inventory[year].boilers.push(this.newBoiler());
              }

              if (
                normalized.inventory[year].has_cooling &&
                normalized.inventory[year].refrigeration_systems.length === 0
              ) {
                normalized.inventory[year].refrigeration_systems.push(
                  this.newRefrigerationSystem(),
                );
              }
            }

            return normalized;
          },

          initializeScopeOneData() {
            for (const year of ["2024", "2025", "2026"]) {
              this.ensureYearInventory(year);
              this.formData.inventory[year].has_scope1 = true;

              if (this.formData.inventory[year].scope1_sources.length === 0) {
                this.formData.inventory[year].scope1_sources.push(
                  this.newScope1Source(),
                );
              }
            }
          },

          newScope1Source() {
            return {
              id:
                "src_" +
                Date.now() +
                "_" +
                Math.random().toString(36).slice(2, 6),
              source_type: "",
              source_type_other: "",
              fuel_type: "",
              fuel_type_other: "",
              quantity: null,
              unit: "",
              unit_other: "",
              note: "",
            };
          },

          newEquipmentId(prefix) {
            return (
              prefix +
              "_" +
              Date.now() +
              "_" +
              Math.random().toString(36).slice(2, 6)
            );
          },

          newBoiler() {
            return {
              id: this.newEquipmentId("boiler"),
              capacity: "",
              fuel: "Sinh khối",
              fuel_other: "",
              consumption: null,
              unit: "tấn/năm",
            };
          },

          newRefrigerationSystem() {
            return {
              id: this.newEquipmentId("cooling"),
              equipment: "Máy lạnh",
              equipment_other: "",
              capacity: "",
              gas_type: "R22",
              gas_type_other: "",
              full_charge_kg: null,
              recharge_kg: null,
            };
          },

          toggleBoilers(year, enabled = null) {
            const inventory = this.formData.inventory[year];
            inventory.has_boiler =
              typeof enabled === "boolean" ? enabled : inventory.has_boiler;

            if (inventory.has_boiler && inventory.boilers.length === 0) {
              inventory.boilers.push(this.newBoiler());
            }

            if (!inventory.has_boiler) {
              this.clearErrorsByPrefix("inventory." + year + ".boilers");
            }
          },

          addBoiler(year) {
            this.formData.inventory[year].boilers.push(this.newBoiler());
            this.$nextTick(() => window.lucide?.createIcons());
          },

          removeBoiler(year, index) {
            const inventory = this.formData.inventory[year];
            inventory.boilers.splice(index, 1);
            this.clearErrorsByPrefix("inventory." + year + ".boilers");

            if (inventory.boilers.length === 0) {
              inventory.has_boiler = false;
            }
          },

          toggleRefrigerationSystems(year, enabled = null) {
            const inventory = this.formData.inventory[year];
            inventory.has_cooling =
              typeof enabled === "boolean" ? enabled : inventory.has_cooling;

            if (
              inventory.has_cooling &&
              inventory.refrigeration_systems.length === 0
            ) {
              inventory.refrigeration_systems.push(
                this.newRefrigerationSystem(),
              );
            }

            if (!inventory.has_cooling) {
              this.clearErrorsByPrefix(
                "inventory." + year + ".refrigeration_systems",
              );
            }
          },

          addRefrigerationSystem(year) {
            this.formData.inventory[year].refrigeration_systems.push(
              this.newRefrigerationSystem(),
            );
            this.$nextTick(() => window.lucide?.createIcons());
          },

          removeRefrigerationSystem(year, index) {
            const inventory = this.formData.inventory[year];
            inventory.refrigeration_systems.splice(index, 1);
            this.clearErrorsByPrefix(
              "inventory." + year + ".refrigeration_systems",
            );

            if (inventory.refrigeration_systems.length === 0) {
              inventory.has_cooling = false;
            }
          },

          reportingOptionForYears(years) {
            const signature = [...years].sort().join(",");

            if (signature === "2024") return "2024";
            if (signature === "2025") return "2025";
            if (signature === "2026") return "2026";
            if (signature === "2024,2025,2026") return "2024_2026";

            return "both";
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
            for (const key of Object.keys(this.errors)) {
              const match = key.match(/^inventory\.(2024|2025|2026)\./);
              if (match && !this.formData.reporting_years.includes(match[1])) {
                delete this.errors[key];
              }
            }
            this.clearFieldError("reporting_years");
          },

          ensureYearInventory(yr) {
            if (!this.formData.inventory[yr]) {
              this.formData.inventory[yr] = {
                has_scope1: true,
                has_boiler: false,
                boilers: [],
                has_cooling: false,
                refrigeration_systems: [],
                scope1_sources: [this.newScope1Source()],
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

          handleMitigationReportFile(event) {
            this.mitigationReportFile = event.target.files?.[0] || null;
            this.clearFieldError("mitigation_report_file");

            if (this.mitigationReportFile) {
              this.validateMitigationReportFile();
            }
          },

          removeMitigationReportFile() {
            this.mitigationReportFile = null;
            this.clearFieldError("mitigation_report_file");

            if (this.$refs.mitigationReportInput) {
              this.$refs.mitigationReportInput.value = "";
            }
          },

          validateMitigationReportFile() {
            if (!this.mitigationReportFile) return true;

            const allowedExtensions = ["pdf", "doc", "docx", "xls", "xlsx"];
            const extension = this.mitigationReportFile.name
              .split(".")
              .pop()
              ?.toLowerCase();

            if (!extension || !allowedExtensions.includes(extension)) {
              this.errors.mitigation_report_file =
                "File báo cáo chỉ chấp nhận định dạng PDF, Word hoặc Excel.";
              return false;
            }

            if (this.mitigationReportFile.size > 10 * 1024 * 1024) {
              this.errors.mitigation_report_file =
                "File báo cáo không được lớn hơn 10 MB.";
              return false;
            }

            return true;
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
            this.formData.company.legal_representative.phone = "0901000000";
            this.formData.company.technical_contact.name = "Chị Nguyệt Sương";
            this.formData.company.technical_contact.phone = "0903841777";

            this.selectReportingOption("2024_2026");

            // Year 2024
            const y24 = this.formData.inventory["2024"];
            y24.has_scope1 = true;
            y24.has_boiler = true;
            y24.boilers = [{
              id: this.newEquipmentId("boiler"),
              capacity: "3 tấn hơi/giờ",
              fuel: "Sinh khối",
              fuel_other: "",
              consumption: 500,
              unit: "tấn/năm",
            }];
            y24.has_cooling = true;
            y24.refrigeration_systems = [{
              id: this.newEquipmentId("cooling"),
              equipment: "Máy lạnh",
              equipment_other: "",
              capacity: "2 HP",
              gas_type: "R22",
              gas_type_other: "",
              full_charge_kg: 10,
              recharge_kg: 2,
            }];
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
            y25.boilers = [{
              id: this.newEquipmentId("boiler"),
              capacity: "3 tấn hơi/giờ",
              fuel: "Sinh khối",
              fuel_other: "",
              consumption: 500,
              unit: "tấn/năm",
            }];
            y25.has_cooling = true;
            y25.refrigeration_systems = [{
              id: this.newEquipmentId("cooling"),
              equipment: "Máy lạnh",
              equipment_other: "",
              capacity: "2 HP",
              gas_type: "R22",
              gas_type_other: "",
              full_charge_kg: 10,
              recharge_kg: 2,
            }];
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
            y26.boilers = [{
              id: this.newEquipmentId("boiler"),
              capacity: "3 tấn hơi/giờ",
              fuel: "Sinh khối",
              fuel_other: "",
              consumption: 500,
              unit: "tấn/năm",
            }];
            y26.has_cooling = true;
            y26.refrigeration_systems = [{
              id: this.newEquipmentId("cooling"),
              equipment: "Máy lạnh",
              equipment_other: "",
              capacity: "2 HP",
              gas_type: "R22",
              gas_type_other: "",
              full_charge_kg: 10,
              recharge_kg: 2,
            }];
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
          addScope1Source(year) {
            this.formData.inventory[year].scope1_sources.push(
              this.newScope1Source(),
            );
            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
            });
          },

          openDeleteSourceModal(year, index) {
            if (
              this.formData.inventory[year].scope1_sources.length <= 1
            ) {
              alert(
                "Phạm vi 1 luôn phải có ít nhất một nguồn phát thải. Vui lòng cập nhật nguồn hiện tại hoặc thêm nguồn mới trước khi xóa.",
              );
              return;
            }
            this.pendingDeleteYear = year;
            this.pendingDeleteIndex = index;
            this.showDeleteModal = true;
            this.$nextTick(() => {
              if (window.lucide) lucide.createIcons();
              this.$refs.deleteCancelButton?.focus();
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
            return (
              stepNumber === 1 ||
              stepNumber === this.currentStep ||
              this.completedSteps.includes(stepNumber) ||
              this.completedSteps.includes(stepNumber - 1)
            );
          },

          jumpToStep(stepNumber) {
            if (
              this.currentStep === 1 &&
              stepNumber > 1 &&
              !this.validateCurrentStep(1)
            ) {
              return;
            }

            if (this.canNavigateTo(stepNumber)) {
              this.currentStep = stepNumber;
              window.scrollTo({ top: 0, behavior: this.scrollBehavior() });
            }
          },

          prevStep() {
            if (this.currentStep > 1) {
              this.currentStep--;
              window.scrollTo({ top: 0, behavior: this.scrollBehavior() });
            }
          },

          handleNext() {
            if (this.validateCurrentStep()) {
              if (!this.completedSteps.includes(this.currentStep)) {
                this.completedSteps.push(this.currentStep);
              }
              if (this.currentStep < 7) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: this.scrollBehavior() });
              }
            }
          },

          // Validation Engine
          hasError(fieldKey) {
            return !!this.errors[fieldKey];
          },

          hasErrorPrefix(fieldPrefix) {
            return Object.keys(this.errors).some(
              (key) => key === fieldPrefix || key.startsWith(fieldPrefix + "."),
            );
          },

          getErrorMessage(fieldKey) {
            return this.errors[fieldKey] || "";
          },

          getFirstErrorMessage(fieldPrefix) {
            const key = Object.keys(this.errors).find(
              (errorKey) =>
                errorKey === fieldPrefix ||
                errorKey.startsWith(fieldPrefix + "."),
            );

            return key ? this.errors[key] : "";
          },

          clearFieldError(fieldKey) {
            if (this.errors[fieldKey]) {
              delete this.errors[fieldKey];
            }
          },

          clearErrorsByPrefix(fieldPrefix) {
            for (const key of Object.keys(this.errors)) {
              if (key === fieldPrefix || key.startsWith(fieldPrefix + ".")) {
                delete this.errors[key];
              }
            }
          },

          isVietnameseMobilePhone(phone) {
            if (typeof phone !== "string") return false;

            const normalizedPhone = phone
              .trim()
              .replace(/[\s.-]/g, "")
              .replace(/^\+84/, "0");

            return /^0(?:3|5|7|8|9)\d{8}$/.test(normalizedPhone);
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
            } else if (fieldKey === "company.legal_representative.phone") {
              const phone = this.formData.company.legal_representative.phone;
              if (!phone || !phone.trim()) {
                this.errors["company.legal_representative.phone"] =
                  "Vui lòng nhập số điện thoại Người đại diện.";
              } else if (!this.isVietnameseMobilePhone(phone)) {
                this.errors["company.legal_representative.phone"] =
                  "Số điện thoại Người đại diện phải là số di động Việt Nam hợp lệ.";
              } else {
                delete this.errors["company.legal_representative.phone"];
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
              } else if (
                !this.isVietnameseMobilePhone(
                  this.formData.company.technical_contact.phone,
                )
              ) {
                this.errors["company.technical_contact.phone"] =
                  "Số điện thoại cán bộ phụ trách phải là số di động Việt Nam hợp lệ.";
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
            const stepNumber = this.stepForField(key);
            this.currentStep = stepNumber;

            const inventoryMatch = key.match(/^inventory\.(2024|2025|2026)\./);
            if (inventoryMatch && stepNumber === 3) {
              this.activeScope1Year = inventoryMatch[1];
            }

            let elemId = null;
            if (key === "company.name") elemId = "company_name";
            else if (key === "company.tax_code") elemId = "company_tax_code";
            else if (key === "company.address") elemId = "company_address";
            else if (key === "company.industry") elemId = "company_industry";
            else if (key === "company.email") elemId = "company_email";
            else if (key === "company.legal_representative.name")
              elemId = "legal_rep_name";
            else if (key === "company.legal_representative.phone")
              elemId = "legal_rep_phone";
            else if (key === "company.technical_contact.name")
              elemId = "technical_contact_name";
            else if (key === "company.technical_contact.phone")
              elemId = "technical_contact_phone";
            else if (key.includes(".scope1_sources"))
              elemId = "scope1_sources_" + key.split(".")[1];
            else if (key.includes("grid_electricity_kwh"))
              elemId = key
                .replace("inventory.", "grid_elec_")
                .replace(".grid_electricity_kwh", "");
            else if (key.includes("solar_electricity_kwh"))
              elemId = key
                .replace("inventory.", "solar_elec_")
                .replace(".solar_electricity_kwh", "");
            else if (key.includes("energy_toe"))
              elemId = key
                .replace("inventory.", "energy_toe_")
                .replace(".energy_toe", "");
            else if (key.includes("scope1_emissions"))
              elemId = key
                .replace("inventory.", "scope1_em_")
                .replace(".scope1_emissions", "");
            else if (key.includes("scope2_emissions"))
              elemId = key
                .replace("inventory.", "scope2_em_")
                .replace(".scope2_emissions", "");
            else if (
              key.startsWith("inventory.") &&
              key.includes("report_url")
            )
              elemId = key
                .replace("inventory.", "report_url_")
                .replace(".report_url", "");
            else if (key === "mitigation.implemented_measures")
              elemId = "implemented_measures";
            else if (key === "mitigation.planned_reduction_tco2e")
              elemId = "planned_red";
            else if (key === "mitigation.actual_reduction_tco2e")
              elemId = "actual_red";
            else if (key === "mitigation.report_url")
              elemId = "mitigation_report_url";
            else if (key === "mitigation_report_file")
              elemId = "mitigation_report_file";

            this.$nextTick(() => {
              if (!elemId) return;

              const el = document.getElementById(elemId);
              if (el) {
                el.scrollIntoView({
                  behavior: this.scrollBehavior(),
                  block: "center",
                });
                el.focus();
              }
            });
          },

          stepForField(key) {
            if (key === "company" || key.startsWith("company.")) return 1;
            if (key.startsWith("reporting_years")) return 2;
            if (key.includes(".has_scope1") || key.includes(".scope1_sources") || key.includes(".boiler") || key.includes(".refrigeration")) return 3;
            if (key.includes("grid_electricity_kwh") || key.includes("solar_electricity_kwh") || key.includes("energy_toe")) return 4;
            if (key.includes("scope1_emissions") || key.includes("scope2_emissions") || key.includes("report_method") || key.includes("report_url") && key.startsWith("inventory.")) return 5;
            if (key === "inventory" || key.startsWith("inventory.")) return 3;
            if (key.startsWith("mitigation.")) return 6;
            if (key === "mitigation_report_file") return 6;

            return 7;
          },

          validateCurrentStep(
            stepNumber = this.currentStep,
            resetErrors = true,
            focusOnError = true,
          ) {
            if (resetErrors) this.errors = {};
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
            if (stepNumber === 1) {
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
                !c.legal_representative.phone ||
                !c.legal_representative.phone.trim()
              ) {
                markError(
                  "company.legal_representative.phone",
                  "Vui lòng nhập số điện thoại Người đại diện.",
                  "legal_rep_phone",
                );
              } else if (
                !this.isVietnameseMobilePhone(c.legal_representative.phone)
              ) {
                markError(
                  "company.legal_representative.phone",
                  "Số điện thoại Người đại diện phải là số di động Việt Nam hợp lệ.",
                  "legal_rep_phone",
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
              } else if (
                !this.isVietnameseMobilePhone(c.technical_contact.phone)
              ) {
                markError(
                  "company.technical_contact.phone",
                  "Số điện thoại cán bộ phụ trách phải là số di động Việt Nam hợp lệ.",
                  "technical_contact_phone",
                );
              }
            }

            // STEP 2 VALIDATION
            if (stepNumber === 2) {
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
            if (stepNumber === 3) {
              for (const yr of this.formData.reporting_years) {
                const inv = this.formData.inventory[yr];
                if (inv.has_boiler) {
                  if (!inv.boilers || inv.boilers.length === 0) {
                    markError(
                      "inventory." + yr + ".boilers",
                      "Vui lòng khai báo ít nhất một lò hơi cho năm " + yr + ".",
                    );
                  }

                  for (let i = 0; i < inv.boilers.length; i++) {
                    const boiler = inv.boilers[i];
                    const boilerPath =
                      "inventory." + yr + ".boilers." + i;
                    if (!boiler.capacity || !boiler.capacity.trim()) {
                      markError(
                        boilerPath + ".capacity",
                        "Vui lòng nhập công suất thiết kế lò hơi #" + (i + 1) + ".",
                      );
                    }
                    if (!boiler.fuel) {
                      markError(
                        boilerPath + ".fuel",
                        "Vui lòng chọn nhiên liệu đốt lò hơi #" + (i + 1) + ".",
                      );
                    }
                    if (boiler.fuel === "Khác" && (!boiler.fuel_other || !boiler.fuel_other.trim())) {
                      markError(
                        boilerPath + ".fuel_other",
                        "Vui lòng nêu rõ nhiên liệu khác của lò hơi #" + (i + 1) + ".",
                      );
                    }
                    if (boiler.consumption === null || boiler.consumption === "" || isNaN(boiler.consumption) || Number(boiler.consumption) < 0) {
                      markError(
                        boilerPath + ".consumption",
                        "Lượng đốt lò hơi #" + (i + 1) + " phải là số lớn hơn hoặc bằng 0.",
                      );
                    }
                    if (!boiler.unit) {
                      markError(
                        boilerPath + ".unit",
                        "Vui lòng chọn đơn vị lượng đốt lò hơi #" + (i + 1) + ".",
                      );
                    }
                  }
                }

                if (inv.has_cooling) {
                  if (
                    !inv.refrigeration_systems ||
                    inv.refrigeration_systems.length === 0
                  ) {
                    markError(
                      "inventory." + yr + ".refrigeration_systems",
                      "Vui lòng khai báo ít nhất một hệ thống lạnh cho năm " + yr + ".",
                    );
                  }

                  for (
                    let i = 0;
                    i < inv.refrigeration_systems.length;
                    i++
                  ) {
                    const refrigeration = inv.refrigeration_systems[i];
                    const refrigerationPath =
                      "inventory." + yr + ".refrigeration_systems." + i;
                    if (!refrigeration.equipment) {
                      markError(
                        refrigerationPath + ".equipment",
                        "Vui lòng chọn thiết bị lạnh #" + (i + 1) + ".",
                      );
                    }
                    if (refrigeration.equipment === "Khác" && (!refrigeration.equipment_other || !refrigeration.equipment_other.trim())) {
                      markError(
                        refrigerationPath + ".equipment_other",
                        "Vui lòng nêu rõ thiết bị lạnh khác ở hệ thống #" + (i + 1) + ".",
                      );
                    }
                    if (!refrigeration.capacity || !refrigeration.capacity.trim()) {
                      markError(
                        refrigerationPath + ".capacity",
                        "Vui lòng nhập công suất lạnh của hệ thống #" + (i + 1) + ".",
                      );
                    }
                    if (!refrigeration.gas_type) {
                      markError(
                        refrigerationPath + ".gas_type",
                        "Vui lòng chọn môi chất lạnh của hệ thống #" + (i + 1) + ".",
                      );
                    }
                    if (refrigeration.gas_type === "Khác" && (!refrigeration.gas_type_other || !refrigeration.gas_type_other.trim())) {
                      markError(
                        refrigerationPath + ".gas_type_other",
                        "Vui lòng nêu rõ môi chất lạnh khác ở hệ thống #" + (i + 1) + ".",
                      );
                    }
                    if (refrigeration.full_charge_kg === null || refrigeration.full_charge_kg === "" || isNaN(refrigeration.full_charge_kg) || Number(refrigeration.full_charge_kg) < 0) {
                      markError(
                        refrigerationPath + ".full_charge_kg",
                        "Lượng gas nạp đầy của hệ thống #" + (i + 1) + " phải là số lớn hơn hoặc bằng 0.",
                      );
                    }
                  }
                }

                  if (!inv.scope1_sources || inv.scope1_sources.length === 0) {
                    markError(
                      "inventory." + yr + ".scope1_sources",
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
                          "inventory." + yr + ".scope1_sources." + i + ".source_type",
                          "Vui lòng chọn loại nguồn phát thải ở nguồn #" +
                            (i + 1),
                        );
                      } else if (src.source_type === "Khác" && (!src.source_type_other || !src.source_type_other.trim())) {
                        markError(
                          "inventory." + yr + ".scope1_sources." + i + ".source_type_other",
                          "Vui lòng nêu rõ loại nguồn phát thải khác ở nguồn #" + (i + 1),
                        );
                      }
                      if (!src.fuel_type) {
                        markError(
                          "inventory." + yr + ".scope1_sources." + i + ".fuel_type",
                          "Vui lòng chọn loại nhiên liệu / chất sử dụng ở nguồn #" +
                            (i + 1),
                        );
                      } else if (src.fuel_type === "Khác" && (!src.fuel_type_other || !src.fuel_type_other.trim())) {
                        markError(
                          "inventory." + yr + ".scope1_sources." + i + ".fuel_type_other",
                          "Vui lòng nêu rõ nhiên liệu / chất khác ở nguồn #" + (i + 1),
                        );
                      }
                      if (
                        src.quantity === null ||
                        src.quantity === "" ||
                        isNaN(src.quantity) ||
                        Number(src.quantity) < 0
                      ) {
                        markError(
                          "inventory." + yr + ".scope1_sources." + i + ".quantity",
                          "Lượng sử dụng ở nguồn #" +
                            (i + 1) +
                            " phải là số lớn hơn hoặc bằng 0.",
                        );
                      }
                      if (!src.unit) {
                        markError(
                          "inventory." + yr + ".scope1_sources." + i + ".unit",
                          "Vui lòng chọn đơn vị tính ở nguồn #" + (i + 1),
                        );
                      } else if (src.unit === "Khác" && (!src.unit_other || !src.unit_other.trim())) {
                        markError(
                          "inventory." + yr + ".scope1_sources." + i + ".unit_other",
                          "Vui lòng nêu rõ đơn vị tính khác ở nguồn #" + (i + 1),
                        );
                      }
                    }
                }
              }
            }

            // STEP 4 VALIDATION
            if (stepNumber === 4) {
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
                if (this.isInvalidOptionalNonNegativeNumber(inv.energy_toe)) {
                  markError(
                    "inventory." + yr + ".energy_toe",
                    "Tổng năng lượng quy đổi TOE năm " + yr + " phải là số lớn hơn hoặc bằng 0.",
                    "energy_toe_" + yr,
                  );
                }
              }
            }

            // STEP 5 VALIDATION
            if (stepNumber === 5) {
              for (const yr of this.formData.reporting_years) {
                const inv = this.formData.inventory[yr];
                if (this.isInvalidOptionalNonNegativeNumber(inv.scope1_emissions)) {
                  markError(
                    "inventory." + yr + ".scope1_emissions",
                    "Phát thải Phạm vi 1 năm " + yr + " phải là số lớn hơn hoặc bằng 0.",
                    "scope1_em_" + yr,
                  );
                }
                if (this.isInvalidOptionalNonNegativeNumber(inv.scope2_emissions)) {
                  markError(
                    "inventory." + yr + ".scope2_emissions",
                    "Phát thải Phạm vi 2 năm " + yr + " phải là số lớn hơn hoặc bằng 0.",
                    "scope2_em_" + yr,
                  );
                }
                if (!inv.report_method) {
                  markError(
                    "inventory." + yr + ".report_method",
                    "Vui lòng chọn hình thức cung cấp báo cáo cho năm " + yr,
                  );
                } else if (inv.report_method === "Dán link báo cáo") {
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
            if (stepNumber === 6) {
              if (this.formData.mitigation.implemented === null) {
                markError(
                  "mitigation.implemented",
                  "Vui lòng chọn tình trạng thực hiện biện pháp giảm nhẹ.",
                );
              } else if (
                this.formData.mitigation.implemented === true &&
                (!this.formData.mitigation.implemented_measures ||
                  !this.formData.mitigation.implemented_measures.trim())
              ) {
                markError(
                  "mitigation.implemented_measures",
                  "Vui lòng mô tả các biện pháp giảm nhẹ đã thực hiện.",
                  "implemented_measures",
                );
              }
              if (
                this.isInvalidOptionalNonNegativeNumber(
                  this.formData.mitigation.planned_reduction_tco2e,
                )
              ) {
                markError(
                  "mitigation.planned_reduction_tco2e",
                  "Lượng phát thải dự kiến cắt giảm phải là số lớn hơn hoặc bằng 0.",
                  "planned_red",
                );
              }
              if (
                this.isInvalidOptionalNonNegativeNumber(
                  this.formData.mitigation.actual_reduction_tco2e,
                )
              ) {
                markError(
                  "mitigation.actual_reduction_tco2e",
                  "Lượng phát thải thực tế cắt giảm phải là số lớn hơn hoặc bằng 0.",
                  "actual_red",
                );
              }
              if (
                this.formData.mitigation.report_url &&
                !this.isHttpUrl(this.formData.mitigation.report_url)
              ) {
                markError(
                  "mitigation.report_url",
                  "Đường dẫn báo cáo giảm nhẹ phải bắt đầu bằng http:// hoặc https://.",
                  "mitigation_report_url",
                );
              }
              if (!this.validateMitigationReportFile()) {
                isValid = false;
                firstErrorElement ||= document.getElementById(
                  "mitigation_report_file",
                );
              }
            }

            // STEP 7 VALIDATION
            if (stepNumber === 7) {
              if (!this.formData.confirmation) {
                markError(
                  "confirmation",
                  "Vui lòng tích chọn cam kết xác nhận tính chính xác của dữ liệu trước khi gửi.",
                );
              }
            }

            // Auto-scroll and focus first error if any
            if (!isValid && focusOnError) {
              this.$nextTick(() => {
                firstErrorElement?.scrollIntoView({
                  behavior: this.scrollBehavior(),
                  block: "center",
                });
                firstErrorElement?.focus();
              });
            }

            return isValid;
          },

          validateEntireForm() {
            this.errors = {};
            let firstInvalidStep = null;

            for (let stepNumber = 1; stepNumber <= 7; stepNumber++) {
              if (!this.validateCurrentStep(stepNumber, false, false)) {
                firstInvalidStep ??= stepNumber;
              }
            }

            if (firstInvalidStep !== null) {
              this.currentStep = firstInvalidStep;
              const firstErrorKey = Object.keys(this.errors)[0];
              const inventoryMatch = firstErrorKey?.match(
                /^inventory\.(2024|2025|2026)\./,
              );
              if (inventoryMatch && firstInvalidStep === 3) {
                this.activeScope1Year = inventoryMatch[1];
              }
              this.focusFieldByKey(firstErrorKey);

              return false;
            }

            return true;
          },

          // Submission Modal and Action
          openSubmitConfirmationModal() {
            if (this.validateEntireForm()) {
              this.submissionError = "";
              this.showSubmitModal = true;
              this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
                this.$refs.submitCancelButton?.focus();
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
              const payload = new FormData();
              payload.append("formData", JSON.stringify(this.formData));

              if (this.mitigationReportFile) {
                payload.append(
                  "mitigation_report_file",
                  this.mitigationReportFile,
                );
              }

              const response = await fetch('/api/submissions', {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken || '',
                },
                body: payload,
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

                const firstErrorKey = Object.keys(this.errors)[0];
                if (firstErrorKey) {
                  this.showSubmitModal = false;
                  this.currentStep = this.stepForField(firstErrorKey);

                  const inventoryMatch = firstErrorKey.match(
                    /^inventory\.(2024|2025|2026)\./,
                  );
                  if (inventoryMatch && this.currentStep === 3) {
                    this.activeScope1Year = inventoryMatch[1];
                  }

                  this.focusFieldByKey(firstErrorKey);
                }

                throw new Error(
                  response.status === 422
                    ? "Dữ liệu chưa hợp lệ. Vui lòng đóng hộp thoại và kiểm tra lại các trường được đánh dấu."
                    : "Hệ thống chưa thể tiếp nhận hồ sơ. Vui lòng thử lại.",
                );
              }

              this.submittedDataReceipt = result.receipt;
              this.showSubmitModal = false;
              this.isSubmitted = true;
              localStorage.removeItem("ghg_inventory_form_draft");
              this.saveState = "idle";

              window.scrollTo({ top: 0, behavior: this.scrollBehavior() });
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

          formatFileSize(bytes) {
            if (!Number.isFinite(bytes) || bytes <= 0) return "0 KB";

            if (bytes < 1024 * 1024) {
              return `${Math.max(1, Math.round(bytes / 1024))} KB`;
            }

            return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
          },

          isInvalidOptionalNonNegativeNumber(value) {
            return (
              value !== null &&
              value !== "" &&
              (isNaN(value) || Number(value) < 0)
            );
          },

          isHttpUrl(value) {
            return /^https?:\/\/.+/i.test(String(value).trim());
          },

          scrollBehavior() {
            return window.matchMedia?.("(prefers-reduced-motion: reduce)")
              .matches
              ? "auto"
              : "smooth";
          },
        };
      }
