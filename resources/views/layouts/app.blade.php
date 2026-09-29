<!doctype html>
<html lang="vi" class="h-full bg-[#F4F7F5]">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>
      @yield('title', 'Bảng Cung Cấp Số Liệu Kiểm Kê Khí Nhà Kính và Kế Hoạch Giảm Nhẹ - Sở Công Thương TP.HCM')
    </title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-so-cong-thuong.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    <!-- Google Fonts: Be Vietnam Pro & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
      rel="stylesheet"
    />

    <!-- Tailwind CSS CDN with EcoCheck brand guidelines -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Be Vietnam Pro"', '"Inter"', "sans-serif"],
              mono: ['"JetBrains Mono"', "monospace"],
            },
            colors: {
              primary: {
                DEFAULT: "#003c33",
                50: "#F0F7F4",
                100: "#D9ECE4",
                200: "#B5DCCE",
                500: "#1a7e4b",
                600: "#003c33",
                700: "#002f28",
                800: "#00251f",
                900: "#091710",
              },
              accent: {
                DEFAULT: "#9fe870",
                hover: "#8ee05b",
                light: "#f1faeb",
                subtle: "#e4f7d9",
                dark: "#003c33",
              },
              ecopine: {
                DEFAULT: "#003c33",
                dark: "#002820",
                night: "#091710",
                hover: "#0c4a3f",
                light: "#e6f3ef",
              },
              ecolime: {
                DEFAULT: "#9fe870",
                hover: "#8ee05b",
                light: "#f1faeb",
                subtle: "#e4f7d9",
              },
              secondary: "#003c33",
              surface: "#FFFFFF",
              canvas: "#F4F7F5",
              borderui: "#DCE5E0",
              txprimary: "#101828",
              txsecondary: "#5A6E65",
              danger: "#DC2626",
              warning: "#D97706",
            },
            boxShadow: {
              subtle:
                "0 1px 3px 0 rgba(0, 40, 32, 0.04), 0 1px 2px -1px rgba(0, 40, 32, 0.02)",
              card: "0 4px 20px -2px rgba(0, 60, 51, 0.06), 0 2px 6px -1px rgba(0, 60, 51, 0.03)",
              elevated:
                "0 12px 32px -4px rgba(0, 60, 51, 0.10), 0 4px 12px -2px rgba(0, 60, 51, 0.04)",
              glow: "0 0 20px rgba(159, 232, 112, 0.45)",
              pine: "0 8px 24px -4px rgba(0, 60, 51, 0.28)",
            },
            borderRadius: {
              xl: "0.75rem",
              "2xl": "1rem",
              "3xl": "1.5rem",
            },
          },
        },
      };
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js & Alpine Persist Plugin -->
    <script
      defer
      src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"
    ></script>
    <script
      defer
      src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
    ></script>

    <style>
      [x-cloak] {
        display: none !important;
      }

      input[type="number"]::-webkit-inner-spin-button,
      input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }
      input[type="number"] {
        -moz-appearance: textfield;
      }

      .focus-ring:focus {
        outline: none;
        box-shadow:
          0 0 0 2px #ffffff,
          0 0 0 4px #003c33,
          0 0 12px rgba(159, 232, 112, 0.35);
        border-color: #003c33;
      }

      html {
        scroll-padding-top: 8.5rem;
      }

      button:focus-visible,
      a:focus-visible,
      [role="radio"]:focus-visible {
        outline: 3px solid #003c33;
        outline-offset: 3px;
      }

      @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
          scroll-behavior: auto !important;
          animation-duration: 0.01ms !important;
          animation-iteration-count: 1 !important;
          transition-duration: 0.01ms !important;
        }
      }

      @media print {
        header,
        aside,
        .sticky,
        button:not(.print-include),
        nav {
          display: none !important;
        }
        body,
        main,
        section {
          background: white !important;
          padding: 0 !important;
          margin: 0 !important;
        }
        .shadow-subtle,
        .shadow-card,
        .shadow-elevated {
          box-shadow: none !important;
          border-color: #cbd5e1 !important;
        }
      }

      ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
      }
      ::-webkit-scrollbar-track {
        background: #f4f7f5;
      }
      ::-webkit-scrollbar-thumb {
        background: #b5dcce;
        border-radius: 9999px;
      }
      ::-webkit-scrollbar-thumb:hover {
        background: #003c33;
      }
    </style>
  </head>
  <body
    class="min-h-full font-sans text-txprimary antialiased bg-canvas selection:bg-[#9fe870] selection:text-[#003c33]"
    x-data="ghgApp()"
    x-init="initApp()"
  >
    <!-- TOP STICKY GOVERNMENT HEADER -->
    @include('forms.partials.header')

    <!-- MAIN BODY CONTENT -->
    @yield('content')

    <!-- MODALS -->
    @include('forms.partials.modals.delete-source-modal')
    @include('forms.partials.modals.confirm-submit-modal')

    <!-- TOAST -->
    @include('forms.partials.toast')

    <!-- APPLICATION LOGIC JS -->
    <script src="{{ asset('js/ghg-app.js') }}?v={{ filemtime(public_path('js/ghg-app.js')) }}"></script>
    <script>
      // Initialize icons on DOM ready
      document.addEventListener("DOMContentLoaded", function () {
        if (window.lucide) {
          lucide.createIcons();
        }
      });
    </script>
  </body>
</html>
