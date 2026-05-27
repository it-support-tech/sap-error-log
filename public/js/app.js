document.addEventListener('DOMContentLoaded', () => {
    initAnimations();
    initToasts();
    initModals();
});

function initAnimations() {
    const els = document.querySelectorAll('[class*="stagger-"]');
    els.forEach(el => el.classList.add('animate-fade-in-up'));
}

function initToasts() {
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(t => {
        setTimeout(() => {
            t.style.animation = 'slideInRight 0.3s ease reverse forwards';
            setTimeout(() => t.remove(), 300);
        }, 3500);
    });
}

function initModals() {
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal-overlay');
            if (modal) {
                modal.style.animation = 'fadeIn 0.2s ease reverse forwards';
                setTimeout(() => modal.remove(), 200);
            }
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) {
                overlay.style.animation = 'fadeIn 0.2s ease reverse forwards';
                setTimeout(() => overlay.remove(), 200);
            }
        });
    });
}

function showToast(message, type = 'success') {
    const existing = document.querySelectorAll('.toast');
    existing.forEach(t => t.remove());

    const icon = type === 'success'
        ? '<svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        : '<svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>';

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `${icon}<span>${message}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideInRight 0.3s ease reverse forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

function openDetailModal(id) {
    fetch(`/api/error-detail.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) { showToast(data.error, 'error'); return; }
            renderDetailModal(data);
        })
        .catch(() => showToast('ໂຫລດຂໍ້ມູນບໍ່ສຳເລັດ', 'error'));
}

function renderDetailModal(e) {
    const statusBadge = e.status === 'resolved'
        ? '<span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>ແກ້ໄຂແລ້ວ</span>'
        : '<span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>ກຳລັງດຳເນີນການ</span>';

    const videoBlock = e.video_url
        ? `<div class="mt-2.5"><a href="${e.video_url}" target="_blank" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-700 text-xs font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z"/></svg>
            ເບິ່ງວິດີໂອວິທີແກ້ໄຂ
           </a></div>`
        : '';

    const imageBlock = e.image_path
        ? `<div class="mt-4"><p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">ຮູບໜ້າ Error</p><img src="/uploads/screenshots/${e.image_path}" alt="Error screenshot" class="rounded-xl max-h-64 w-auto border border-gray-200 cursor-pointer shadow-sm hover:shadow transition-shadow" onclick="window.open(this.src,'_blank')"></div>`
        : '';

    const html = `
    <div class="modal-overlay fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50" id="detailModal">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-xl w-full max-w-xl overflow-hidden animate-scale-in">
            <div class="p-6">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-mono text-gray-400">#${String(e.id).padStart(4,'0')}</span>
                            ${statusBadge}
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background:${e.module_color}12;color:${e.module_color};border:1px solid ${e.module_color}25">${e.module_name_lo}</span>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900 font-mono leading-snug break-all">${e.error_message}</h2>
                    </div>
                    <button data-modal-close class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0 ml-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                        <p class="text-[11px] font-medium text-gray-400 mb-0.5">ວັນທີພົບບັນຫາ</p>
                        <p class="text-sm font-semibold text-gray-700">${e.occurred_at}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                        <p class="text-[11px] font-medium text-gray-400 mb-0.5">ລາຍງານໂດຍ</p>
                        <p class="text-sm font-semibold text-gray-700">${e.employee_name}</p>
                    </div>
                </div>

                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                    <div class="p-4 rounded-xl bg-rose-50/60 border border-rose-100">
                        <p class="text-xs font-bold text-rose-700 mb-1.5 uppercase tracking-wider">ອາການທີ່ພົບ</p>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">${e.symptom}</p>
                    </div>

                    ${e.cause ? `<div class="p-4 rounded-xl bg-amber-50/60 border border-amber-100">
                        <p class="text-xs font-bold text-amber-700 mb-1.5 uppercase tracking-wider">ສາເຫດ</p>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">${e.cause}</p>
                    </div>` : ''}

                    ${e.solution ? `<div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <p class="text-xs font-bold text-emerald-700 mb-1.5 uppercase tracking-wider">ວິທີແກ້ໄຂ</p>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">${e.solution}</p>
                        ${videoBlock}
                    </div>` : videoBlock ? `<div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-100">${videoBlock}</div>` : ''}

                    ${imageBlock}
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                    
                    <a href="/errors/edit.php?id=${e.id}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 px-3.5 py-2 rounded-xl transition-all no-underline">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        ແກ້ໄຂຂໍ້ມູນ
                    </a>

                    ${e.status === 'pending' ? `
                    <button onclick="markResolved(${e.id})" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-3.5 py-2 rounded-xl shadow-sm shadow-indigo-500/10 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ໝາຍເປັນແກ້ໄຂແລ້ວ
                    </button>
                    ` : ''}
                </div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', html);
    initModals();
}

function markResolved(id) {
    fetch('/api/update-status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id, status: 'resolved' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('ອັບເດດສຳເລັດ');
            const modal = document.getElementById('detailModal');
            if (modal) modal.remove();
            setTimeout(() => location.reload(), 500);
        } else {
            showToast('ເກີດຂໍ້ຜິດພາດ', 'error');
        }
    });
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (!preview) return;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.parentElement.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}