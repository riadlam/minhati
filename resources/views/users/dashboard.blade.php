@extends('layouts.main')

@section('title', 'لوحة التحكم - المستخدم')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
}
.bg-success {
    background-color: #10b981 !important;
    color: white;
}
.bg-warning {
    background-color: #f59e0b !important;
    color: white;
}
.bg-secondary {
    background-color: #6b7280 !important;
    color: white;
}

/* Logged-in-as badge (impersonation read-only mode) */
.logged-in-as-badge {
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #f59e0b;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #92400e;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
}
.logged-in-as-badge i { opacity: 0.9; }
.end-impersonate-link {
    margin-right: auto;
    color: #b45309;
    text-decoration: underline;
    font-weight: 600;
}
.end-impersonate-link:hover { color: #92400e; }

/* === Comment Modal Styles === */
.swal2-popup.swal-comment-modal {
    border-radius: 16px !important;
    max-width: 700px !important;
    padding: 0 !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    overflow: hidden;
}

.swal-comment-title {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%);
    color: white !important;
    padding: 1.5rem 2rem;
    margin: 0 !important;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    border-radius: 16px 16px 0 0;
    border-bottom: 3px solid #fdae4b;
}

.swal-comment-content {
    padding: 2rem !important;
    margin: 0 !important;
    text-align: right;
    background: white;
}

.swal-comment-content::-webkit-scrollbar {
    width: 10px;
}

.swal-comment-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}

.swal-comment-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    border-radius: 8px;
}

.swal-comment-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
}

.swal-comment-confirm {
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.75rem 2rem !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
    transition: all 0.3s ease !important;
}

.swal-comment-confirm:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4) !important;
}

.swal-comment-cancel {
    background: linear-gradient(135deg, #6b7280, #4b5563) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.75rem 2rem !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3) !important;
    transition: all 0.3s ease !important;
}

.swal-comment-cancel:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4) !important;
}

.comments-container::-webkit-scrollbar {
    width: 8px;
}

.comments-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}

.comments-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    border-radius: 8px;
}

.comments-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
}

.comment-card:hover {
    transform: translateX(-5px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
}

#commentText:focus {
    outline: none;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
}

.admin-create-user-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 28px rgba(15, 3, 58, 0.08);
}

.admin-card-header h3 {
    color: #0f033a;
    margin-bottom: 0.4rem;
    font-weight: 700;
}

.admin-card-header p {
    color: #4b5563;
    margin-bottom: 1rem;
}

.admin-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0.9rem;
}

.admin-form-actions {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-start;
}

/* ==============================
   DAS Statistics Dashboard
   ============================== */
