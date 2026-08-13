import { useState } from "react";
import FormMessage from "./FormMessage";
import { useConfirmDelete } from "../hooks/useConfirmDelete";

export default function AdminCrudSection({
  sectionId,
  title,
  fields,
  items,
  columns,
  addApiFn,
  updateApiFn,
  deleteApiFn,
  onItemAdded,
  onItemUpdated,
  onItemDeleted,
  emptyText = "Aucun élément enregistré",
  itemKey = "id",
}) {
  const [formData, setFormData] = useState({});
  const [editingItem, setEditingItem] = useState(null);
  const [msg, setMsg] = useState("");
  const [msgType, setMsgType] = useState("");
  const confirmDelete = useConfirmDelete();

  const handleInputChange = (fieldName, value) => {
    setFormData((prev) => ({ ...prev, [fieldName]: value }));
  };

  const handleEditChange = (fieldName, value) => {
    setEditingItem((prev) => ({ ...prev, [fieldName]: value }));
  };

  async function handleAdd(e) {
    e.preventDefault();
    setMsg("Envoi…");
    setMsgType("");

    try {
      const fieldValues = fields.map((f) => formData[f.name] || "");
      const res = await addApiFn(...fieldValues);
      setMsg(res?.message || "Élément ajouté !");
      setMsgType("success");
      setFormData({});
      onItemAdded?.();
    } catch (err) {
      setMsg(err.message);
      setMsgType("error");
    }
  }

  async function handleUpdate(e) {
    e.preventDefault();
    if (!editingItem) return;

    try {
      const fieldValues = fields.map((f) => editingItem[f.name] || "");
      await updateApiFn(editingItem[itemKey], ...fieldValues);
      setEditingItem(null);
      onItemUpdated?.();
    } catch (err) {
      alert(err.message);
    }
  }

  async function handleDelete(id) {
    await confirmDelete(
      `Voulez-vous vraiment supprimer cet élément ?`,
      () => deleteApiFn(id),
      () => onItemDeleted?.(id)
    );
  }

  return (
    <section className="admin-section" id={sectionId}>
      <h2 className="section-title">{title}</h2>

      {/* Add Form */}
      <form onSubmit={handleAdd} className="form-card" id={`add-${sectionId}-form`}>
        {fields.map((field) => (
          <fieldset className="form-group" key={field.name}>
            <label htmlFor={`${sectionId}-${field.name}`}>{field.label}</label>
            {field.type === "textarea" ? (
              <textarea
                id={`${sectionId}-${field.name}`}
                placeholder={field.placeholder}
                rows={field.rows || 4}
                value={formData[field.name] || ""}
                onChange={(e) => handleInputChange(field.name, e.target.value)}
                required
              />
            ) : (
              <input
                type={field.type || "text"}
                id={`${sectionId}-${field.name}`}
                placeholder={field.placeholder}
                value={formData[field.name] || ""}
                onChange={(e) => handleInputChange(field.name, e.target.value)}
                required
              />
            )}
          </fieldset>
        ))}
        <button type="submit" className="btn-primary" id={`submit-${sectionId}`}>
          Ajouter
        </button>
      </form>
      <FormMessage message={msg} type={msgType} />

      {/* Inline Edit Form */}
      {editingItem && (
        <form onSubmit={handleUpdate} className="form-card" id={`edit-${sectionId}-form`} style={{ marginTop: "1rem" }}>
          <h3>Modifier l'élément #{editingItem[itemKey]}</h3>
          {fields.map((field) => (
            <fieldset className="form-group" key={field.name}>
              <label htmlFor={`edit-${sectionId}-${field.name}`}>{field.label}</label>
              {field.type === "textarea" ? (
                <textarea
                  id={`edit-${sectionId}-${field.name}`}
                  rows={field.rows || 4}
                  value={editingItem[field.name] || ""}
                  onChange={(e) => handleEditChange(field.name, e.target.value)}
                  required
                />
              ) : (
                <input
                  type={field.type || "text"}
                  id={`edit-${sectionId}-${field.name}`}
                  value={editingItem[field.name] || ""}
                  onChange={(e) => handleEditChange(field.name, e.target.value)}
                  required
                />
              )}
            </fieldset>
          ))}
          <div style={{ display: "flex", gap: "0.5rem" }}>
            <button type="submit" className="btn-primary" id={`save-${sectionId}-edit`}>
              Enregistrer
            </button>
            <button
              type="button"
              className="btn-danger"
              onClick={() => setEditingItem(null)}
              id={`cancel-${sectionId}-edit`}
            >
              Annuler
            </button>
          </div>
        </form>
      )}

      {/* Table */}
      <figure className="table-wrap" style={{ marginTop: "1.5rem" }}>
        <table className="users-table" id={`${sectionId}-table`}>
          <thead>
            <tr>
              {columns.map((col, idx) => (
                <th key={idx}>{col.header}</th>
              ))}
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {items.map((item) => (
              <tr key={item[itemKey]}>
                {columns.map((col, idx) => (
                  <td key={idx}>{col.render(item)}</td>
                ))}
                <td>
                  <div style={{ display: "flex", gap: "0.5rem" }}>
                    <button
                      type="button"
                      className="pill-link"
                      onClick={() => setEditingItem(item)}
                      id={`edit-${sectionId}-btn-${item[itemKey]}`}
                    >
                      Éditer
                    </button>
                    <button
                      type="button"
                      className="btn-danger"
                      onClick={() => handleDelete(item[itemKey])}
                      id={`delete-${sectionId}-btn-${item[itemKey]}`}
                    >
                      Supprimer
                    </button>
                  </div>
                </td>
              </tr>
            ))}
            {items.length === 0 && (
              <tr>
                <td colSpan={columns.length + 1} className="text-center">
                  {emptyText}
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </figure>
    </section>
  );
}
