import { useAuth } from "../context/AuthContext";
import { deleteVideo as apiDeleteVideo, toggleLike as apiToggleLike } from "../services/api";
import { useState } from "react";

export default function VideoCard({ video, onDeleted }) {
  const { user } = useAuth();
  const [deleting, setDeleting] = useState(false);
  const [likesCount, setLikesCount] = useState(video.likes_count || 0);
  const [liked, setLiked] = useState(false);

  const canDelete = user && (user.role === "admin" || user.role === "moderator");

  async function handleDelete() {
    if (!window.confirm("Supprimer cette vidéo ?")) return;
    setDeleting(true);
    try {
      await apiDeleteVideo(video.id);
      onDeleted?.(video.id);
    } catch (err) {
      alert(err.message);
    } finally {
      setDeleting(false);
    }
  }

  async function handleLike() {
    if (!user) {
      alert("Vous devez être connecté pour liker cette vidéo.");
      return;
    }
    try {
      const res = await apiToggleLike('videos', video.id);
      setLiked(res.liked);
      setLikesCount(res.likes_count);
    } catch (err) {
      alert(err.message);
    }
  }

  /* Extract YouTube ID from various URL formats */
  function getYoutubeId(raw) {
    if (!raw) return "";
    const match = raw.match(
      /(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/
    );
    return match ? match[1] : raw;
  }

  const ytId = getYoutubeId(video.youtube_id || video.youtubeId);

  return (
    <article className="video-card" id={`video-${video.id}`}>
      <iframe
        src={`https://www.youtube.com/embed/${ytId}`}
        title={video.title}
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowFullScreen
        loading="lazy"
      />
      <h3>{video.title}</h3>
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginTop: "0.5rem" }}>
        <mark className="badge">{video.category}</mark>
        <button
          type="button"
          className={`btn-like ${liked ? 'liked' : ''}`}
          onClick={handleLike}
          id={`like-video-${video.id}`}
        >
          ♥ {likesCount}
        </button>
      </div>
      {canDelete && (
        <button
          className="btn-delete"
          onClick={handleDelete}
          disabled={deleting}
          id={`delete-video-${video.id}`}
          style={{ marginTop: "0.75rem" }}
        >
          {deleting ? "Suppression…" : "Supprimer"}
        </button>
      )}
    </article>
  );
}
