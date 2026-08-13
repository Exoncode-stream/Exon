export function useConfirmDelete() {
  const confirmAndDelete = async (confirmMessage, deleteApiFn, onSuccess) => {
    if (!window.confirm(confirmMessage)) return;

    try {
      await deleteApiFn();
      if (onSuccess) onSuccess();
    } catch (err) {
      alert(err.message || "Erreur lors de la suppression.");
    }
  };

  return confirmAndDelete;
}
