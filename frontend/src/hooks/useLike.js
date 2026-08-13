import { useState } from 'react';
import { toggleLike as apiToggleLike } from '../services/api';
import { useAuth } from '../context/AuthContext';

export function useLike(type, id, initialLiked = false, initialCount = 0) {
  const { user } = useAuth();
  const [liked, setLiked] = useState(initialLiked);
  const [likesCount, setLikesCount] = useState(initialCount);

  const toggleLike = async () => {
    if (!user) {
      alert("Vous devez être connecté pour aimer ce contenu.");
      return;
    }

    try {
      const res = await apiToggleLike(type, id);
      setLiked(res.liked);
      setLikesCount(res.likes_count);
    } catch (err) {
      alert(err.message || "Erreur lors de l'action");
    }
  };

  return { liked, likesCount, toggleLike, setLiked, setLikesCount };
}
