const channelUrl = "https://www.youtube.com/@exon9858";

function ArrowIcon({ diagonal = false }: { diagonal?: boolean }) {
  return (
    <svg
      aria-hidden="true"
      viewBox="0 0 24 24"
      fill="none"
      className={diagonal ? "icon icon--diagonal" : "icon"}
    >
      <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" strokeWidth="2" />
    </svg>
  );
}

export default function App() {
  return (
    <div className="site-shell">
      <header className="topbar">
        <a className="wordmark" href="#top" aria-label="Exon home">
          EXON<span>.</span>
        </a>
        <nav className="nav-links" aria-label="Main navigation">
          <a href="#about">About</a>
          <a href="#why">Why I stream</a>
          <a href="#channel">Channel</a>
        </nav>
        <a
          className="channel-link"
          href={channelUrl}
          target="_blank"
          rel="noreferrer"
        >
          Visit channel
          <ArrowIcon diagonal />
        </a>
      </header>

      <main id="top">
        <section className="hero" aria-labelledby="hero-title">
          <header className="hero-copy">
            <p className="eyebrow">
              <span className="status-dot" aria-hidden="true" />
              Computer science student
            </p>
            <h1 id="hero-title">
              Learning,
              <br />
              thinking,
              <br />
              <span>sharing.</span>
            </h1>
            <p className="hero-description">
              I’m Exon, a computer science student learning to trust my own
              logic again by coding without AI.
            </p>
            <nav className="hero-actions" aria-label="Hero actions">
              <a
                className="primary-action"
                href={channelUrl}
                target="_blank"
                rel="noreferrer"
              >
                Go to YouTube
                <span className="action-icon">
                  <ArrowIcon diagonal />
                </span>
              </a>
              <a className="text-action" href="#why">
                Why I stream
                <ArrowIcon />
              </a>
            </nav>
          </header>

          <aside className="hero-statement" aria-label="Exon, computer science student">
            <header className="statement-topline">
              <span>Student notes</span>
              <span>CS / 01</span>
            </header>
            <figure className="statement-center">
              <span className="brace" aria-hidden="true">{"{"}</span>
              <div>
                <p>curiosity</p>
                <p>logic</p>
                <p>progress</p>
              </div>
              <span className="brace" aria-hidden="true">{"}"}</span>
            </figure>
            <footer className="statement-footer">
              <span>Still learning</span>
              <strong>EXON</strong>
            </footer>
          </aside>
        </section>

        <section className="about-section" id="about" aria-labelledby="about-title">
          <p className="section-number" aria-hidden="true">01</p>
          <header className="about-heading">
            <p className="section-kicker">About me</p>
            <h2 id="about-title">
              A student with
              <br />
              <span>a curious mind.</span>
            </h2>
          </header>
          <article className="about-copy">
            <p>
              Computer science gives me a new way to understand how things
              work. There is always another idea to explore, another problem
              to think through, and another concept to understand.
            </p>
            <p>
              This space is a record of that process. Not from the perspective
              of an expert, but from a student rebuilding confidence in his
              own thinking.
            </p>
          </article>
        </section>

        <section className="why-section" id="why" aria-labelledby="why-title">
          <header className="why-intro">
            <p className="section-kicker section-kicker--dark">
              Why I started streaming
            </p>
            <h2 id="why-title">
              I want to learn how to think for myself again.
            </h2>
          </header>
          <div className="why-grid">
            <article>
              <span>01</span>
              <h3>I started with AI</h3>
              <p>
                When I started coding, I used AI a lot. GPT-3.0 was available
                at the time, and asking it for help quickly became a habit.
              </p>
            </article>
            <article>
              <span>02</span>
              <h3>I stopped thinking</h3>
              <p>
                Relying on AI made me lose part of my algorithmic thinking and
                logic. I was getting answers without building the skills to
                reach them myself.
              </p>
            </article>
            <article>
              <span>03</span>
              <h3>I felt disappointed</h3>
              <p>
                Seeing so many people use AI as the default way to code
                disappoints me. It made me question what we lose when we skip
                the difficult part of learning.
              </p>
            </article>
            <article>
              <span>04</span>
              <h3>So I stream</h3>
              <p>
                Streaming forces me to code without AI and work through
                problems with my own mind. If I am not passionate enough to
                think through the code, then what is the point?
              </p>
            </article>
          </div>
        </section>

        <section className="channel-section" id="channel" aria-labelledby="channel-title">
          <header>
            <p className="section-kicker">Follow along</p>
            <h2 id="channel-title">This is only the beginning.</h2>
          </header>
          <a
            className="channel-action"
            href={channelUrl}
            target="_blank"
            rel="noreferrer"
          >
            <span>
              Watch me code on
              <strong>YouTube live</strong>
            </span>
            <span className="round-arrow">
              <ArrowIcon diagonal />
            </span>
          </a>
        </section>
      </main>

      <footer>
        <a className="footer-wordmark" href="#top" aria-label="Exon home">
          EXON<span>.</span>
        </a>
        <p>Computer science student</p>
        <a href={channelUrl} target="_blank" rel="noreferrer">
          youtube.com/@exon9858
        </a>
      </footer>
    </div>
  );
}
