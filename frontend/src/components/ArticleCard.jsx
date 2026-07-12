export default function ArticleCard({ article, onClick }) {
  return (
    <article
      className="article-card"
      onClick={onClick}
      role="button"
      tabIndex={0}
      onKeyDown={(e) => e.key === 'Enter' && onClick?.()}
      id={`article-${article.id}`}
    >
      <h3>{article.title}</h3>
      <p className="article-preview">
        {article.content?.slice(0, 100)}
        {article.content?.length > 100 ? '…' : ''}
      </p>
    </article>
  );
}
