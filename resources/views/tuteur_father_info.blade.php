@extends('layouts.main')

@section('title', 'معلومات الأب')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.profile-container {
    direction: rtl;
    text-align: right;
    background: #f9f9fb;
    min-height: 100vh;
    padding: 40px;
}

.profile-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    padding: 30px;
    max-width: 900px;
    margin: auto;
}

.profile-card h3 {
    color: #0f033a;
    font-weight: bold;
    border-bottom: 2px solid #fdae4b;
    padding-bottom: 10px;
    margin-bottom: 30px;
}

.profile-info label {
    font-weight: bold;
    color: #0f033a;
}

.profile-info p {
    background: #f6f8fa;
    border-radius: 8px;
    padding: 8px 12px;
    margin-bottom: 0;
}

.btn-edit {
    background-color: #fdae4b;
    color: #0f033a;
    font-weight: bold;
    border: none;
    padding: 10px 30px;
    border-radius: 8px;
}

.btn-edit:hover {
    background-color: #f5a742;
    color: #0f033a;
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <h3><i class="fa-solid fa-mars me-2"></i>معلومات الأب</h3>

        @if($father)
            <div class="profile-info row g-3">
                <div class="col-md-6">
                    <label>الرقم الوطني (NIN):</label>
                    <p>{{ $father->nin ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <label>رقم الضمان الاجتماعي (NSS):</label>
                    <p>{{ $father->nss ?? '—' }}</p>
                </div>

                <div class="col-md-6">
                    <label>لقب الأب بالعربية:</label>
                    <p>{{ $father->nom_ar ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <label>اسم الأب بالعربية:</label>
                    <p>{{ $father->prenom_ar ?? '—' }}</p>
                </div>

                <div class="col-md-6">
                    <label>لقب الأب بالفرنسية:</label>
                    <p>{{ $father->nom_fr ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <label>اسم الأب بالفرنسية:</label>
                    <p>{{ $father->prenom_fr ?? '—' }}</p>
                </div>

                <div class="col-md-6">
                    <label>الفئة الاجتماعية:</label>
                    <p>{{ $father->categorie_sociale ?? 'غير محدد' }}</p>
                </div>
                <div class="col-md-6">
                    <label>مبلغ الدخل الشهري:</label>
                    <p>{{ $father->montant_s ? number_format($father->montant_s, 2) . ' دج' : 'غير محدد' }}</p>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="button" class="btn btn-edit me-2" onclick="editFather()">
                    <i class="fa-solid fa-edit me-2"></i>تعديل المعلومات
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4">عودة إلى اللوحة</a>
            </div>
        @else
            <div class="alert alert-info text-center">
                <p class="mb-3">لا توجد معلومات الأب مسجلة</p>
                <button type="button" class="btn btn-edit" onclick="addFather()">
                    <i class="fa-solid fa-plus me-2"></i>إضافة معلومات الأب
                </button>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4">عودة إلى اللوحة</a>
            </div>
        @endif
    </div>
</div>

<!-- Edit Form Modal (will be added via JavaScript) -->
<div id="fatherFormModal" style="display: none;"></div>

<script>
function editFather() {
    // Redirect to dashboard with edit mode (or implement inline edit)
    window.location.href = '{{ route("dashboard") }}?edit_father=1';
}

function addFather() {
    // Redirect to dashboard with add mode (or implement inline add)
    window.location.href = '{{ route("dashboard") }}?add_father=1';
}
</script>
@endsection

