@extends('layouts.main')

@section('title', 'لوحة التحكم - المستخدم')

@vite(['resources/css/dashboard.css'])

@section('content')
<div class="dashboard-container" dir="rtl">

    <!-- Commune name -->
    <div class="dashboard-center">
        <h1>بلدية: {{ session('user_commune') ?? 'غير محددة' }}</h1>
    </div>

    <!-- Welcome header -->
    <div class="dashboard-header">
        <h2>مرحباً، {{ session('user_name') }}</h2>
        <p>الوظيفة: {{ session('user_role') }}</p>
    </div>

    <!-- Logout -->
    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <button class="logout-btn" onclick="confirmLogout()">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>تسجيل الخروج</span>
    </button>

    <!-- Table Section -->
    <div class="children-table-section">
        <div class="table-header">
            <h3 id="table-title">قائمة الأوصياء/الأولياء</h3>
            <button id="back-btn" style="display:none;">◀ العودة</button>
        </div>
        <table class="children-table" id="main-table">
            <thead id="table-head">
                <tr>
                    <th>رقم التعريف الوطني</th>
                    <th>الاسم الكامل</th>
                    <th>الفئة الاجتماعية</th>
                    <th>عدد الأطفال</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach($tuteurs as $tuteur)
                    <tr class="tuteur-row" data-eleves='@json($tuteur->eleves)'>
                        <td>{{ $tuteur->nin }}</td>
                        <td>{{ $tuteur->nom_ar ?? $tuteur->nom_fr }} {{ $tuteur->prenom_ar ?? $tuteur->prenom_fr }}</td>
                        <td>{{ $tuteur->cats ?? '-' }}</td>
                        <td class="children-count clickable-cell">
                            {{ $tuteur->eleves->count() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'تأكيد تسجيل الخروج',
        text: "هل تريد فعلاً تسجيل الخروج؟",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، تسجيل الخروج',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        customClass: {
            popup: 'logout-popup',
            title: 'logout-title',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}

// 🔹 Table switch logic
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('table-body');
    const tableTitle = document.getElementById('table-title');
    const backBtn = document.getElementById('back-btn');
    const tableHead = document.getElementById('table-head');

    // Store original tuteurs HTML
    const tuteursHTML = tableBody.innerHTML;

    // Delegate click events for better performance
    tableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('clickable-cell')) {
            const row = e.target.closest('tr');
            const eleves = JSON.parse(row.dataset.eleves);
            const tuteurName = row.cells[1].innerText;

            // Change table title
            tableTitle.innerText = `تلاميذ الوصي/الولي: ${tuteurName}`;
            backBtn.style.display = 'inline-block';

            // Change table head
            tableHead.innerHTML = `
                <tr>
                    <th>الإجراءات</th>
                    <th>المستوى الدراسي</th>
                    <th>تاريخ الميلاد</th>
                    <th>الاسم الكامل</th>
                </tr>
            `;

            // Fill table body with children
            if (eleves.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4">لا يوجد أطفال لهذا الوصي/الولي</td></tr>';
                return;
            }

            tableBody.innerHTML = '';
            eleves.forEach(eleve => {
                tableBody.innerHTML += `
                    <tr>
                        <td><button onclick="alert('تعديل ${eleve.nom_complet ?? '-'}')">تعديل</button></td>
                        <td>${eleve.niveau ?? '-'}</td>
                        <td>${eleve.date_naissance ?? '-'}</td>
                        <td>${eleve.nom_complet ?? '-'}</td>
                    </tr>
                `;
            });
        }
    });

    // Back button logic
    backBtn.addEventListener('click', () => {
        tableTitle.innerText = 'قائمة الأوصياء/الأولياء';
        backBtn.style.display = 'none';
        tableHead.innerHTML = `
            <tr>
                <th>رقم التعريف الوطني</th>
                <th>الاسم الكامل</th>
                <th>الفئة الاجتماعية</th>
                <th>عدد الأطفال</th>
            </tr>
        `;
        tableBody.innerHTML = tuteursHTML;
    });
});
</script>

@endsection
