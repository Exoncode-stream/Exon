import { render, screen } from '@testing-library/react';
import { act } from 'react';
import { describe, it, expect, beforeEach } from 'vitest';
import { AuthProvider, useAuth } from '../context/AuthContext';

describe('AuthContext', () => {
  beforeEach(() => {
    localStorage.clear();
  });

  it('provides default unauthenticated state when no token in localStorage', () => {
    const TestComponent = () => {
      const { user, loading } = useAuth();
      if (loading) return <div>Loading...</div>;
      return <div>User: {user ? user.username : 'None'}</div>;
    };

    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    expect(screen.getByText('User: None')).toBeInTheDocument();
  });

  it('updates state on loginUser call', () => {
    const TestComponent = () => {
      const { user, loginUser } = useAuth();
      return (
        <div>
          <span>User: {user ? user.username : 'None'}</span>
          <button onClick={() => loginUser('mock_token', 'john_doe', 'admin')}>Login</button>
        </div>
      );
    };

    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    expect(screen.getByText('User: None')).toBeInTheDocument();

    act(() => {
      screen.getByText('Login').click();
    });

    expect(screen.getByText('User: john_doe')).toBeInTheDocument();
    expect(localStorage.getItem('token')).toBe('mock_token');
    expect(localStorage.getItem('username')).toBe('john_doe');
    expect(localStorage.getItem('role')).toBe('admin');
  });
});
