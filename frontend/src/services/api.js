const API_BASE = 'http://localhost:8000/api';

function getAuthHeaders() {
  const token = localStorage.getItem('token');
  return token ? { Authorization: `Bearer ${token}` } : {};
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

async function customFetch(endpoint, options = {}) {
  const headers = {
    ...(options.body ? { 'Content-Type': 'application/json' } : {}),
    ...getAuthHeaders(),
    ...options.headers,
  };

  const res = await fetch(`${API_BASE}${endpoint}`, {
    credentials: 'include',
    ...options,
    headers,
  });

  return handleResponse(res);
}

/* ─── Public ─── */

export async function fetchHub() {
  return customFetch('/hub');
}

export async function login(username, password) {
  return customFetch('/login', {
    method: 'POST',
    body: JSON.stringify({ username, password }),
  });
}

export async function register(username, password) {
  return customFetch('/register', {
    method: 'POST',
    body: JSON.stringify({ username, password }),
  });
}

/* ─── Authenticated ─── */

export async function verifyToken() {
  return customFetch('/verify-token');
}

export async function logoutApi() {
  return customFetch('/logout', {
    method: 'POST',
  });
}

export async function fetchVideos() {
  return customFetch('/videos');
}

export async function addVideo(title, youtube_id, category) {
  return customFetch('/videos', {
    method: 'POST',
    body: JSON.stringify({ title, youtube_id, category }),
  });
}

export async function updateVideo(id, title, youtube_id, category) {
  return customFetch(`/videos/${id}`, {
    method: 'PUT',
    body: JSON.stringify({ title, youtube_id, category }),
  });
}

export async function deleteVideo(id) {
  return customFetch(`/videos/${id}`, {
    method: 'DELETE',
  });
}

export async function fetchArticles() {
  return customFetch('/articles');
}

export async function addArticle(title, content) {
  return customFetch('/articles', {
    method: 'POST',
    body: JSON.stringify({ title, content }),
  });
}

export async function updateArticle(id, title, content) {
  return customFetch(`/articles/${id}`, {
    method: 'PUT',
    body: JSON.stringify({ title, content }),
  });
}

export async function deleteArticle(id) {
  return customFetch(`/articles/${id}`, {
    method: 'DELETE',
  });
}

export async function fetchLinks() {
  return customFetch('/links');
}

export async function addLink(name, url) {
  return customFetch('/links', {
    method: 'POST',
    body: JSON.stringify({ name, url }),
  });
}

export async function updateLink(id, name, url) {
  return customFetch(`/links/${id}`, {
    method: 'PUT',
    body: JSON.stringify({ name, url }),
  });
}

export async function deleteLink(id) {
  return customFetch(`/links/${id}`, {
    method: 'DELETE',
  });
}

export async function fetchUsers() {
  return customFetch('/users');
}

export async function updateUserRole(userId, role) {
  return customFetch(`/users/${userId}/role`, {
    method: 'PUT',
    body: JSON.stringify({ role }),
  });
}

/* ─── Comments & Likes ─── */

export async function fetchComments(type, id) {
  return customFetch(`/${type}/${id}/comments`);
}

export async function addComment(type, id, content) {
  return customFetch(`/${type}/${id}/comments`, {
    method: 'POST',
    body: JSON.stringify({ content }),
  });
}

export async function deleteComment(id) {
  return customFetch(`/comments/${id}`, {
    method: 'DELETE',
  });
}

export async function toggleLike(type, id) {
  return customFetch(`/${type}/${id}/like`, {
    method: 'POST',
  });
}

/* ─── Profile & Account ─── */

export async function fetchProfile() {
  return customFetch('/profile');
}

export async function changePassword(currentPassword, newPassword, newPasswordConfirmation) {
  return customFetch('/profile/password', {
    method: 'PUT',
    body: JSON.stringify({
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirmation: newPasswordConfirmation,
    }),
  });
}