.das-kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.75rem;
}
.das-kpi-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 4px 20px rgba(15, 3, 58, 0.06);
    border: 1px solid rgba(15, 3, 58, 0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    position: relative;
    overflow: hidden;
}
.das-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 4px;
    height: 100%;
    border-radius: 0 16px 16px 0;
}
.das-kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(15, 3, 58, 0.12);
}
.kpi-eleves::before { background: linear-gradient(180deg, #3b82f6, #1d4ed8); }
.kpi-tuteurs::before { background: linear-gradient(180deg, #10b981, #059669); }
.kpi-schools::before { background: linear-gradient(180deg, #f59e0b, #d97706); }
.kpi-communes::before { background: linear-gradient(180deg, #8b5cf6, #6d28d9); }

.kpi-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}
.kpi-eleves .kpi-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.kpi-tuteurs .kpi-icon { background: linear-gradient(135deg, #10b981, #059669); }
.kpi-schools .kpi-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.kpi-communes .kpi-icon { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

.kpi-body {
    display: flex;
    flex-direction: column;
}
.kpi-value {
    font-size: 2rem;
    font-weight: 800;
    color: #0f033a;
    line-height: 1.1;
}
.kpi-label {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 600;
    margin-top: 0.2rem;
}

/* Charts row */
.das-charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.75rem;
}
.das-charts-row-2 {
    grid-template-columns: 2fr 1fr;
}
.das-chart-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(15, 3, 58, 0.06);
    border: 1px solid rgba(15, 3, 58, 0.06);
}
.chart-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f033a;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.chart-title i {
    color: #fdae4b;
    font-size: 1.1rem;
}
.chart-wrapper {
    position: relative;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chart-wrapper-bar {
    height: 240px;
}
.chart-wrapper-bar-h {
    height: 280px;
}
.chart-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
    margin-top: 0.75rem;
}
.chart-legend-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.8rem;
    color: #374151;
    font-weight: 500;
}
.chart-legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    flex-shrink: 0;
}

/* Recent table */
.das-recent-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(15, 3, 58, 0.06);
    border: 1px solid rgba(15, 3, 58, 0.06);
    margin-bottom: 1.75rem;
}
.recent-table-wrap {
    overflow-x: auto;
}
.recent-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}
.recent-table thead {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%);
    color: white;
}
.recent-table thead th {
    padding: 0.75rem 0.6rem;
    text-align: center;
    font-weight: 600;
    font-size: 0.82rem;
    white-space: nowrap;
}
.recent-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
}
.recent-table tbody tr:hover {
    background: rgba(253, 174, 75, 0.06);
}
.recent-table tbody td {
    padding: 0.65rem 0.6rem;
    text-align: center;
    font-size: 0.82rem;
    color: #374151;
    white-space: nowrap;
}
.status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-badge.accepted { background: #dcfce7; color: #166534; }
.status-badge.refused { background: #fee2e2; color: #991b1b; }
.status-badge.pending { background: #fef3c7; color: #92400e; }

/* Quick nav */
.das-nav-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    margin-bottom: 1rem;
}
.das-nav-card {
    background: white;
    border-radius: 14px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none !important;
    color: #0f033a;
    font-weight: 700;
    font-size: 1rem;
    border: 2px solid transparent;
    box-shadow: 0 4px 16px rgba(15, 3, 58, 0.06);
    transition: all 0.3s ease;
}
.das-nav-card:hover {
    border-color: #fdae4b;
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(253, 174, 75, 0.18);
    color: #0f033a;
}
.das-nav-card i {
    font-size: 1.5rem;
    color: #fdae4b;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Responsive */
@media (max-width: 1100px) {
    .das-kpi-row { grid-template-columns: repeat(2, 1fr); }
    .das-charts-row { grid-template-columns: 1fr; }
    .das-charts-row-2 { grid-template-columns: 1fr; }
    .das-nav-cards { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .das-kpi-row { grid-template-columns: 1fr; }
}
.das-kpi-row.admin-kpi-row-2 {
    grid-template-columns: repeat(2, 1fr);
}
@media (max-width: 1100px) {
    .das-kpi-row.admin-kpi-row-2 { grid-template-columns: 1fr; }
}
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
                <li class="sidebar-item active">
                    <a href="{{ route('user.dashboard') }}" class="sidebar-link">
                        <i class="fa-solid fa-home"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                @if(session('user_role') === 'admin')
                    <li class="sidebar-item">
                        <a href="{{ route('user.users.list') }}" class="sidebar-link">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>المستخدمون</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('user.add.user') }}" class="sidebar-link">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>إضافة مستخدم جديد</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('user.admin.ts_commune.management') }}" class="sidebar-link">
                            <i class="fa-solid fa-building-user"></i>
                            <span>إدارة تقني البلدية</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('user.admin.das.management') }}" class="sidebar-link">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>إدارة DAS</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('user.admin.comite_wilaya.management') }}" class="sidebar-link">
                            <i class="fa-solid fa-landmark"></i>
                            <span>إدارة لجنة الولاية</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('user.admin.antr.management') }}" class="sidebar-link">
                            <i class="fa-solid fa-sitemap"></i>
                            <span>إدارة ATR</span>
                        </a>
                    </li>
                @endif
                @if(session('user_role') !== 'admin')
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
        <!-- Main Content Wrapper -->
        <div class="dashboard-content-wrapper">
    <!-- Welcome header -->
    <div class="dashboard-header">
        <h2 id="user-name">مرحباً، {{ session('user_name') ?? 'المستخدم' }}</h2>
        <p id="user-role">الوظيفة: @php
            $r = session('user_role');
            echo match($r) {
                'das' => 'DAS',
                'comite_wilaya' => 'اللجنة الولائية',
                'antr' => 'الفرع الجهوي',
                default => $r ?? '-',
            };
        @endphp</p>
        <p class="dashboard-header-commune" id="user-commune">
            @if(session('user_role') === 'antr')
                الفرع الجهوي: {{ $antenneName ?? 'غير محدد' }} — ولاية: {{ $wilayaName ?? session('user_wilaya') ?? 'غير محددة' }}
            @elseif(session('user_role') === 'das' || session('user_role') === 'comite_wilaya')
                ولاية: {{ $wilayaName ?? session('user_wilaya') ?? 'غير محددة' }}
            @else
                بلدية: {{ session('user_commune') ?? 'غير محددة' }}
            @endif
        </p>
    </div>

    @if(in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
    {{-- ========================= DAS / Comité Wilaya / ATR Statistics Dashboard ========================= --}}
    <div id="das-stats-loading" style="text-align:center; padding:3rem;">
        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
        <p style="margin-top:1rem; color:#6b7280; font-weight:600;">جارٍ تحميل الإحصائيات...</p>
    </div>
    <div id="das-stats-container" style="display:none;">

    {{-- Row 1: KPI summary cards --}}
    <div class="das-kpi-row">
        <div class="das-kpi-card kpi-eleves">
            <div class="kpi-icon"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="kpi-body">
                <span class="kpi-value" id="kpi-eleves">0</span>
                <span class="kpi-label">التلاميذ</span>
            </div>
        </div>
        <div class="das-kpi-card kpi-tuteurs">
            <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-body">
                <span class="kpi-value" id="kpi-tuteurs">0</span>
                <span class="kpi-label">الأولياء / الأوصياء</span>
            </div>
        </div>
        <div class="das-kpi-card kpi-schools">
            <div class="kpi-icon"><i class="fa-solid fa-school"></i></div>
            <div class="kpi-body">
                <span class="kpi-value" id="kpi-schools">0</span>
                <span class="kpi-label">المؤسسات التعليمية</span>
            </div>
        </div>
        <div class="das-kpi-card kpi-communes">
            <div class="kpi-icon"><i class="fa-solid fa-city"></i></div>
            <div class="kpi-body">
                <span class="kpi-value" id="kpi-communes">0</span>
                <span class="kpi-label">البلديات</span>
            </div>
        </div>
    </div>

    {{-- Row 2: Primary status pie + Gender pie + Education bar --}}
    <div class="das-charts-row">
        <div class="das-chart-card">
            <h4 class="chart-title" id="titlePrimaryStatus"><i class="fa-solid fa-gavel"></i> حالة الملفات</h4>
            <div class="chart-wrapper"><canvas id="chartPrimaryStatus"></canvas></div>
            <div class="chart-legend" id="legendPrimaryStatus"></div>
        </div>
        <div class="das-chart-card">
            <h4 class="chart-title"><i class="fa-solid fa-venus-mars"></i> التوزيع حسب الجنس</h4>
            <div class="chart-wrapper"><canvas id="chartGender"></canvas></div>
            <div class="chart-legend" id="legendGender"></div>
        </div>
        <div class="das-chart-card">
            <h4 class="chart-title"><i class="fa-solid fa-layer-group"></i> المستوى الدراسي</h4>
            <div class="chart-wrapper chart-wrapper-bar"><canvas id="chartLevels"></canvas></div>
        </div>
    </div>

    {{-- Row 2b (comite_wilaya only): DAS decisions breakdown --}}
    <div class="das-charts-row" id="rowDasDecisions" style="display:none;">
        <div class="das-chart-card">
            <h4 class="chart-title"><i class="fa-solid fa-scale-balanced"></i> قرارات DAS</h4>
            <div class="chart-wrapper"><canvas id="chartDasDecisions"></canvas></div>
            <div class="chart-legend" id="legendDasDecisions"></div>
        </div>
        <div class="das-chart-card das-chart-wide" style="grid-column: span 2;">
            <h4 class="chart-title"><i class="fa-solid fa-chart-column"></i> مقارنة القرارات: DAS مقابل اللجنة الولائية</h4>
            <div class="chart-wrapper chart-wrapper-bar"><canvas id="chartCompare"></canvas></div>
        </div>
    </div>

    {{-- Row 2c (antr only): Final decision + Wilaya breakdown + Pipeline comparison --}}
    <div class="das-charts-row" id="rowAntrDecisions" style="display:none;">
        <div class="das-chart-card">
            <h4 class="chart-title"><i class="fa-solid fa-flag-checkered"></i> القرار النهائي</h4>
            <div class="chart-wrapper"><canvas id="chartFinalStatus"></canvas></div>
            <div class="chart-legend" id="legendFinalStatus"></div>
        </div>
        <div class="das-chart-card das-chart-wide" style="grid-column: span 2;">
            <h4 class="chart-title"><i class="fa-solid fa-chart-column"></i> التلاميذ حسب الولاية</h4>
            <div class="chart-wrapper chart-wrapper-bar"><canvas id="chartWilayas"></canvas></div>
        </div>
    </div>

    {{-- Row 3: Communes bar chart + Relation tuteur pie --}}
    <div class="das-charts-row das-charts-row-2">
        <div class="das-chart-card das-chart-wide">
            <h4 class="chart-title"><i class="fa-solid fa-map-location-dot"></i> التلاميذ حسب البلدية</h4>
            <div class="chart-wrapper chart-wrapper-bar-h"><canvas id="chartCommunes"></canvas></div>
        </div>
        <div class="das-chart-card">
            <h4 class="chart-title"><i class="fa-solid fa-handshake"></i> نوع الكفالة</h4>
            <div class="chart-wrapper"><canvas id="chartRelation"></canvas></div>
            <div class="chart-legend" id="legendRelation"></div>
        </div>
    </div>

    {{-- Row 4: Recent students table --}}
    <div class="das-recent-card">
        <h4 class="chart-title"><i class="fa-solid fa-clock-rotate-left"></i> آخر التلاميذ المسجلين</h4>
        <div class="recent-table-wrap">
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>رقم التلميذ</th>
                        <th>الاسم الكامل</th>
                        <th>الجنس</th>
                        <th>المستوى</th>
                        <th>المؤسسة</th>
                        <th>الولي</th>
                        <th id="thStatusDas">حالة DAS</th>
                        <th id="thStatusComite" style="display:none;">حالة اللجنة</th>
                        <th id="thStatusFinal" style="display:none;">القرار النهائي</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody id="recentElevesBody">
                    <tr><td colspan="8" style="text-align:center;color:#9ca3af;">لا توجد بيانات</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick navigation --}}
    <div class="das-nav-cards">
        <a href="{{ route('user.tuteurs.list') }}" class="das-nav-card">
            <i class="fa-solid fa-users"></i>
            <span>الأوصياء والأولياء</span>
        </a>
        <a href="{{ route('user.students.list') }}" class="das-nav-card">
            <i class="fa-solid fa-user-graduate"></i>
            <span>التلاميذ</span>
        </a>
    </div>

    </div>{{-- /das-stats-container --}}
    @elseif(session('user_role') === 'admin')
    {{-- ========================= Admin Statistics Dashboard (same layout as DAS/Comité/ATR) ========================= --}}
    <div id="admin-stats-loading" style="text-align:center; padding:3rem;">
        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
        <p style="margin-top:1rem; color:#6b7280; font-weight:600;">جارٍ تحميل الإحصائيات...</p>
    </div>
    <div id="admin-stats-container" style="display:none;">
        <div class="das-kpi-row">
            <div class="das-kpi-card kpi-eleves">
                <div class="kpi-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="kpi-body">
                    <span class="kpi-value" id="admin-kpi-eleves">0</span>
                    <span class="kpi-label">التلاميذ</span>
                </div>
            </div>
            <div class="das-kpi-card kpi-tuteurs">
                <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
                <div class="kpi-body">
                    <span class="kpi-value" id="admin-kpi-tuteurs">0</span>
                    <span class="kpi-label">الأولياء / الأوصياء</span>
                </div>
            </div>
            <div class="das-kpi-card kpi-schools">
                <div class="kpi-icon"><i class="fa-solid fa-school"></i></div>
                <div class="kpi-body">
                    <span class="kpi-value" id="admin-kpi-schools">0</span>
                    <span class="kpi-label">المؤسسات التعليمية</span>
                </div>
            </div>
            <div class="das-kpi-card kpi-communes">
                <div class="kpi-icon"><i class="fa-solid fa-users-gear"></i></div>
                <div class="kpi-body">
                    <span class="kpi-value" id="admin-kpi-users">0</span>
                    <span class="kpi-label">المستخدمون</span>
                </div>
            </div>
        </div>
        <div class="das-kpi-row admin-kpi-row-2">
            <div class="das-kpi-card kpi-eleves">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);"><i class="fa-solid fa-map"></i></div>
                <div class="kpi-body">
                    <span class="kpi-value" id="admin-kpi-wilayas">0</span>
                    <span class="kpi-label">الولايات</span>
                </div>
            </div>
            <div class="das-kpi-card kpi-tuteurs">
                <div class="kpi-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);"><i class="fa-solid fa-city"></i></div>
                <div class="kpi-body">
                    <span class="kpi-value" id="admin-kpi-communes">0</span>
                    <span class="kpi-label">البلديات</span>
                </div>
            </div>
        </div>
        <div class="das-charts-row">
            <div class="das-chart-card" style="grid-column: span 1;">
                <h4 class="chart-title"><i class="fa-solid fa-chart-pie"></i> المستخدمون حسب الرتبة</h4>
                <div class="chart-wrapper"><canvas id="admin-chart-roles"></canvas></div>
                <div class="chart-legend" id="admin-legend-roles"></div>
            </div>
        </div>
        <div class="das-nav-cards">
            <a href="{{ route('user.users.list') }}" class="das-nav-card">
                <i class="fa-solid fa-users-gear"></i>
                <span>قائمة المستخدمين</span>
            </a>
            <a href="{{ route('user.add.user') }}" class="das-nav-card">
                <i class="fa-solid fa-user-plus"></i>
                <span>إضافة مستخدم جديد</span>
            </a>
            <a href="{{ route('user.admin.ts_commune.management') }}" class="das-nav-card">
                <i class="fa-solid fa-building-user"></i>
                <span>إدارة تقني البلدية</span>
            </a>
            <a href="{{ route('user.admin.das.management') }}" class="das-nav-card">
                <i class="fa-solid fa-user-tie"></i>
                <span>إدارة DAS</span>
            </a>
            <a href="{{ route('user.admin.comite_wilaya.management') }}" class="das-nav-card">
                <i class="fa-solid fa-landmark"></i>
                <span>إدارة لجنة الولاية</span>
            </a>
            <a href="{{ route('user.admin.antr.management') }}" class="das-nav-card">
                <i class="fa-solid fa-sitemap"></i>
                <span>إدارة ATR</span>
            </a>
        </div>
    </div>
    @else
    <!-- Action Cards Section -->
    <div class="dashboard-actions-grid">
        <a href="{{ route('user.tuteurs.list') }}" class="dashboard-action-card">
            <div class="action-card-icon primary">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="action-card-content">
                <h3>الأوصياء والأولياء</h3>
                <p>عرض وإدارة جميع الأوصياء والأولياء المسجلين</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="{{ route('user.students.list') }}" class="dashboard-action-card">
            <div class="action-card-icon info">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div class="action-card-content">
                <h3>التلاميذ</h3>
                <p>عرض وإدارة جميع التلاميذ المسجلين</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        @if(!in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
        <a href="{{ route('user.pending.requests') }}" class="dashboard-action-card">
            <div class="action-card-icon warning">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="action-card-content">
                <h3>الطلبات قيد التأكيد</h3>
                <p>مراجعة الطلبات التي في انتظار الموافقة</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        @endif
    </div>
    @endif

    </div>
    </div>
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

document.addEventListener('DOMContentLoaded', () => {
    const adminCreateUserForm = document.getElementById('adminCreateUserForm');
    // Admin create form only exists on add-user page, not on dashboard
    if (adminCreateUserForm) {

    let API_TOKEN = @json(session('api_token'));
    if (!API_TOKEN && typeof localStorage !== 'undefined') API_TOKEN = localStorage.getItem('api_token');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const getUrl = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);
    const roleSelect = document.getElementById('adminRoleSelect');
    const wilayaWrapper = document.getElementById('adminWilayaWrapper');
    const communeWrapper = document.getElementById('adminCommuneWrapper');
    const codeWilayaSelect = document.getElementById('adminCodeWilaya');
    const codeCommSelect = document.getElementById('adminCodeComm');

    const setRoleVisibility = () => {
        const role = roleSelect ? roleSelect.value : '';

        if (role === 'ts_commune') {
            if (wilayaWrapper) wilayaWrapper.style.display = 'block';
            if (communeWrapper) communeWrapper.style.display = 'block';
            if (codeWilayaSelect) codeWilayaSelect.required = true;
            if (codeCommSelect) codeCommSelect.required = true;
        } else if (role === 'das' || role === 'comite_wilaya' || role === 'antr') {
            if (wilayaWrapper) wilayaWrapper.style.display = 'block';
            if (communeWrapper) communeWrapper.style.display = 'none';
            if (codeWilayaSelect) codeWilayaSelect.required = true;
            if (codeCommSelect) {
                codeCommSelect.required = false;
                codeCommSelect.value = '';
            }
        } else {
            if (wilayaWrapper) wilayaWrapper.style.display = 'none';
            if (communeWrapper) communeWrapper.style.display = 'none';
            if (codeWilayaSelect) {
                codeWilayaSelect.required = false;
                codeWilayaSelect.value = '';
            }
            if (codeCommSelect) {
                codeCommSelect.required = false;
                codeCommSelect.value = '';
            }
        }
    };

    const loadWilayas = async () => {
        if (!codeWilayaSelect) return;
        try {
            const response = await fetch(getUrl('/api/wilayas'), {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json().catch(() => []);
            const wilayas = Array.isArray(data) ? data : (data.data || []);

            codeWilayaSelect.innerHTML = '<option value="">اختر الولاية...</option>';
            wilayas.forEach((w) => {
                const option = document.createElement('option');
                option.value = w.code_wil || w.id || '';
                option.textContent = w.lib_wil_ar || w.nom_wilaya || w.code_wil || 'ولاية';
                codeWilayaSelect.appendChild(option);
            });
        } catch (_) {
            codeWilayaSelect.innerHTML = '<option value="">تعذر تحميل الولايات</option>';
        }
    };

    const loadCommunesByWilaya = async (wilayaCode) => {
        if (!codeCommSelect) return;
        if (!wilayaCode) {
            codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
            codeCommSelect.disabled = true;
            return;
        }
        try {
            const response = await fetch(getUrl(`/api/communes/by-wilaya/${encodeURIComponent(wilayaCode)}`), {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json().catch(() => []);
            const communes = Array.isArray(data) ? data : (data.data || []);

            codeCommSelect.innerHTML = '<option value="">اختر البلدية...</option>';
            communes.forEach((c) => {
                const option = document.createElement('option');
                option.value = c.code_comm || c.id || '';
                option.textContent = c.lib_comm_ar || c.nom_commune || c.code_comm || 'بلدية';
                codeCommSelect.appendChild(option);
            });
            codeCommSelect.disabled = false;
        } catch (_) {
            codeCommSelect.innerHTML = '<option value="">تعذر تحميل البلديات</option>';
            codeCommSelect.disabled = true;
        }
    };

    const checkCodeUserExists = async (codeUser) => {
        try {
            const response = await fetch(getUrl(`/api/users/${encodeURIComponent(codeUser)}`), {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            return response.ok;
        } catch (_) {
            return false;
        }
    };

    if (roleSelect) {
        roleSelect.addEventListener('change', () => {
            setRoleVisibility();
            if (codeWilayaSelect && codeWilayaSelect.value) {
                loadCommunesByWilaya(codeWilayaSelect.value);
            }
        });
    }

    if (codeWilayaSelect) {
        codeWilayaSelect.addEventListener('change', () => {
            loadCommunesByWilaya(codeWilayaSelect.value);
        });
    }

    loadWilayas();
    setRoleVisibility();
    if (codeCommSelect) {
        codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
        codeCommSelect.disabled = true;
    }

    adminCreateUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(adminCreateUserForm);
        const payload = Object.fromEntries(formData.entries());
        payload.code_user = (payload.code_user || '').trim();

        const roleValue = payload.role || '';
        if (roleValue === 'ts_commune') {
            if (!payload.code_wilaya || !payload.code_comm) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار الولاية والبلدية.' });
                return;
            }
        } else if (roleValue === 'das' || roleValue === 'comite_wilaya' || roleValue === 'antr') {
            if (!payload.code_wilaya) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار الولاية.' });
                return;
            }
            payload.code_comm = null;
        } else {
            payload.code_comm = null;
            payload.code_wilaya = null;
        }

        if (!/^\d{18}$/.test(payload.code_user || '')) {
            Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'رقم المستخدم يجب أن يكون 18 رقمًا.' });
            return;
        }

        const exists = await checkCodeUserExists(payload.code_user);
        if (exists) {
            Swal.fire({
                icon: 'error',
                title: 'موجود مسبقًا',
                text: 'رقم المستخدم موجود مسبقًا، يرجى إدخال رقم آخر.',
                confirmButtonText: 'حسنًا'
            });
            return;
        }

        try {
            Swal.fire({
                title: 'جارٍ إنشاء المستخدم...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            };

            if (API_TOKEN) {
                headers['Authorization'] = `Bearer ${API_TOKEN}`;
            }

            const response = await fetch(getUrl('/api/admin/users'), {
                method: 'POST',
                headers,
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                let message = data.message || 'فشل إنشاء المستخدم';
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (Array.isArray(firstError) && firstError.length > 0) {
                        message = firstError[0];
                    }
                }
                throw new Error(message);
            }

            await Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: data.message || 'تم إنشاء المستخدم بنجاح',
                confirmButtonText: 'حسنًا'
            });

            adminCreateUserForm.reset();
            if (roleSelect) roleSelect.value = 'ts_commune';
            setRoleVisibility();
            if (codeCommSelect) {
                codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
                codeCommSelect.disabled = true;
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: error.message || 'حدث خطأ أثناء إنشاء المستخدم',
                confirmButtonText: 'حسنًا'
            });
        }
    });
    }
});

