@extends('layouts.app')

@section('title', 'Bảng Cung Cấp Số Liệu Kiểm Kê Khí Nhà Kính và Kế Hoạch Giảm Nhẹ - Sở Công Thương TP.HCM')

@section('content')
  <!-- SUCCESS RECEIPT VIEW (Shown after final submission) -->
  @include('forms.partials.success-receipt')

  <!-- MAIN TWO-COLUMN APPLICATION CONTAINER (Form active state) -->
  <main x-show="!isSubmitted" class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
      
      <!-- LEFT SIDEBAR: DESKTOP STEPPER -->
      @include('forms.partials.progress-stepper')

      <!-- RIGHT FORM CONTAINER -->
      <section class="lg:col-span-8 xl:col-span-9 max-w-4xl space-y-6">
        
        <!-- INTRODUCTION NOTICE -->
        @include('forms.partials.intro-card')

        <!-- MAIN STEPPING FORM -->
        <form @submit.prevent="nextStep()" novalidate class="space-y-6">
          
          <!-- STEP 1: THÔNG TIN DOANH NGHIỆP -->
          @include('forms.partials.step-1-company')

          <!-- STEP 2: KỲ BÁO CÁO -->
          @include('forms.partials.step-2-reporting-period')

          <!-- STEP 3: PHÁT THẢI TRỰC TIẾP (SCOPE 1) -->
          @include('forms.partials.step-3-scope-1')

          <!-- STEP 4: PHÁT THẢI GIÁN TIẾP (SCOPE 2) -->
          @include('forms.partials.step-4-scope-2')

          <!-- STEP 5: KẾT QUẢ KIỂM KÊ -->
          @include('forms.partials.step-5-inventory-results')

          <!-- STEP 6: KẾ HOẠCH GIẢM NHẸ -->
          @include('forms.partials.step-6-mitigation')

          <!-- STEP 7: XÁC NHẬN VÀ KÝ NỘP -->
          @include('forms.partials.step-7-review')

          <!-- BOTTOM STICKY NAVIGATION -->
          @include('forms.partials.step-navigation')

        </form>
      </section>
    </div>
  </main>
@endsection
