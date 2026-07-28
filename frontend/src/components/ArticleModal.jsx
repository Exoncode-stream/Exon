import { useEffect, useRef } from 'react';
import ReactMarkdown from 'react-markdown';

export default function ArticleModal({ article, onClose }) {
  const dialogRef = useRef(null);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (article) {
      dialog?.showModal();
    } else {
      dialog?.close();
    }
  }, [article]);

  if (!article) return null;

  // Helper to decode HTML entities if content was htmlspecialchars-encoded in DB
  const rawContent = (article.content || '')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'");

  return (
    <dialog ref={dialogRef} className="modal" id="article-modal" onClose={onClose}>
      <article className="modal-content">
        <header className="modal-header">
          <h2>{article.title}</h2>
          <button className="modal-close" onClick={onClose} aria-label="Fermer">
            ✕
          </button>
        </header>
        <div className="modal-body markdown-body" id="article-modal-body">
          <ReactMarkdown>{rawContent}</ReactMarkdown>
        </div>
      </article>
    </dialog>
  );
}
