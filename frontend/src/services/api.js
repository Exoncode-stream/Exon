const API_BASE = 'http://localhost:8000/api';

function getAuthHeaders() {
  const token = localStorage.getItem('token');
  return token ? { Authorization: `Bearer ${token}` } : {};
}

function jsonHeaders() {
  return {
    'Content-Type': 'application/json',
    ...getAuthHeaders(),
  };
}

async function handleResponse(response) {
  let data;
  try {
    data = await response.json();
  } catch {
    throw new Error(`Erreur serveur (${response.status}) : Réponse non valide.`);
  }
  if (!response.ok) {
    throw new Error(data.error || data.message || `Échec de la requête (${response.status})`);
  }
  return data;
}

/* ─── Public ─── */

export async function fetchHub() {
  const res = await fetch(`${API_BASE}/hub`);
  return handleResponse(res);
}

export async function login(username, password) {
  const res = await fetch(`${API_BASE}/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password }),
  });
  return handleResponse(res);
}

export async function register(username, password) {
  const res = await fetch(`${API_BASE}/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password }),
  });
  return handleResponse(res);
}

/* ─── Authenticated ─── */

export async function verifyToken() {
  const res = await fetch(`${API_BASE}/verify-token`, {
    headers: getAuthHeaders(),
  });
  return handleResponse(res);
}

export async function logoutApi() {
  const res = await fetch(`${API_BASE}/logout`, {
    method: 'POST',
    headers: jsonHeaders(),
  });
  return handleResponse(res);
}

export async function fetchVideos() {
  const res = await fetch(`${API_BASE}/videos`);
  return handleResponse(res);
}

export async function addVideo(title, youtube_id, category) {
  const res = await fetch(`${API_BASE}/videos`, {
    method: 'POST',
    headers: jsonHeaders(),
    body: JSON.stringify({ title, youtube_id, category }),
  });
  return handleResponse(res);
}

export async function updateVideo(id, title, youtube_id, category) {
  const res = await fetch(`${API_BASE}/videos/${id}`, {
    method: 'PUT',
    headers: jsonHeaders(),
    body: JSON.stringify({ title, youtube_id, category }),
  });
  return handleResponse(res);
}

export async function deleteVideo(id) {
  const res = await fetch(`${API_BASE}/videos/${id}`, {
    method: 'DELETE',
    headers: jsonHeaders(),
  });
  return handleResponse(res);
}

export async function fetchArticles() {
  const res = await fetch(`${API_BASE}/articles`);
  return handleResponse(res);
}

export async function addArticle(title, content) {
  const res = await fetch(`${API_BASE}/articles`, {
    method: 'POST',
    headers: jsonHeaders(),
    body: JSON.stringify({ title, content }),
  });
  return handleResponse(res);
}

export async function updateArticle(id, title, content) {
  const res = await fetch(`${API_BASE}/articles/${id}`, {
    method: 'PUT',
    headers: jsonHeaders(),
    body: JSON.stringify({ title, content }),
  });
  return handleResponse(res);
}

export async function deleteArticle(id) {
  const res = await fetch(`${API_BASE}/articles/${id}`, {
    method: 'DELETE',
    headers: jsonHeaders(),
  });
  return handleResponse(res);
}

export async function fetchLinks() {
  const res = await fetch(`${API_BASE}/links`);
  return handleResponse(res);
}

export async function addLink(name, url) {
  const res = await fetch(`${API_BASE}/links`, {
    method: 'POST',
    headers: jsonHeaders(),
    body: JSON.stringify({ name, url }),
  });
  return handleResponse(res);
}

export async function updateLink(id, name, url) {
  const res = await fetch(`${API_BASE}/links/${id}`, {
    method: 'PUT',
    headers: jsonHeaders(),
    body: JSON.stringify({ name, url }),
  });
  return handleResponse(res);
}

export async function deleteLink(id) {
  const res = await fetch(`${API_BASE}/links/${id}`, {
    method: 'DELETE',
    headers: jsonHeaders(),
  });
  return handleResponse(res);
}

export async function fetchUsers() {
  const res = await fetch(`${API_BASE}/users`, {
    headers: getAuthHeaders(),
  });
  return handleResponse(res);
}

export async function updateUserRole(userId, role) {
  const res = await fetch(`${API_BASE}/users/${userId}/role`, {
    method: 'PUT',
    headers: jsonHeaders(),
    body: JSON.stringify({ role }),
  });
  return handleResponse(res);
}

/* ─── Comments & Likes ─── */

export async function fetchComments(type, id) {
  const res = await fetch(`${API_BASE}/${type}/${id}/comments`);
  return handleResponse(res);
}

export async function addComment(type, id, content) {
  const res = await fetch(`${API_BASE}/${type}/${id}/comments`, {
    method: 'POST',
    headers: jsonHeaders(),
    body: JSON.stringify({ content }),
  });
  return handleResponse(res);
}

export async function deleteComment(id) {
  const res = await fetch(`${API_BASE}/comments/${id}`, {
    method: 'DELETE',
    headers: jsonHeaders(),
  });
  return handleResponse(res);
}

export async function toggleLike(type, id) {
  const res = await fetch(`${API_BASE}/${type}/${id}/like`, {
    method: 'POST',
    headers: jsonHeaders(),
  });
  return handleResponse(res);
}

/* ─── Profile & Account ─── */

export async function fetchProfile() {
  const res = await fetch(`${API_BASE}/profile`, {
    headers: getAuthHeaders(),
  });
  return handleResponse(res);
}

export async function changePassword(currentPassword, newPassword, newPasswordConfirmation) {
  const res = await fetch(`${API_BASE}/profile/password`, {
    method: 'PUT',
    headers: jsonHeaders(),
    body: JSON.stringify({
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirmation: newPasswordConfirmation,
    }),
  });
  return handleResponse(res);
}
