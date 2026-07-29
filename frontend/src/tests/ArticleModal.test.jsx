import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import ArticleModal from '../components/ArticleModal';
import { AuthContext } from '../context/AuthContext';

// Mock dialog HTML5 methods in jsdom
beforeEach(() => {
  HTMLDialogElement.prototype.showModal = vi.fn();
  HTMLDialogElement.prototype.close = vi.fn();
});

describe('ArticleModal Component', () => {
  const mockArticle = {
    id: 1,
    title: 'Markdown Guide',
    content: '# Hello World\nThis is a **Markdown** article.',
    likes_count: 3,
  };

  it('does not render when article is null', () => {
    const { container } = render(
      <AuthContext.Provider value={{ user: null }}>
        <ArticleModal article={null} onClose={vi.fn()} />
      </AuthContext.Provider>
    );

    expect(container.firstChild).toBeNull();
  });

  it('renders modal title and markdown content when article is provided', () => {
    render(
      <AuthContext.Provider value={{ user: null }}>
        <ArticleModal article={mockArticle} onClose={vi.fn()} />
      </AuthContext.Provider>
    );

    expect(screen.getByText('Markdown Guide')).toBeInTheDocument();
    expect(screen.getByText('Hello World')).toBeInTheDocument();
    expect(screen.getByText('Markdown')).toBeInTheDocument();
  });
});
