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
  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.error || `Request failed (${response.status})`);
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

export async function addVideo(title, youtube_id, category) {
  const res = await fetch(`${API_BASE}/videos`, {
    method: 'POST',
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

export async function addArticle(title, content) {
  const res = await fetch(`${API_BASE}/articles`, {
    method: 'POST',
    headers: jsonHeaders(),
    body: JSON.stringify({ title, content }),
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
