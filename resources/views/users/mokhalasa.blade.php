@extends('layouts.main')

@section('title', 'المخالصة — قائمة الأوصياء/الأولياء')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.logged-in-as-badge {
    position: sticky; top: 0; z-index: 100;
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.5rem 1rem; margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #f59e0b; border-radius: 8px;
    font-size: 0.9rem; font-weight: 600; color: #92400e;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
}
.end-impersonate-link { margin-right: auto; color: #b45309; text-decoration: underline; font-weight: 600; }
.mokhalasa-intro { margin-bottom: 1rem; color: #6b7280; font-size: 0.95rem; }
</style>
@endpush

@section('content')
<div class="dashboard-container" dir="rtl">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-header">
            <h3>القائمة</h3>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="{{ route('user.dashboard') }}" class="sidebar-link">
                        <i class="fa-solid fa-home"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.tuteurs.list') }}" class="sidebar-link">
                        <i class="fa-solid fa-users"></i>
                        <span>الأوصياء والأولياء</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.students.list') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>التلاميذ</span>
                    </a>
                </li>
                <li class="sidebar-item active">
                    <a href="{{ route('user.mokhalasa') }}" class="sidebar-link">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>المخالصة</span>
                    </a>
                </li>
                @if(!in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
                <li class="sidebar-item">
                    <a href="{{ route('user.add.student') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>إضافة تلميذ جديد</span>
                    </a>
                </li>
                @endif
                @if(!in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
                <li class="sidebar-item">
                    <a href="{{ route('user.pending.requests') }}" class="sidebar-link">
                        <i class="fa-solid fa-file-check"></i>
                        <span>الطلبات قيد التأكيد</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.approved.requests') }}" class="sidebar-link">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>الطلبات المؤكدة</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
        <div class="sidebar-footer">
            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <button class="sidebar-logout-btn" onclick="confirmLogout()">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>تسجيل الخروج</span>
            </button>
        </div>
    </aside>

    <div class="dashboard-main-content">
        @if(!empty($impersonating) && !empty($loggedInAsName))
        <div class="logged-in-as-badge" role="status">
            <i class="fa-solid fa-user-secret"></i>
            <span>تم الدخول باسم {{ $loggedInAsName }}</span>
            <a href="{{ route('user.impersonate.end') }}" class="end-impersonate-link">إنهاء العرض فقط</a>
        </div>
        @endif
        <div class="dashboard-content-wrapper">
            <div class="dashboard-header">
                <h2><i class="fa-solid fa-file-invoice-dollar"></i> المخالصة — قائمة الأوصياء/الأولياء</h2>
                <p class="mokhalasa-intro">أوصياء/أولياء لديهم تلاميذ مقبول نهائي (القرار النهائي = مقبول) ولم يُولّد لهم دفعة بعد. المبلغ المستحق = عدد التلاميذ × 5000 د.ج</p>
            </div>

            <div id="mokhalasa-loading" style="text-align:center; padding:2rem;">
                <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
                <p style="margin-top:0.75rem; color:#6b7280;">جارٍ تحميل القائمة...</p>
            </div>
            <div id="mokhalasa-content" style="display:none;">
                <div class="das-recent-card">
                    <div class="recent-table-wrap">
                        <table class="recent-table">
                            <thead>
                                <tr>
                                    <th>رقم الولي/الوصي (NIN)</th>
                                    <th>الاسم واللقب</th>
                                    <th>عدد التلاميذ</th>
                                    <th>المبلغ المستحق (د.ج)</th>
                                </tr>
                            </thead>
                            <tbody id="mokhalasaBody">
                                <tr><td colspan="4" style="text-align:center;color:#9ca3af;">لا توجد بيانات</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const getUrl = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);
    const loadingEl = document.getElementById('mokhalasa-loading');
    const contentEl = document.getElementById('mokhalasa-content');
    const tbody = document.getElementById('mokhalasaBody');

    fetch(getUrl('/api/user/dashboard-stats'), {
        credentials: 'include',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(json => {
        loadingEl.style.display = 'none';
        contentEl.style.display = 'block';
        const list = (json.success && json.data && Array.isArray(json.data.mokhalasa)) ? json.data.mokhalasa : [];
        if (list.length > 0) {
            tbody.innerHTML = list.map(m => `
                <tr>
                    <td style="font-family:monospace;">${m.nin || '—'}</td>
                    <td>${m.nom_prenom || '—'}</td>
                    <td>${Number(m.eleves_count || 0).toLocaleString('ar-DZ')}</td>
                    <td>${Number(m.montant_due || 0).toLocaleString('ar-DZ')}</td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#9ca3af;">لا توجد بيانات للمخالصة</td></tr>';
        }
    })
    .catch(err => {
        console.error('Mokhalasa load error:', err);
        loadingEl.style.display = 'none';
        contentEl.style.display = 'block';
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#ef4444;">تعذر تحميل القائمة</td></tr>';
    });
});
</script>
@endsection
