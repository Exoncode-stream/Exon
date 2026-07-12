import { useEffect, useRef } from 'react';

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

  return (
    <dialog ref={dialogRef} className="modal" id="article-modal" onClose={onClose}>
      <article className="modal-content">
        <header className="modal-header">
          <h2>{article.title}</h2>
          <button className="modal-close" onClick={onClose} aria-label="Fermer">
            ✕
          </button>
        </header>
        <p className="modal-body">{article.content}</p>
      </article>
    </dialog>
  );
}
