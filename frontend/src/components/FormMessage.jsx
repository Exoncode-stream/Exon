export default function FormMessage({ message, type }) {
  if (!message) return null;

  return (
    <output className={`form-message ${type}`} role="status">
      {message}
    </output>
  );
}
