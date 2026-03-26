// ============================================================
// assets/js/main.js - Client-side Logic
// Online Voting System
// ============================================================

/* ---- Tab Switching (Login / Register) ---- */
function switchTab(tab) {
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f => f.classList.add('hidden'));

    document.querySelector(`.auth-tab[data-tab="${tab}"]`).classList.add('active');
    document.getElementById(`form-${tab}`).classList.remove('hidden');
}

/* ---- Form Validation: Registration ---- */
function validateRegister() {
    const name    = document.getElementById('reg-name');
    const email   = document.getElementById('reg-email');
    const sid     = document.getElementById('reg-sid');
    const pass    = document.getElementById('reg-pass');
    const confirm = document.getElementById('reg-confirm');

    clearErrors();
    let valid = true;

    if (!name || name.value.trim().length < 2) {
        showError('err-name', 'Full name must be at least 2 characters.'); valid = false;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        showError('err-email', 'Please enter a valid email address.'); valid = false;
    }
    if (!sid || sid.value.trim().length < 3) {
        showError('err-sid', 'Please enter a valid student ID.'); valid = false;
    }
    if (!pass || pass.value.length < 6) {
        showError('err-pass', 'Password must be at least 6 characters.'); valid = false;
    }
    if (!confirm || confirm.value !== pass.value) {
        showError('err-confirm', 'Passwords do not match.'); valid = false;
    }

    if (valid) setLoading('btn-register', true);
    return valid;
}

/* ---- Form Validation: Login ---- */
function validateLogin() {
    const email = document.getElementById('login-email');
    const pass  = document.getElementById('login-pass');

    clearErrors();
    let valid = true;

    if (!email || email.value.trim() === '') {
        showError('err-login-email', 'Please enter your email.'); valid = false;
    }
    if (!pass || pass.value === '') {
        showError('err-login-pass', 'Please enter your password.'); valid = false;
    }

    if (valid) setLoading('btn-login', true);
    return valid;
}

/* ---- Voting: Candidate Selection ---- */
let selectedCandidate = null;

function selectCandidate(id, name) {
    document.querySelectorAll('.candidate-card').forEach(c => c.classList.remove('selected'));
    const card = document.querySelector(`.candidate-card[data-id="${id}"]`);
    if (card) card.classList.add('selected');

    selectedCandidate = id;
    document.getElementById('candidate_id').value = id;

    const btn = document.getElementById('btn-vote');
    if (btn) {
        btn.disabled = false;
        btn.textContent = `✔ Vote for ${name}`;
    }

    const info = document.getElementById('selected-info');
    if (info) {
        info.textContent = `Selected: ${name}`;
        info.className = 'alert alert-info';
    }
}

/* ---- Vote Form Validation ---- */
function validateVote() {
    if (!selectedCandidate) {
        alert('Please select a candidate before submitting your vote.');
        return false;
    }
    if (!confirm(`Are you sure you want to vote? This action cannot be undone.`)) {
        return false;
    }
    setLoading('btn-vote', true);
    return true;
}

/* ---- Helpers ---- */
function showError(id, msg) {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; el.style.display = 'block'; }
}

function clearErrors() {
    document.querySelectorAll('.field-error').forEach(e => {
        e.textContent = '';
        e.style.display = 'none';
    });
}

function setLoading(btnId, state) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    if (state) {
        btn.dataset.original = btn.innerHTML;
        btn.innerHTML = '<span class="spinner"></span> Please wait...';
        btn.disabled = true;
    } else {
        btn.innerHTML = btn.dataset.original;
        btn.disabled = false;
    }
}

/* ---- Auto-dismiss alerts ---- */
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert-auto');
    alerts.forEach(a => {
        setTimeout(() => {
            a.style.transition = 'opacity 0.5s';
            a.style.opacity = '0';
            setTimeout(() => a.remove(), 500);
        }, 4000);
    });

    // Animate progress bars
    document.querySelectorAll('.progress-fill').forEach(bar => {
        const target = bar.dataset.width || '0';
        bar.style.width = '0%';
        setTimeout(() => bar.style.width = target + '%', 300);
    });
});

/* ---- Admin: Confirm dangerous actions ---- */
function confirmAction(msg) {
    return confirm(msg || 'Are you sure?');
}

/* ---- Password visibility toggle ---- */
function togglePass(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
}
