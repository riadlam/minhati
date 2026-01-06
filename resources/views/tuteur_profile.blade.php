@extends('layouts.main')

@section('title', 'معلوماتي الشخصية')

@section('content')
<div class="tuteur-page">
    <div class="tuteur-card">
        <div class="tuteur-card__header">
            <div>
                <h3 class="tuteur-card__title"><i class="fa-solid fa-user"></i>معلوماتي الشخصية</h3>
                <p class="tuteur-card__subtitle">عرض معلومات الحساب (قراءة فقط)</p>
            </div>
            <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">
                <i class="fa-solid fa-arrow-right"></i>عودة
            </a>
        </div>

        <div class="tuteur-card__body">
            <div class="tuteur-kv">
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">اللقب بالعربية</div><div class="tuteur-kv__v">{{ $tuteur->nom_ar ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">الاسم بالعربية</div><div class="tuteur-kv__v">{{ $tuteur->prenom_ar ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">اللقب باللاتينية</div><div class="tuteur-kv__v">{{ $tuteur->nom_fr ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">الاسم باللاتينية</div><div class="tuteur-kv__v">{{ $tuteur->prenom_fr ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">تاريخ الميلاد</div><div class="tuteur-kv__v">{{ $tuteur->date_naiss ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">العنوان</div><div class="tuteur-kv__v">{{ $tuteur->adresse ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">رقم الهاتف</div><div class="tuteur-kv__v">{{ $tuteur->tel ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">البريد الإلكتروني</div><div class="tuteur-kv__v">{{ $tuteur->email ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">رقم بطاقة التعريف الوطنية</div><div class="tuteur-kv__v">{{ $tuteur->num_cni ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">تاريخ إصدار البطاقة</div><div class="tuteur-kv__v">{{ $tuteur->date_cni ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">رقم الضمان الاجتماعي</div><div class="tuteur-kv__v">{{ $tuteur->nss ?? '—' }}</div></div>
                <div class="tuteur-kv__item"><div class="tuteur-kv__k">رقم الحساب البريدي</div><div class="tuteur-kv__v">{{ $tuteur->num_cpt ?? '—' }} - {{ $tuteur->cle_cpt ?? '—' }}</div></div>
            </div>
        </div>
    </div>
</div>
@endsection