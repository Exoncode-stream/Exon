import { useState, useEffect, useRef, useCallback } from 'react';
import ReactMarkdown from 'react-markdown';
import { useAuth } from '../context/AuthContext';
import { fetchComments, addComment, deleteComment, toggleLike } from '../services/api';

export default function ArticleModal({ article, onClose }) {
  const dialogRef = useRef(null);
  const { user } = useAuth();

  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState("");
  const [commentError, setCommentError] = useState("");
  const [likesCount, setLikesCount] = useState(article?.likes_count || 0);
  const [liked, setLiked] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const loadComments = useCallback(async () => {
    if (!article?.id) return;
    try {
      const data = await fetchComments('articles', article.id);
      setComments(data.comments || []);
    } catch (err) {
      console.error("Failed to load comments", err);
    }
  }, [article?.id]);

  useEffect(() => {
    const dialog = dialogRef.current;
    if (article) {
      dialog?.showModal();
      setLikesCount(article.likes_count || 0);
      setLiked(false);
      loadComments();
    } else {
      dialog?.close();
    }
  }, [article, loadComments]);

  if (!article) return null;

  const rawContent = article.content || '';

  async function handleToggleLike() {
    if (!user) {
      alert("Vous devez être connecté pour liker cet article.");
      return;
    }
    try {
      const res = await toggleLike('articles', article.id);
      setLiked(res.liked);
      setLikesCount(res.likes_count);
    } catch (err) {
      alert(err.message);
    }
  }

  async function handleAddComment(e) {
    e.preventDefault();
    if (!newComment.trim()) return;

    setIsSubmitting(true);
    setCommentError("");

    try {
      const res = await addComment('articles', article.id, newComment);
      setComments((prev) => [res.comment, ...prev]);
      setNewComment("");
    } catch (err) {
      setCommentError(err.message);
    } finally {
      setIsSubmitting(false);
    }
  }

  async function handleDeleteComment(id) {
    if (!window.confirm("Voulez-vous supprimer ce commentaire ?")) return;

    try {
      await deleteComment(id);
      setComments((prev) => prev.filter((c) => c.id !== id));
    } catch (err) {
      alert(err.message);
    }
  }

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

        {/* ─── Like & Interaction Footer ─── */}
        <footer className="article-interactions" id="article-interactions">
          <button
            type="button"
            className={`btn-like ${liked ? 'liked' : ''}`}
            onClick={handleToggleLike}
            id="like-article-btn"
          >
            ♥ {likesCount} {likesCount === 1 ? 'Like' : 'Likes'}
          </button>
        </footer>

        {/* ─── Comments Section ─── */}
        <section className="comments-section" id="comments-section">
          <h3>Commentaires ({comments.length})</h3>

          {user ? (
            <form onSubmit={handleAddComment} className="comment-form" id="add-comment-form">
              <textarea
                rows="2"
                placeholder="Rédigez un commentaire..."
                value={newComment}
                onChange={(e) => setNewComment(e.target.value)}
                required
                id="comment-input"
              />
              {commentError && <p className="error-text">{commentError}</p>}
              <button type="submit" className="btn-primary" disabled={isSubmitting} id="submit-comment-btn">
                {isSubmitting ? "Envoi..." : "Publier"}
              </button>
            </form>
          ) : (
            <p className="login-prompt">&gt; Connectez-vous pour laisser un commentaire.</p>
          )}

          <ul className="comments-list" id="comments-list">
            {comments.map((comment) => {
              const isOwner = user && user.username === comment.user?.username;
              const isStaff = user && ['admin', 'moderator'].includes(user.role);

              return (
                <li key={comment.id} className="comment-item">
                  <div className="comment-header">
                    <span className="comment-author">@{comment.user?.username || 'Anonyme'}</span>
                    <time className="comment-date">
                      {comment.created_at ? new Date(comment.created_at).toLocaleDateString() : ''}
                    </time>
                  </div>
                  <p className="comment-text">{comment.content}</p>
                  {(isOwner || isStaff) && (
                    <button
                      type="button"
                      className="btn-delete-comment"
                      onClick={() => handleDeleteComment(comment.id)}
                      id={`delete-comment-${comment.id}`}
                    >
                      Supprimer
                    </button>
                  )}
                </li>
              );
            })}

            {comments.length === 0 && (
              <li className="no-comments">&gt; Aucun commentaire pour le moment. Soyez le premier !</li>
            )}
          </ul>
        </section>
      </article>
    </dialog>
  );
}
