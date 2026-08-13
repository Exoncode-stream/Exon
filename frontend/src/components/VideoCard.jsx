import { useAuth } from "../context/AuthContext";
import { deleteVideo as apiDeleteVideo } from "../services/api";
import { useState } from "react";
import { isStaff } from "../utils/auth";
import { useLike } from "../hooks/useLike";
import { useConfirmDelete } from "../hooks/useConfirmDelete";

export default function VideoCard({ video, onDeleted }) {
  const { user } = useAuth();
  const [deleting, setDeleting] = useState(false);
  const { liked, likesCount, toggleLike } = useLike('videos', video.id, false, video.likes_count || 0);
  const confirmDelete = useConfirmDelete();

  const canDelete = isStaff(user);

  async function handleDelete() {
    setDeleting(true);
    await confirmDelete(
      "Supprimer cette vidéo ?",
      () => apiDeleteVideo(video.id),
      () => onDeleted?.(video.id)
    );
    setDeleting(false);
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
          onClick={toggleLike}
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
