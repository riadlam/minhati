@extends('layouts.main')

@section('title', 'معلوماتي الشخصية')

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
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <h3>معلوماتي الشخصية</h3>

        <div class="profile-info row g-3">
            <div class="col-md-6">
                <label>اللقب بالعربية:</label>
                <p>{{ $tuteur->nom_ar }}</p>
            </div>
            <div class="col-md-6">
                <label>الاسم بالعربية:</label>
                <p>{{ $tuteur->prenom_ar }}</p>
            </div>

            <div class="col-md-6">
                <label>اللقب باللاتينية:</label>
                <p>{{ $tuteur->nom_fr }}</p>
            </div>
            <div class="col-md-6">
                <label>الاسم باللاتينية:</label>
                <p>{{ $tuteur->prenom_fr }}</p>
            </div>

            <div class="col-md-6">
                <label>تاريخ الميلاد:</label>
                <p>{{ $tuteur->date_naiss ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <label>العنوان:</label>
                <p>{{ $tuteur->adresse ?? '—' }}</p>
            </div>

            <div class="col-md-6">
                <label>رقم الهاتف:</label>
                <p>{{ $tuteur->tel ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <label>البريد الإلكتروني:</label>
                <p>{{ $tuteur->email ?? '—' }}</p>
            </div>

            <div class="col-md-6">
                <label>رقم بطاقة التعريف الوطنية:</label>
                <p>{{ $tuteur->num_cni ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <label>تاريخ إصدار البطاقة:</label>
                <p>{{ $tuteur->date_cni ?? '—' }}</p>
            </div>

            <div class="col-md-6">
                <label>رقم الضمان الاجتماعي :</label>
                <p>{{ $tuteur->nss ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <label>رقم الحساب البريدي:</label>
                <p>{{ $tuteur->num_cpt ?? '—' }} - {{ $tuteur->cle_cpt ?? '—' }}</p>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4">عودة إلى اللوحة</a>
        </div>
    </div>
</div>
@endsection