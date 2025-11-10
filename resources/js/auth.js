import { supabase } from './supabaseClient';

export async function signInWithEmail(email, password) {
  return await supabase.auth.signInWithPassword({ email, password });
}

export async function loadProfile() {
  const { data: { session } } = await supabase.auth.getSession();
  if (!session) return null;
  const res = await fetch('/api/me', {
    headers: { Authorization: 'Bearer ' + session.access_token }
  });
  if (!res.ok) return null;
  return await res.json();
}

export async function signOut() {
  await supabase.auth.signOut();
}

export async function signUpWithEmail(email, password, name) {
  return await supabase.auth.signUp({
    email,
    password,
    options: {
      data: { name },
      emailRedirectTo: `${window.location.origin}/`
    }
  });
}

export async function resetPasswordEmail(email) {
  return await supabase.auth.resetPasswordForEmail(email, {
    redirectTo: `${window.location.origin}/reset-password`
  });
}

export async function updatePassword(newPassword) {
  return await supabase.auth.updateUser({ password: newPassword });
}