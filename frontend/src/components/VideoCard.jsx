import { useAuth } from "../context/AuthContext";
import { deleteVideo as apiDeleteVideo } from "../services/api";
import { useState } from "react";

export default function VideoCard({ video, onDeleted }) {
  const { user } = useAuth();
  const [deleting, setDeleting] = useState(false);
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
      <mark className="badge">{video.category}</mark>
      {canDelete && (
        <button
          className="btn-delete"
          onClick={handleDelete}
          disabled={deleting}
          id={`delete-video-${video.id}`}
        >
          {deleting ? "Suppression…" : "Supprimer"}
        </button>
      )}
    </article>
  );
}
