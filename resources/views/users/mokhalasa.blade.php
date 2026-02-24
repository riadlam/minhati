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
.filters-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}
.filters-row .filter-group { display: flex; flex-direction: column; gap: 0.5rem; min-width: 0; }
.filters-row .filter-group label { color: #374151; font-weight: 600; font-size: 0.9rem; margin: 0; }
.filters-row .filter-group.wilaya-filter { min-width: 200px; }
.filters-row .filter-group.search-filter { flex: 1; min-width: 220px; }
.filters-row .filter-group .filter-control {
    padding: 0.5rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.95rem;
    background: white;
    width: 100%;
    min-width: 0;
}
.filters-row .filter-group .filter-control:focus {
    outline: none;
    border-color: #fdae4b;
    box-shadow: 0 0 0 3px rgba(253, 174, 75, 0.2);
}
.filters-row .filter-actions { flex-shrink: 0; margin-right: auto; }
/* Tuteur modal (same as tuteurs_list) */
.swal2-popup.swal-tuteur-modal {
    border-radius: 16px !important;
    max-width: 90% !important;
    padding: 0 !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    overflow: hidden;
}
.swal2-popup.swal-tuteur-modal .swal2-title {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%);
    color: white !important;
    padding: 1.5rem 2rem;
    margin: 0 !important;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: right;
    border-radius: 16px 16px 0 0;
    border-bottom: 3px solid #fdae4b;
}
.swal2-popup.swal-tuteur-modal .swal2-html-container {
    padding: 2rem !important;
    margin: 0 !important;
    text-align: right;
    max-height: 65vh;
    overflow-y: auto;
    background: white;
}
.tuteur-info-section {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}
.tuteur-info-section h6 {
    color: #0f033a;
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid #fdae4b;
}
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }
.info-item {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    border-right: 4px solid #fdae4b;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.info-item strong { color: #64748b; font-size: 0.85rem; display: block; margin-bottom: 0.25rem; }
.info-item p { color: #0f1419; font-size: 1rem; margin: 0; }
.expand-toggle-container { text-align: center; margin-top: 1rem; }
.expand-toggle-btn {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    cursor: pointer;
}
.eleves-section { background: white; padding: 1.5rem; border-radius: 12px; }
.eleves-section h6 { color: #0f033a; font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem; }
.eleves-table { width: 100%; border-collapse: collapse; background: white; }
.eleves-table thead { background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%); color: white; }
.eleves-table thead th { padding: 0.75rem; text-align: center; font-size: 0.9rem; }
.eleves-table tbody td { padding: 0.75rem; text-align: center; border-bottom: 1px solid #e2e8f0; }
.empty-state { padding: 1.5rem; text-align: center; color: #64748b; }
/* Action button: same as tuteurs_list (uses .children-table .btn-info from dashboard.css) */
.children-table .btn-view-mokhalasa {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    white-space: nowrap;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.children-table .btn-view-mokhalasa:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}
</style>
@endpush

@section('content')
<div class="dashboard-container" dir="rtl">
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
            <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1;">
                    <h2><i class="fa-solid fa-file-invoice-dollar"></i> المخالصة — قائمة الأوصياء/الأولياء</h2>
                    <p class="mokhalasa-intro">أوصياء/أولياء لديهم تلاميذ مقبول نهائي (القرار النهائي = مقبول) ولم يُولّد لهم دفعة بعد. المبلغ المستحق = عدد التلاميذ × 5000 د.ج</p>
                </div>
                <button type="button" id="btnGenerateMokhalasa" style="background: linear-gradient(135deg, #0f033a, #1a0f4a); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; color: white; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(15,3,58,0.3); white-space: nowrap;">
                    <i class="fa-solid fa-file-export"></i>
                    <span>إنشاء ملف المخالصة</span>
                </button>
            </div>

            <div class="children-table-section">
                <div class="filters-row">
                    <div class="filter-group wilaya-filter">
                        <label for="wilayaFilter">الولاية:</label>
                        <select id="wilayaFilter" class="filter-control">
                            <option value="">جميع ولايات المنطقة</option>
                            @foreach($wilayas ?? [] as $w)
                                <option value="{{ $w->code_wil }}">{{ $w->lib_wil_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group search-filter">
                        <label for="ninSearch">البحث بـ NIN أو الاسم:</label>
                        <input type="text" id="ninSearch" class="filter-control" placeholder="ابحث برقم التعريف أو الاسم...">
                    </div>
                    <div class="filter-actions">
                        <button id="clearFilters" type="button" style="padding: 0.5rem 1.5rem; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: none;">
                            <i class="fa-solid fa-times"></i> مسح الفلاتر
                        </button>
                    </div>
                </div>

                <div class="children-table-wrapper">
                    <table class="children-table" id="main-table">
                        <thead>
                            <tr>
                                <th>رقم الولي/الوصي (NIN)</th>
                                <th>الاسم واللقب</th>
                                <th>عدد التلاميذ</th>
                                <th>المبلغ المستحق (د.ج)</th>
                                <th style="min-width: 120px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px;">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <span class="visually-hidden">جارٍ التحميل...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="pagination-container" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 1.5rem; padding: 1rem;"></div>
            </div>
        </div>
    </div>
</div>

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
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('logout-form').submit();
    });
}

const getApiUrlPath = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);
let currentPage = 1;
let currentWilaya = '';
let currentNinSearch = '';
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('table-body');
    const wilayaFilter = document.getElementById('wilayaFilter');
    const ninSearch = document.getElementById('ninSearch');
    const clearFilters = document.getElementById('clearFilters');
    const paginationContainer = document.getElementById('pagination-container');

    function updateClearButton() {
        clearFilters.style.display = (currentWilaya || currentNinSearch) ? 'block' : 'none';
    }

    if (wilayaFilter) {
        wilayaFilter.addEventListener('change', function() {
            currentWilaya = this.value;
            updateClearButton();
            loadMokhalasa(1);
        });
    }

    ninSearch.addEventListener('input', function() {
        currentNinSearch = this.value.trim();
        updateClearButton();
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadMokhalasa(1), 400);
    });

    clearFilters.addEventListener('click', function() {
        currentWilaya = '';
        currentNinSearch = '';
        if (wilayaFilter) wilayaFilter.value = '';
        ninSearch.value = '';
        updateClearButton();
        loadMokhalasa(1);
    });

    function renderPagination(total, lastPage, page) {
        if (lastPage <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        let html = '';
        if (page > 1) {
            html += '<button type="button" class="btn btn-sm btn-outline-primary" data-page="' + (page - 1) + '"><i class="fa-solid fa-chevron-right"></i></button>';
        }
        html += '<span style="padding: 0 1rem; font-weight: 600;">صفحة ' + page + ' من ' + lastPage + ' (' + total + ' عنصر)</span>';
        if (page < lastPage) {
            html += '<button type="button" class="btn btn-sm btn-outline-primary" data-page="' + (page + 1) + '"><i class="fa-solid fa-chevron-left"></i></button>';
        }
        paginationContainer.innerHTML = html;
        paginationContainer.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', function() {
                loadMokhalasa(parseInt(this.getAttribute('data-page'), 10));
            });
        });
    }

    async function loadMokhalasa(page) {
        tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;"><div class="spinner-border text-primary"></div></td></tr>';
        try {
            const url = new URL(getApiUrlPath('/api/user/mokhalasa-list'));
            url.searchParams.set('page', page);
            if (currentWilaya) url.searchParams.set('wilaya', currentWilaya);
            if (currentNinSearch) url.searchParams.set('nin_search', currentNinSearch);
            const res = await fetch(url, {
                credentials: 'include',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '',
                    'Accept': 'application/json'
                }
            });
            const result = await res.json();
            console.log('[Mokhalasa] API response', {
                status: res.status,
                ok: res.ok,
                success: result.success,
                dataLength: (result.data || []).length,
                total: result.total,
                current_page: result.current_page,
                last_page: result.last_page,
                debug: result.debug,
                message: result.message
            });
            if (!result.success) {
                console.warn('[Mokhalasa] API returned success=false', result.message);
                tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: red;">حدث خطأ أثناء تحميل البيانات</td></tr>';
                return;
            }
            const list = result.data || [];
            currentPage = result.current_page || 1;
            const lastPage = result.last_page || 1;
            const total = result.total || 0;

            if (list.length === 0) {
                console.warn('[Mokhalasa] Empty list', result.debug || 'no debug');
                tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">لا توجد بيانات للمخالصة</td></tr>';
                renderPagination(0, 1, 1);
                return;
            }

            const escapeAttr = (s) => (s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const escapeJs = (s) => (s || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            tableBody.innerHTML = list.map(m => {
                const nin = (m.nin || '').toString();
                const ninSafe = escapeJs(nin);
                const cnt = Number(m.eleves_count || 0);
                const montant = Number(m.montant_due || 0);
                return '<tr>' +
                    '<td style="font-family: monospace;">' + (escapeAttr(nin) || '—') + '</td>' +
                    '<td>' + (escapeAttr(m.nom_prenom) || '—') + '</td>' +
                    '<td>' + cnt.toLocaleString('ar-DZ') + '</td>' +
                    '<td>' + montant.toLocaleString('ar-DZ') + '</td>' +
                    '<td><button type="button" class="btn-view-mokhalasa" onclick="viewTuteur(\'' + ninSafe + '\')" title="عرض التفاصيل"><i class="fa-solid fa-eye"></i> عرض</button></td>' +
                    '</tr>';
            }).join('');
            renderPagination(total, lastPage, currentPage);
        } catch (err) {
            console.error('Mokhalasa load error:', err);
            tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: red;">تعذر تحميل القائمة</td></tr>';
        }
    }

    window.viewTuteur = async function(nin) {
        Swal.fire({
            title: 'جارٍ التحميل...',
            html: '<div class="spinner-border text-primary"></div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        try {
            const response = await fetch(getApiUrlPath('/api/user/tuteurs/' + encodeURIComponent(nin)), {
                credentials: 'include',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!data.success || !data.tuteur) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل تحميل البيانات', confirmButtonText: 'حسنًا' });
                return;
            }
            const t = data.tuteur;
            const eleves = t.eleves || [];
            let communeName = '-';
            if (t.commune_residence && t.commune_residence.lib_comm_ar) communeName = t.commune_residence.lib_comm_ar;
            else if (t.communeResidence && t.communeResidence.lib_comm_ar) communeName = t.communeResidence.lib_comm_ar;

            const ccp = (t.num_cpt != null && t.num_cpt !== '') ? String(t.num_cpt) : '';
            const cle = (t.cle_cpt != null && t.cle_cpt !== '') ? String(t.cle_cpt) : '';
            const ccpCle = (ccp || cle) ? (ccp + (ccp && cle ? ' — ' : '') + (cle ? 'Clé: ' + cle : '')) : '-';

            let html = '<div class="tuteur-details-modal" style="direction: rtl;">';
            html += '<div class="tuteur-info-section"><h6><i class="fa-solid fa-user-circle" style="color: #fdae4b;"></i> معلومات الوصي/الولي</h6>';
            html += '<div class="info-grid">';
            html += '<div class="info-item"><strong>الاسم الكامل</strong><p>' + (((t.prenom_ar || t.prenom_fr || '') + ' ' + (t.nom_ar || t.nom_fr || '')).trim() || '-') + '</p></div>';
            html += '<div class="info-item"><strong>رقم التعريف الوطني (NIN)</strong><p>' + (t.nin || '-') + '</p></div>';
            html += '<div class="info-item"><strong>تاريخ الميلاد</strong><p>' + (t.date_naiss || '-') + '</p></div>';
            html += '<div class="info-item"><strong>البلدية</strong><p>' + communeName + '</p></div>';
            html += '<div class="info-item"><strong>الفئة الاجتماعية</strong><p>' + (t.cats || '-') + '</p></div>';
            html += '<div class="info-item"><strong>الحساب البريدي (CCP)</strong><p>' + ccpCle + '</p></div>';
            html += '</div></div>';
            html += '<div class="eleves-section"><h6><i class="fa-solid fa-graduation-cap" style="color: #fdae4b;"></i> التلاميذ (' + eleves.length + ')</h6>';
            if (eleves.length === 0) {
                html += '<div class="empty-state">لا يوجد تلاميذ مسجلين</div>';
            } else {
                html += '<div class="table-responsive"><table class="eleves-table"><thead><tr><th>رقم التعريف المدرسي</th><th>الاسم الكامل</th><th>تاريخ الميلاد</th><th>المستوى</th><th>المؤسسة</th></tr></thead><tbody>';
                eleves.forEach(e => {
                    const nomE = ((e.prenom || '') + ' ' + (e.nom || '')).trim() || '-';
                    const dateNaiss = (e.date_naiss != null && e.date_naiss !== '') ? String(e.date_naiss) : '—';
                    const niveau = (e.niv_scol != null && e.niv_scol !== '') ? String(e.niv_scol) : ((e.classe_scol != null && e.classe_scol !== '') ? String(e.classe_scol) : '—');
                    const etabObj = e.etablissement;
                    const etab = (etabObj && (etabObj.nom_etabliss != null && etabObj.nom_etabliss !== '')) ? String(etabObj.nom_etabliss) : (e.etablissement_nom != null && e.etablissement_nom !== '' ? String(e.etablissement_nom) : '—');
                    html += '<tr><td>' + (e.num_scolaire != null ? String(e.num_scolaire) : '—') + '</td><td>' + nomE + '</td><td>' + dateNaiss + '</td><td>' + niveau + '</td><td>' + etab + '</td></tr>';
                });
                html += '</tbody></table></div>';
            }
            html += '</div></div>';

            Swal.fire({
                title: 'تفاصيل الوصي/الولي',
                html: html,
                width: '90%',
                maxWidth: '1000px',
                showCloseButton: true,
                showConfirmButton: true,
                confirmButtonText: 'إغلاق',
                confirmButtonColor: '#0f033a',
                customClass: { popup: 'swal-tuteur-modal', htmlContainer: 'swal-tuteur-content' },
                didOpen: () => {
                    const content = document.querySelector('.swal-tuteur-content');
                    if (content) { content.style.maxHeight = '70vh'; content.style.overflowY = 'auto'; }
                }
            });
        } catch (error) {
            console.error('View tuteur error:', error);
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ أثناء تحميل البيانات', confirmButtonText: 'حسنًا' });
        }
    };

    loadMokhalasa(1);

    document.getElementById('btnGenerateMokhalasa').addEventListener('click', async function() {
        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> جاري الإنشاء...';
        try {
            const url = getApiUrlPath('/api/user/generate-mokhalasa-file');
            const res = await fetch(url, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json, text/plain'
                }
            });
            const contentType = res.headers.get('Content-Type') || '';
            if (contentType.indexOf('application/json') !== -1) {
                const json = await res.json();
                if (!json.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'خطأ', text: json.message || 'فشل إنشاء الملف', confirmButtonText: 'حسنًا' });
                    } else {
                        alert(json.message || 'فشل إنشاء الملف');
                    }
                    return;
                }
            }
            if (!res.ok) {
                const text = await res.text();
                let msg = 'فشل إنشاء الملف';
                try {
                    const j = JSON.parse(text);
                    if (j.message) msg = j.message;
                } catch (_) {}
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: msg, confirmButtonText: 'حسنًا' });
                } else {
                    alert(msg);
                }
                return;
            }
            const blob = await res.blob();
            const disp = res.headers.get('Content-Disposition');
            let filename = 'PrimeScol_CCP_Mokhalasa.txt';
            if (disp && disp.indexOf('filename=') !== -1) {
                const m = disp.match(/filename="?([^";\n]+)"?/);
                if (m) filename = m[1].trim();
            }
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(a.href);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'تم', text: 'تم إنشاء ملف المخالصة وتنزيله.', confirmButtonText: 'حسنًا' });
            }
            loadMokhalasa(1);
        } catch (err) {
            console.error('Generate mokhalasa error:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر إنشاء الملف', confirmButtonText: 'حسنًا' });
            } else {
                alert('تعذر إنشاء الملف');
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
});
</script>
@endsection
