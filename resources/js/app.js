import './bootstrap';
import { signInWithEmail, loadProfile, signOut, signUpWithEmail, resetPasswordEmail, updatePassword } from './auth';

function el(id) { return document.getElementById(id); }

window.addEventListener('DOMContentLoaded', () => {
  const form = el('login-form');
  const out = el('output');
  const btnMe = el('btn-me');
  const btnLogout = el('btn-logout');

  // Binding untuk halaman login YIHAA
  const emailInput = el('hs-floating-input-email');
  const passwordInput = el('hs-floating-input-passowrd-value'); // maintain existing id typo
  const signInBtn = el('sign-in-btn');
  const registerBtn = el('register-btn');
  const forgotBtn = el('forgot-btn');
  const resetBtn = el('reset-btn');
  const registerName = el('register-name');
  const resetPasswordValue = el('reset-password-value');
  const passwordToggleBtn = document.querySelector('[data-hs-toggle-password]');

  if (emailInput && passwordInput && signInBtn) {
    signInBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      
      if (!email || !password) {
        alert('Please enter email and password');
        return;
      }

      signInBtn.textContent = 'Signing in...';
      signInBtn.disabled = true;

      const { data, error } = await signInWithEmail(email, password);
      
      if (error) {
        alert('Login error: ' + error.message);
        signInBtn.textContent = 'Sign In';
        signInBtn.disabled = false;
        return;
      }

      signInBtn.textContent = 'Success! Redirecting...';
      setTimeout(() => { window.location.href = '/home'; }, 800);
    });
  }

  // Binding untuk form sederhana (fallback)
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (out) out.textContent = 'Logging in...';
      const email = el('email')?.value.trim();
      const password = el('password')?.value;
      const { data, error } = await signInWithEmail(email, password);
      if (error) {
        if (out) out.textContent = 'Login error: ' + error.message;
        return;
      }
      if (out) out.textContent = 'Login success! Redirecting...';
      setTimeout(() => { window.location.href = '/home'; }, 800);
    });
  }

  // Signup flow
  if (registerBtn && emailInput && passwordInput) {
    registerBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      const name = registerName?.value?.trim() || email.split('@')[0];
      if (!email || !password) { alert('Email & password required'); return; }
      registerBtn.textContent = 'Registering...';
      registerBtn.disabled = true;
      const { data, error } = await signUpWithEmail(email, password, name);
      if (error) {
        alert('Signup error: ' + error.message);
        registerBtn.textContent = 'Register';
        registerBtn.disabled = false;
        return;
      }
      // If email confirmation is ON, session may be null
      if (!data.session) {
        alert('Check your email to confirm account');
        registerBtn.textContent = 'Email sent';
        return;
      }
      registerBtn.textContent = 'Success! Redirecting...';
      setTimeout(() => { window.location.href = '/home'; }, 900);
    });
  }

  // Forgot password flow
  if (forgotBtn && emailInput) {
    forgotBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const email = emailInput.value.trim();
      if (!email) { alert('Enter email'); return; }
      forgotBtn.textContent = 'Sending...';
      forgotBtn.disabled = true;
      const { error } = await resetPasswordEmail(email);
      if (error) {
        alert('Error: ' + error.message);
        forgotBtn.textContent = 'Send reset link';
        forgotBtn.disabled = false;
        return;
      }
      forgotBtn.textContent = 'Email sent!';
    });
  }

  // Reset password page
  if (resetBtn && resetPasswordValue) {
    resetBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const newPass = resetPasswordValue.value;
      if (!newPass) { alert('Enter new password'); return; }
      resetBtn.textContent = 'Updating...';
      resetBtn.disabled = true;
      const { error } = await updatePassword(newPass);
      if (error) {
        alert('Error: ' + error.message);
        resetBtn.textContent = 'Update password';
        resetBtn.disabled = false;
        return;
      }
      resetBtn.textContent = 'Updated! Redirecting...';
      setTimeout(() => { window.location.href = '/'; }, 1200);
    });
  }

  // Password reveal toggle fallback
  if (passwordToggleBtn && passwordInput) {
    passwordToggleBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const currentType = passwordInput.getAttribute('type');
      const nextType = currentType === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', nextType);
    });
  }

  if (btnMe) {
    btnMe.addEventListener('click', async () => {
      if (out) out.textContent = 'Fetching /api/me...';
      const me = await loadProfile();
      if (out) out.textContent = JSON.stringify(me, null, 2);
    });
  }

  if (btnLogout) {
    btnLogout.addEventListener('click', async () => {
      await signOut();
      if (out) out.textContent = 'Logged out';
      setTimeout(() => { window.location.href = '/'; }, 500);
    });
  }
});

// Expose untuk debugging console
window.loadProfile = loadProfile;
window.signInWithEmail = signInWithEmail;
window.signOut = signOut;
window.signUpWithEmail = signUpWithEmail;
window.resetPasswordEmail = resetPasswordEmail;
window.updatePassword = updatePassword;
