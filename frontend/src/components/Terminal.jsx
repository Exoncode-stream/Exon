import { useState, useEffect, useRef } from "react";

export default function Terminal({ pseudo, description }) {
  const [displayText, setDisplayText] = useState("");
  const [showContent, setShowContent] = useState(false);
  const textRef = useRef("whoami");
  const indexRef = useRef(0);

  useEffect(() => {
    const id = setInterval(() => {
      if (indexRef.current < textRef.current.length) {
        setDisplayText((prev) => prev + textRef.current[indexRef.current]);
        indexRef.current += 1;
      } else {
        clearInterval(id);
        setTimeout(() => setShowContent(true), 350);
      }
    }, 65);

    return () => clearInterval(id);
  }, []);

  return (
    <section className="terminal-block fade-in" id="hero-terminal">
      <header className="terminal-header">
        <i className="terminal-dot dot-red" aria-hidden="true" />
        <i className="terminal-dot dot-yellow" aria-hidden="true" />
        <i className="terminal-dot dot-green" aria-hidden="true" />
        <cite className="terminal-title">exon@hub:~</cite>
      </header>

      <pre className="terminal-body">
        <code className="terminal-line">
          <samp className="terminal-prompt">$ </samp>
          <kbd>{displayText}</kbd>
          {!showContent && <mark className="cursor" aria-hidden="true" />}
        </code>

        {showContent && (
          <>
            <h1 className="terminal-pseudo">{pseudo}</h1>
            <p className="terminal-desc">{description}</p>
          </>
        )}
      </pre>
    </section>
  );
}