// Comment eleve - Enhanced with rich styling (same as tuteurs_list)
async function commentEleve(num_scolaire) {
    // Show loading
    Swal.fire({
        title: 'جارٍ التحميل...',
        html: '<div class="spinner-border text-primary" role="status"></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => { Swal.showLoading(); }
    });

    // First, get existing comments
    let existingComments = [];
    try {
        const response = await fetch(getUrl(`/api/user/eleves/${num_scolaire}/comments`), {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            existingComments = data.comments || [];
        }
    } catch (error) {
        console.error('Error loading comments:', error);
    }

    // Build HTML for existing comments with rich styling
    let commentsHTML = '';
    if (existingComments.length > 0) {
        commentsHTML = `
            <div class="comments-container" style="max-height: 400px; overflow-y: auto; margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; border: 1px solid rgba(15, 3, 58, 0.1);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 3px solid #fdae4b;">
                    <i class="fa-solid fa-comments" style="color: #fdae4b; font-size: 1.25rem;"></i>
                    <h6 style="margin: 0; color: #0f033a; font-weight: 700; font-size: 1.1rem;">التعليقات السابقة (${existingComments.length})</h6>
                </div>
        `;
        
        existingComments.forEach((comment, index) => {
            const dateObj = new Date(comment.created_at);
            const date = dateObj.toLocaleDateString('ar-DZ', { year: 'numeric', month: 'long', day: 'numeric' });
            const time = dateObj.toLocaleTimeString('ar-DZ', { hour: '2-digit', minute: '2-digit' });
            
            commentsHTML += `
                <div class="comment-card" style="background: white; padding: 1.25rem; margin-bottom: 1rem; border-radius: 12px; border-right: 4px solid #2563eb; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; right: 0; width: 100%; height: 3px; background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);"></div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #3b82f6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);">
                                <i class="fa-solid fa-user"></i>
                    </div>
                            <div>
                                <strong style="color: #0f033a; font-size: 0.95rem; font-weight: 700; display: block; margin-bottom: 0.25rem;">
                                    ${(comment.user && comment.user.nom_user) ? comment.user.nom_user + ' ' + (comment.user.prenom_user || '') : 'مستخدم'}
                                </strong>
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: #6b7280; font-size: 0.8rem;">
                                    <i class="fa-solid fa-calendar" style="color: #9ca3af;"></i>
                                    <span>${date}</span>
                                    <span style="margin: 0 0.25rem;">•</span>
                                    <i class="fa-solid fa-clock" style="color: #9ca3af;"></i>
                                    <span>${time}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border-right: 2px solid #e5e7eb;">
                        <p style="margin: 0; color: #374151; line-height: 1.8; font-size: 0.95rem; white-space: pre-wrap; word-break: break-word;">${comment.text}</p>
                    </div>
                </div>
            `;
        });
        commentsHTML += '</div>';
    } else {
        commentsHTML = `
            <div class="empty-comments" style="text-align: center; padding: 3rem 2rem; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 12px; margin-bottom: 2rem; border: 2px dashed #3b82f6;">
                <i class="fa-solid fa-comment-slash" style="font-size: 3rem; color: #3b82f6; margin-bottom: 1rem; opacity: 0.6; display: block;"></i>
                <div style="color: #1e40af; font-weight: 600; font-size: 1rem;">لا توجد تعليقات سابقة</div>
            </div>
        `;
    }

    const result = await Swal.fire({
        title: '<div style="display: flex; align-items: center; gap: 0.75rem; justify-content: center;"><i class="fa-solid fa-comments" style="color: #fdae4b;"></i><span>التعليقات</span></div>',
        html: `
            <div style="direction: rtl; text-align: right;">
            ${commentsHTML}
                <div class="new-comment-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 3px solid #fdae4b;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <i class="fa-solid fa-plus-circle" style="color: #2563eb; font-size: 1.25rem;"></i>
                        <label style="color: #0f033a; font-weight: 700; font-size: 1.1rem; margin: 0;">إضافة تعليق جديد</label>
                    </div>
                    <div style="position: relative;">
                        <textarea id="commentText" rows="5" style="width: 100%; padding: 1rem 1.25rem; border: 2px solid #e5e7eb; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical; transition: all 0.3s ease; background: white; color: #374151; line-height: 1.6;" placeholder="اكتب تعليقك هنا... (الحد الأقصى 1000 حرف)" oninput="updateCommentCounter()"></textarea>
                        <div id="commentCounter" style="position: absolute; bottom: 0.75rem; left: 1rem; color: #9ca3af; font-size: 0.85rem; background: white; padding: 0.25rem 0.5rem; border-radius: 4px;">0 / 1000</div>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-paper-plane"></i> إضافة التعليق',
        cancelButtonText: '<i class="fa-solid fa-times"></i> إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        width: '700px',
        customClass: {
            popup: 'swal-comment-modal',
            title: 'swal-comment-title',
            htmlContainer: 'swal-comment-content',
            confirmButton: 'swal-comment-confirm',
            cancelButton: 'swal-comment-cancel'
        },
        didOpen: () => {
            // Add scrollbar styling to comments container
            const commentsContainer = document.querySelector('.comments-container');
            if (commentsContainer) {
                commentsContainer.style.scrollbarWidth = 'thin';
                commentsContainer.style.scrollbarColor = '#2563eb #f1f5f9';
            }
            
            // Focus textarea
            const textarea = document.getElementById('commentText');
            if (textarea) {
                textarea.focus();
                textarea.addEventListener('input', function() {
                    if (this.value.length > 1000) {
                        this.value = this.value.substring(0, 1000);
                    }
                    updateCommentCounter();
                });
            }
        },
        preConfirm: async () => {
            const text = document.getElementById('commentText').value.trim();
            if (!text) {
                Swal.showValidationMessage('<i class="fa-solid fa-exclamation-circle"></i> يرجى إدخال نص التعليق');
                return false;
            }
            if (text.length > 1000) {
                Swal.showValidationMessage('<i class="fa-solid fa-exclamation-circle"></i> التعليق طويل جداً (الحد الأقصى 1000 حرف)');
                return false;
            }
            return text;
        }
    });
    
        if (result.isConfirmed && result.value) {
        // Show loading
        Swal.fire({
            title: 'جارٍ الإضافة...',
            html: '<div class="spinner-border text-primary" role="status"></div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

            try {
                const response = await fetch(getUrl(`/api/user/eleves/${num_scolaire}/comments`), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ text: result.value })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                    title: '<div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;"><i class="fa-solid fa-check-circle"></i><span>تمت الإضافة</span></div>',
                    html: '<div style="color: #059669; font-weight: 600;">تمت إضافة التعليق بنجاح</div>',
                        confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#10b981',
                    timer: 2000,
                    timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                    title: '<div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;"><i class="fa-solid fa-exclamation-triangle"></i><span>خطأ</span></div>',
                        text: data.message || 'فشلت إضافة التعليق',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                title: '<div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;"><i class="fa-solid fa-exclamation-triangle"></i><span>خطأ</span></div>',
                    text: 'حدث خطأ أثناء إضافة التعليق',
                confirmButtonText: 'حسنًا',
                confirmButtonColor: '#ef4444'
                });
            }
        }
}

// Update comment counter
function updateCommentCounter() {
    const textarea = document.getElementById('commentText');
    const counter = document.getElementById('commentCounter');
    if (textarea && counter) {
        const length = textarea.value.length;
        counter.textContent = `${length} / 1000`;
        if (length > 900) {
            counter.style.color = '#ef4444';
            counter.style.fontWeight = '700';
        } else if (length > 700) {
            counter.style.color = '#f59e0b';
            counter.style.fontWeight = '600';
        } else {
            counter.style.color = '#9ca3af';
            counter.style.fontWeight = '400';
        }
                    }
                }

// ======================== DAS Dashboard Charts ========================
@if(in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
(function() {
    const getUrl = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);

    function animateNumber(el, target) {
        const duration = 800;
        const start = 0;
        const startTime = performance.now();
        const step = (now) => {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(start + (target - start) * eased).toLocaleString('ar-DZ');
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    function makeLegend(containerId, items) {
        const el = document.getElementById(containerId);
        if (!el) return;
        el.innerHTML = items.map(i => `<span class="chart-legend-item"><span class="chart-legend-dot" style="background:${i.color}"></span>${i.label}: <b>${i.value}</b></span>`).join('');
    }

    function statusBadge(status) {
        if (status === 'accepte') return '<span class="status-badge accepted">مقبول</span>';
        if (status === 'refuse') return '<span class="status-badge refused">مرفوض</span>';
        return '<span class="status-badge pending">قيد الدراسة</span>';
    }

    async function loadDasStats() {
        try {
            const res = await fetch(getUrl('/api/user/dashboard-stats'), {
                credentials: 'include',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            });
            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'Error');
            const d = json.data;

            document.getElementById('das-stats-loading').style.display = 'none';
            document.getElementById('das-stats-container').style.display = 'block';

            // KPIs
            animateNumber(document.getElementById('kpi-eleves'), d.totals.eleves);
            animateNumber(document.getElementById('kpi-tuteurs'), d.totals.tuteurs);
            animateNumber(document.getElementById('kpi-schools'), d.totals.schools);
            animateNumber(document.getElementById('kpi-communes'), d.totals.communes);

            // Common chart options
            const pieFontFamily = "'Cairo', sans-serif";
            Chart.defaults.font.family = pieFontFamily;
            const isComite = d.role === 'comite_wilaya';
            const isAntr = d.role === 'antr';

            // Chart 1: Primary status doughnut
            let primaryData, primaryTitle;
            if (isAntr) {
                primaryData = d.final_status;
                primaryTitle = 'القرار النهائي (الفرع الجهوي)';
            } else if (isComite) {
                primaryData = d.comite_status;
                primaryTitle = 'حالة الملفات (اللجنة الولائية)';
            } else {
                primaryData = d.das_status;
                primaryTitle = 'حالة الملفات (DAS)';
            }
            const titleEl = document.getElementById('titlePrimaryStatus');
            if (titleEl) titleEl.innerHTML = `<i class="fa-solid fa-gavel"></i> ${primaryTitle}`;

            const statusColors = ['#10b981', '#ef4444', '#f59e0b'];
            new Chart(document.getElementById('chartPrimaryStatus'), {
                type: 'doughnut',
                data: {
                    labels: ['مقبول', 'مرفوض', 'قيد الدراسة'],
                    datasets: [{
                        data: [primaryData.accepte, primaryData.refuse, primaryData.pending],
                        backgroundColor: statusColors,
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            rtl: true,
                            textDirection: 'rtl',
                            callbacks: { label: (c) => `${c.label}: ${c.raw}` }
                        }
                    }
                }
            });
            makeLegend('legendPrimaryStatus', [
                { color: statusColors[0], label: 'مقبول', value: primaryData.accepte },
                { color: statusColors[1], label: 'مرفوض', value: primaryData.refuse },
                { color: statusColors[2], label: 'قيد الدراسة', value: primaryData.pending },
            ]);

            // ATR extra charts: Final decision doughnut + Wilaya breakdown bar
            if (isAntr) {
                document.getElementById('rowAntrDecisions').style.display = 'grid';
                // Show etat_final column in recent table
                const thFinal = document.getElementById('thStatusFinal');
                if (thFinal) thFinal.style.display = '';

                // Final status doughnut
                const finalColors = ['#10b981', '#ef4444', '#f59e0b'];
                new Chart(document.getElementById('chartFinalStatus'), {
                    type: 'doughnut',
                    data: {
                        labels: ['مقبول نهائي', 'مرفوض نهائي', 'قيد الدراسة'],
                        datasets: [{
                            data: [d.final_status.accepte, d.final_status.refuse, d.final_status.pending],
                            backgroundColor: finalColors,
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: { legend: { display: false }, tooltip: { rtl: true, textDirection: 'rtl' } }
                    }
                });
                makeLegend('legendFinalStatus', [
                    { color: finalColors[0], label: 'مقبول نهائي', value: d.final_status.accepte },
                    { color: finalColors[1], label: 'مرفوض نهائي', value: d.final_status.refuse },
                    { color: finalColors[2], label: 'قيد الدراسة', value: d.final_status.pending },
                ]);

                // Wilaya breakdown bar chart
                if (d.wilayas && d.wilayas.length > 0) {
                    const wLabels = d.wilayas.map(w => w.wilaya_name || w.code_wil);
                    const wValues = d.wilayas.map(w => w.cnt);
                    const wPalette = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#84cc16','#f97316','#6366f1'];
                    new Chart(document.getElementById('chartWilayas'), {
                        type: 'bar',
                        data: {
                            labels: wLabels,
                            datasets: [{
                                label: 'عدد التلاميذ',
                                data: wValues,
                                backgroundColor: wLabels.map((_, i) => wPalette[i % wPalette.length]),
                                borderRadius: 8,
                                barThickness: 40
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f3f4f6' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }

            // Comité wilaya extra charts: DAS decisions + comparison
            if (isComite) {
                document.getElementById('rowDasDecisions').style.display = 'grid';
                // Show both status columns in table
                const thDas = document.getElementById('thStatusDas');
                const thComite = document.getElementById('thStatusComite');
                if (thDas) thDas.style.display = '';
                if (thComite) thComite.style.display = '';

                // DAS decisions doughnut
                const dasColors2 = ['#0ea5e9', '#f97316', '#94a3b8'];
                new Chart(document.getElementById('chartDasDecisions'), {
                    type: 'doughnut',
                    data: {
                        labels: ['مقبول DAS', 'مرفوض DAS', 'قيد الدراسة'],
                        datasets: [{
                            data: [d.das_status.accepte, d.das_status.refuse, d.das_status.pending],
                            backgroundColor: dasColors2,
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: { legend: { display: false }, tooltip: { rtl: true, textDirection: 'rtl' } }
                    }
                });
                makeLegend('legendDasDecisions', [
                    { color: dasColors2[0], label: 'مقبول DAS', value: d.das_status.accepte },
                    { color: dasColors2[1], label: 'مرفوض DAS', value: d.das_status.refuse },
                    { color: dasColors2[2], label: 'قيد الدراسة', value: d.das_status.pending },
                ]);

                // Grouped bar: DAS vs Comité comparison
                new Chart(document.getElementById('chartCompare'), {
                    type: 'bar',
                    data: {
                        labels: ['مقبول', 'مرفوض', 'قيد الدراسة'],
                        datasets: [
                            {
                                label: 'DAS',
                                data: [d.das_status.accepte, d.das_status.refuse, d.das_status.pending],
                                backgroundColor: ['#0ea5e9', '#0ea5e9', '#0ea5e9'],
                                borderRadius: 6,
                                barThickness: 32
                            },
                            {
                                label: 'اللجنة الولائية',
                                data: [d.comite_status.accepte, d.comite_status.refuse, d.comite_status.pending],
                                backgroundColor: ['#8b5cf6', '#8b5cf6', '#8b5cf6'],
                                borderRadius: 6,
                                barThickness: 32
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                rtl: true,
                                labels: { font: { weight: '600' }, padding: 16 }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f3f4f6' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Chart 2: Gender doughnut
            const genderColors = ['#3b82f6', '#ec4899'];
            new Chart(document.getElementById('chartGender'), {
                type: 'doughnut',
                data: {
                    labels: ['ذكور', 'إناث'],
                    datasets: [{
                        data: [d.gender.male, d.gender.female],
                        backgroundColor: genderColors,
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { rtl: true, textDirection: 'rtl' }
                    }
                }
            });
            makeLegend('legendGender', [
                { color: genderColors[0], label: 'ذكور', value: d.gender.male },
                { color: genderColors[1], label: 'إناث', value: d.gender.female },
            ]);

            // Chart 3: Education levels bar
            const lvlLabels = Object.keys(d.education_levels);
            const lvlValues = Object.values(d.education_levels);
            const lvlColors = ['#6366f1', '#f59e0b', '#10b981', '#8b5cf6'];
            new Chart(document.getElementById('chartLevels'), {
                type: 'bar',
                data: {
                    labels: lvlLabels,
                    datasets: [{
                        label: 'عدد التلاميذ',
                        data: lvlValues,
                        backgroundColor: lvlLabels.map((_, i) => lvlColors[i % lvlColors.length]),
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Chart 4: Communes horizontal bar
            const commLabels = d.communes.map(c => c.commune_name || c.code_comm);
            const commValues = d.communes.map(c => c.cnt);
            new Chart(document.getElementById('chartCommunes'), {
                type: 'bar',
                data: {
                    labels: commLabels,
                    datasets: [{
                        label: 'عدد التلاميذ',
                        data: commValues,
                        backgroundColor: commLabels.map((_, i) => {
                            const palette = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#84cc16','#f97316','#6366f1','#14b8a6','#a855f7','#eab308','#0ea5e9','#e11d48'];
                            return palette[i % palette.length];
                        }),
                        borderRadius: 6,
                        barThickness: 24
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f3f4f6' } },
                        y: { grid: { display: false } }
                    }
                }
            });

            // Chart 5: Relation tuteur doughnut (wali vs wasi)
            const relColors = ['#0ea5e9', '#fdae4b'];
            new Chart(document.getElementById('chartRelation'), {
                type: 'doughnut',
                data: {
                    labels: ['ولي', 'وصي'],
                    datasets: [{
                        data: [d.relation_tuteur.wali, d.relation_tuteur.wasi],
                        backgroundColor: relColors,
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: { legend: { display: false }, tooltip: { rtl: true, textDirection: 'rtl' } }
                }
            });
            makeLegend('legendRelation', [
                { color: relColors[0], label: 'ولي', value: d.relation_tuteur.wali },
                { color: relColors[1], label: 'وصي', value: d.relation_tuteur.wasi },
            ]);

            // Recent eleves table
            const tbody = document.getElementById('recentElevesBody');
            if (d.recent_eleves && d.recent_eleves.length > 0) {
                tbody.innerHTML = d.recent_eleves.map(e => {
                    const comiteCol = isComite ? `<td>${statusBadge(e.etat_comite_wilaya)}</td>` : '';
                    const finalCol = isAntr ? `<td>${statusBadge(e.etat_final)}</td>` : '';
                    return `<tr>
                    <td style="font-family:monospace; font-size:0.78rem;">${e.num_scolaire}</td>
                    <td>${e.nom || ''} ${e.prenom || ''}</td>
                    <td>${e.sexe || '—'}</td>
                    <td>${e.niv_scol || '—'}</td>
                    <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;">${e.etablissement}</td>
                    <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;">${e.tuteur_nom || '—'}</td>
                    <td>${statusBadge(e.etat_das)}</td>
                    ${comiteCol}
                    ${finalCol}
                    <td style="font-size:0.78rem;">${e.date_insertion ? new Date(e.date_insertion).toLocaleDateString('ar-DZ') : '—'}</td>
                </tr>`;
                }).join('');
            }

        } catch (err) {
            console.error('DAS stats load error:', err);
            const loadEl = document.getElementById('das-stats-loading');
            if (loadEl) loadEl.innerHTML = '<p style="color:#ef4444; font-weight:600; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i> تعذر تحميل الإحصائيات</p>';
        }
    }

    // Load Chart.js then fetch stats
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
        script.onload = loadDasStats;
        document.head.appendChild(script);
    } else {
        loadDasStats();
    }
})();
@endif

// ======================== Admin Dashboard Stats ========================
@if(session('user_role') === 'admin')
(function() {
    const getUrl = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);
    function animateNumber(el, target) {
        if (!el) return;
        const duration = 800;
        const start = 0;
        const startTime = performance.now();
        const step = (now) => {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(start + (target - start) * eased).toLocaleString('ar-DZ');
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }
    async function loadAdminStats() {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let API_TOKEN = @json(session('api_token'));
            if (!API_TOKEN && typeof localStorage !== 'undefined') API_TOKEN = localStorage.getItem('api_token');
            const headers = { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf };
            if (API_TOKEN) headers['Authorization'] = 'Bearer ' + API_TOKEN;
            const res = await fetch(getUrl('/api/admin/dashboard-stats'), { credentials: 'same-origin', headers });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.message || 'Error');
            const d = json.data;
            document.getElementById('admin-stats-loading').style.display = 'none';
            document.getElementById('admin-stats-container').style.display = 'block';
            animateNumber(document.getElementById('admin-kpi-eleves'), d.eleves_total);
            animateNumber(document.getElementById('admin-kpi-tuteurs'), d.tuteurs_total);
            animateNumber(document.getElementById('admin-kpi-schools'), d.schools_total);
            animateNumber(document.getElementById('admin-kpi-users'), d.users_total);
            animateNumber(document.getElementById('admin-kpi-wilayas'), d.wilayas_count);
            animateNumber(document.getElementById('admin-kpi-communes'), d.communes_count);
            const roleData = d.users_by_role || {};
            const labels = [];
            const values = [];
            const colors = ['#0f033a', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'];
            ['admin', 'ts_commune', 'das', 'comite_wilaya', 'antr'].forEach((role, i) => {
                const r = roleData[role];
                if (r) { labels.push(r.label); values.push(r.count); }
            });
            const legendEl = document.getElementById('admin-legend-roles');
            if (legendEl) {
                let idx = 0;
                legendEl.innerHTML = ['admin', 'ts_commune', 'das', 'comite_wilaya', 'antr'].map((role) => {
                    const r = roleData[role];
                    if (!r) return '';
                    const color = colors[idx % colors.length];
                    idx++;
                    return `<span class="chart-legend-item"><span class="chart-legend-dot" style="background:${color}"></span>${r.label}: <b>${r.count}</b></span>`;
                }).filter(Boolean).join('');
            }
            if (typeof Chart !== 'undefined' && labels.length > 0) {
                new Chart(document.getElementById('admin-chart-roles'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: labels.map((_, i) => colors[i % colors.length]),
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { rtl: true, textDirection: 'rtl', callbacks: { label: (c) => `${c.label}: ${c.raw}` } }
                        }
                    }
                });
            }
        } catch (err) {
            console.error('Admin stats error', err);
            const el = document.getElementById('admin-stats-loading');
            if (el) el.innerHTML = '<p style="color:#ef4444; font-weight:600; text-align:center;"><i class="fa-solid fa-circle-exclamation"></i> تعذر تحميل الإحصائيات</p>';
        }
    }
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
        script.onload = loadAdminStats;
        document.head.appendChild(script);
    } else {
        loadAdminStats();
    }
})();
@endif
</script>

@endsection
