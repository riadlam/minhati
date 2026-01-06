@extends('layouts.main')

@section('title', 'معلومات الأم')

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

.mother-card {
    background: #f6f8fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
}

.mother-card h5 {
    color: #0f033a;
    font-weight: bold;
    margin-bottom: 15px;
    border-bottom: 1px solid #fdae4b;
    padding-bottom: 10px;
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-card">
        @if($tuteur->relation_tuteur == 1)
            <h3><i class="fa-solid fa-venus me-2"></i>معلومات الأمهات</h3>
            
            @if($mothers && $mothers->count() > 0)
                @foreach($mothers as $mother)
                    <div class="mother-card">
                        <h5>الأم {{ $loop->iteration }}</h5>
                        <div class="profile-info row g-3">
                            <div class="col-md-6">
                                <label>الرقم الوطني (NIN):</label>
                                <p>{{ $mother->nin ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>رقم الضمان الاجتماعي (NSS):</label>
                                <p>{{ $mother->nss ?? '—' }}</p>
                            </div>

                            <div class="col-md-6">
                                <label>لقب الأم بالعربية:</label>
                                <p>{{ $mother->nom_ar ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>اسم الأم بالعربية:</label>
                                <p>{{ $mother->prenom_ar ?? '—' }}</p>
                            </div>

                            <div class="col-md-6">
                                <label>لقب الأم بالفرنسية:</label>
                                <p>{{ $mother->nom_fr ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>اسم الأم بالفرنسية:</label>
                                <p>{{ $mother->prenom_fr ?? '—' }}</p>
                            </div>

                            <div class="col-md-6">
                                <label>الفئة الاجتماعية:</label>
                                <p>{{ $mother->categorie_sociale ?? 'غير محدد' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>مبلغ الدخل الشهري:</label>
                                <p>{{ $mother->montant_s ? number_format($mother->montant_s, 2) . ' دج' : 'غير محدد' }}</p>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-sm btn-primary me-2" onclick="editMother({{ $mother->id }})">
                                <i class="fa-solid fa-edit me-1"></i>تعديل
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteMother({{ $mother->id }}, '{{ $mother->nom_ar }} {{ $mother->prenom_ar }}')">
                                <i class="fa-solid fa-trash me-1"></i>حذف
                            </button>
                        </div>
                    </div>
                @endforeach
                
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-edit me-2" onclick="addMother()">
                        <i class="fa-solid fa-plus me-2"></i>إضافة أم جديدة
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4">عودة إلى اللوحة</a>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <p class="mb-3">لا توجد أمهات مسجلة</p>
                    <button type="button" class="btn btn-edit" onclick="addMother()">
                        <i class="fa-solid fa-plus me-2"></i>إضافة أم جديدة
                    </button>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4">عودة إلى اللوحة</a>
                </div>
            @endif
        @else
            <h3><i class="fa-solid fa-venus me-2"></i>معلومات الأم</h3>
            
            @if($mother)
                <div class="profile-info row g-3">
                    <div class="col-md-6">
                        <label>الرقم الوطني (NIN):</label>
                        <p>{{ $mother->nin ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>رقم الضمان الاجتماعي (NSS):</label>
                        <p>{{ $mother->nss ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label>لقب الأم بالعربية:</label>
                        <p>{{ $mother->nom_ar ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>اسم الأم بالعربية:</label>
                        <p>{{ $mother->prenom_ar ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label>لقب الأم بالفرنسية:</label>
                        <p>{{ $mother->nom_fr ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>اسم الأم بالفرنسية:</label>
                        <p>{{ $mother->prenom_fr ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label>الفئة الاجتماعية:</label>
                        <p>{{ $mother->categorie_sociale ?? 'غير محدد' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>مبلغ الدخل الشهري:</label>
                        <p>{{ $mother->montant_s ? number_format($mother->montant_s, 2) . ' دج' : 'غير محدد' }}</p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-edit me-2" onclick="editMother({{ $mother->id }})">
                        <i class="fa-solid fa-edit me-2"></i>تعديل المعلومات
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4">عودة إلى اللوحة</a>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <p class="mb-3">لا توجد معلومات أم مسجلة</p>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4">عودة إلى اللوحة</a>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
function editMother(id) {
    window.location.href = '{{ route("dashboard") }}?edit_mother=' + id;
}

function deleteMother(id, name) {
    if (confirm('هل أنت متأكد من حذف ' + name + '؟')) {
        // Implement delete via API
        fetch(`/api/mothers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Authorization': 'Bearer ' + localStorage.getItem('api_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || response.ok) {
                alert('تم الحذف بنجاح');
                location.reload();
            } else {
                alert('حدث خطأ أثناء الحذف');
            }
        })
        .catch(error => {
            alert('حدث خطأ أثناء الحذف');
        });
    }
}

function addMother() {
    window.location.href = '{{ route("dashboard") }}?add_mother=1';
}
</script>
@endsection

