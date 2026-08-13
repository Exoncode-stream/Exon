export function isStaff(user) {
  return Boolean(user && ['admin', 'moderator'].includes(user.role));
}

export function isAdmin(user) {
  return Boolean(user && user.role === 'admin');
}
