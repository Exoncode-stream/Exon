import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import VideoCard from '../components/VideoCard';
import { AuthContext } from '../context/AuthContext';

describe('VideoCard Component', () => {
  const mockVideo = {
    id: 1,
    title: 'Laravel 12 Tutorial',
    youtube_id: 'dQw4w9WgXcQ',
    category: 'Laravel',
    likes_count: 5,
  };

  it('renders video title, category badge, and iframe embed src', () => {
    render(
      <AuthContext.Provider value={{ user: null }}>
        <VideoCard video={mockVideo} />
      </AuthContext.Provider>
    );

    expect(screen.getByText('Laravel 12 Tutorial')).toBeInTheDocument();
    expect(screen.getByText('Laravel')).toBeInTheDocument();
    expect(screen.getByText('♥ 5')).toBeInTheDocument();

    const iframe = screen.getByTitle('Laravel 12 Tutorial');
    expect(iframe).toHaveAttribute('src', 'https://www.youtube.com/embed/dQw4w9WgXcQ');
  });

  it('displays delete button for admin or moderator users', () => {
    const adminUser = { username: 'admin', role: 'admin' };

    render(
      <AuthContext.Provider value={{ user: adminUser }}>
        <VideoCard video={mockVideo} onDeleted={vi.fn()} />
      </AuthContext.Provider>
    );

    expect(screen.getByText('Supprimer')).toBeInTheDocument();
  });
});
