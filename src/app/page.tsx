import Image from "next/image";
import Link from "next/link";

/**
 * Main landing page for the Exon developer platform.
 *
 * Architecture Decisions & Constraints:
 * - 100% Semantic HTML5: Zero `<div>` or `<span>` elements used across the page to ensure
 *   a clean accessibility tree and native landmark navigation.
 * - Server-Side Rendered (Next.js App Router): Fully static and optimized for performance
 *   and SEO crawlers without unnecessary client-side JavaScript bundle overhead.
 * - Native WAI-ARIA Landmarks: Every major section is bound to its primary heading via `aria-labelledby`.
 *
 * @returns The rendered semantic home page.
 */
export default function Home() {
  return (
    <main className="mx-auto flex w-full max-w-5xl flex-col gap-20 px-6 py-12 sm:px-8 sm:py-20">
      {/* 1. Global Navigation Header
          NOTE: Built with <header> and distinct <nav> landmarks for primary branding and section navigation.
      */}
      <header className="animate-fade-in-up flex flex-col gap-4 border-b border-zinc-200/80 pb-6 transition-colors duration-300 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
        <nav aria-label="Quick links" className="flex items-center gap-3">
          <Link
            href="/"
            className="group flex items-center gap-2.5 text-lg font-black tracking-wider text-zinc-900 uppercase transition-colors duration-200 dark:text-zinc-100"
          >
            <Image
              src="/web-logo.svg"
              alt="Exon logo"
              width={28}
              height={28}
              priority
              className="h-7 w-7 rounded-md object-contain transition-transform duration-300 group-hover:scale-110"
            />
            EXON
            <strong className="text-blue-500 transition-transform duration-300 group-hover:scale-125 inline-block">
              .
            </strong>
          </Link>
          {/* Version badge represented semantically with <kbd> */}
          <kbd className="rounded-md border border-zinc-200 bg-zinc-100 px-2 py-0.5 font-mono text-xs font-medium text-zinc-600 shadow-xs transition-colors duration-200 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
            v1.0 • Student Hub
          </kbd>
        </nav>

        {/* Primary anchor navigation using <menu> for actionable list items */}
        <nav aria-label="Site section navigation">
          <menu className="flex flex-wrap items-center gap-5 text-sm font-medium text-zinc-600 dark:text-zinc-400">
            <li>
              <Link
                href="#about"
                className="transition-colors duration-200 hover:text-zinc-950 dark:hover:text-zinc-100"
              >
                About &amp; Vision
              </Link>
            </li>
            <li>
              <Link
                href="#stack"
                className="transition-colors duration-200 hover:text-zinc-950 dark:hover:text-zinc-100"
              >
                Tech Stack
              </Link>
            </li>
            <li>
              <Link
                href="#projects"
                className="transition-colors duration-200 hover:text-zinc-950 dark:hover:text-zinc-100"
              >
                Projects
              </Link>
            </li>
            <li>
              <Link
                href="#article"
                className="transition-colors duration-200 hover:text-zinc-950 dark:hover:text-zinc-100"
              >
                Article
              </Link>
            </li>
            <li>
              <Link
                href="#contact"
                className="rounded-lg bg-zinc-900 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs transition-all duration-200 hover:bg-zinc-800 hover:scale-105 active:scale-95 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-zinc-200"
              >
                Contact
              </Link>
            </li>
          </menu>
        </nav>
      </header>

      {/* 2. Hero Section
          Contains primary positioning statement, developer mission, and key metrics description list.
      */}
      <section
        aria-labelledby="hero-title"
        className="animate-fade-in-up delay-100 flex flex-col gap-8 rounded-3xl border border-zinc-200/80 bg-linear-to-b from-white via-zinc-50/50 to-white p-8 shadow-sm transition-all duration-300 sm:p-12 dark:border-zinc-800 dark:from-zinc-900/90 dark:via-zinc-900/40 dark:to-zinc-950/80"
      >
        <header className="flex flex-col gap-5">
          {/* Live availability indicator with pulse animation */}
          <nav aria-label="Status badge">
            <kbd className="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3.5 py-1 font-mono text-xs font-semibold text-emerald-700 shadow-xs transition-all duration-300 hover:border-emerald-500/50 dark:text-emerald-400">
              <strong
                aria-hidden="true"
                className="animate-pulse-glow h-2 w-2 rounded-full bg-emerald-500 inline-block"
              />
              Open for Collaboration &amp; Mentorship
            </kbd>
          </nav>

          <h1
            id="hero-title"
            className="text-4xl font-extrabold tracking-tight text-zinc-900 sm:text-5xl lg:text-6xl dark:text-zinc-50"
          >
            Exon{" "}
            <strong className="font-light text-zinc-400 dark:text-zinc-600">
              /
            </strong>{" "}
            Student Developer Hub
          </h1>

          <p className="max-w-3xl text-xl leading-relaxed text-zinc-700 dark:text-zinc-300">
            A central space dedicated to software engineering experiments,
            open-source projects, and real-world learning from scratch.
          </p>
        </header>

        <section
          aria-label="Mission and vision"
          className="grid grid-cols-1 gap-6 text-base leading-relaxed text-zinc-600 sm:grid-cols-2 dark:text-zinc-400"
        >
          <p>
            Building software should not be an exclusive club or an intimidating
            race. In an era where AI tools and complex frameworks evolve
            constantly, mastering foundational concepts, understanding web
            semantics, and writing maintainable code remain the truest
            superpowers.
          </p>
          <p>
            This hub exists to prove that consistency and curiosity surpass
            imposter syndrome. Follow my engineering roadmap, inspect
            open-source architectures, and let&apos;s build durable software
            together.
          </p>
        </section>

        {/* High-level profile metadata formatted as a description list */}
        <dl className="grid grid-cols-2 gap-4 border-y border-zinc-200/70 py-6 sm:grid-cols-4 dark:border-zinc-800">
          <article className="group flex flex-col gap-1 transition-transform duration-200 hover:-translate-y-0.5">
            <dt className="text-xs font-semibold uppercase tracking-wider text-zinc-500 transition-colors duration-200 group-hover:text-blue-500 dark:text-zinc-400">
              Role
            </dt>
            <dd className="font-semibold text-zinc-900 dark:text-zinc-100">
              CS Student
            </dd>
          </article>
          <article className="group flex flex-col gap-1 transition-transform duration-200 hover:-translate-y-0.5">
            <dt className="text-xs font-semibold uppercase tracking-wider text-zinc-500 transition-colors duration-200 group-hover:text-blue-500 dark:text-zinc-400">
              Focus
            </dt>
            <dd className="font-semibold text-zinc-900 dark:text-zinc-100">
              Fullstack &amp; Systems
            </dd>
          </article>
          <article className="group flex flex-col gap-1 transition-transform duration-200 hover:-translate-y-0.5">
            <dt className="text-xs font-semibold uppercase tracking-wider text-zinc-500 transition-colors duration-200 group-hover:text-blue-500 dark:text-zinc-400">
              Methodology
            </dt>
            <dd className="font-semibold text-zinc-900 dark:text-zinc-100">
              Building in Public
            </dd>
          </article>
          <article className="group flex flex-col gap-1 transition-transform duration-200 hover:-translate-y-0.5">
            <dt className="text-xs font-semibold uppercase tracking-wider text-zinc-500 transition-colors duration-200 group-hover:text-blue-500 dark:text-zinc-400">
              Architecture
            </dt>
            <dd className="font-semibold text-zinc-900 dark:text-zinc-100">
              MVC &amp; Modular
            </dd>
          </article>
        </dl>

        <nav aria-label="Hero actions">
          <menu className="flex flex-wrap items-center gap-4">
            <li>
              <Link
                href="#projects"
                className="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-zinc-800 hover:scale-[1.02] active:scale-[0.98] dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-zinc-200"
              >
                Explore Active Projects →
              </Link>
            </li>
            <li>
              <Link
                href="#stack"
                className="inline-flex items-center justify-center rounded-xl border border-zinc-300 bg-white px-6 py-3 text-sm font-semibold text-zinc-800 shadow-sm transition-all duration-200 hover:border-zinc-400 hover:bg-zinc-50 hover:scale-[1.02] active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-zinc-600 dark:hover:bg-zinc-800"
              >
                View Tech Stack
              </Link>
            </li>
            <li>
              <Link
                href="#contact"
                className="inline-flex items-center justify-center px-4 py-3 text-sm font-semibold text-zinc-600 transition-colors duration-200 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100"
              >
                Get in Touch
              </Link>
            </li>
          </menu>
        </nav>
      </section>

      {/* 3. Core Philosophy / About Section */}
      <section
        id="about"
        aria-labelledby="about-title"
        className="animate-fade-in-up delay-200 flex flex-col gap-8 rounded-3xl border border-zinc-200/80 bg-white p-8 shadow-sm transition-all duration-300 sm:p-10 dark:border-zinc-800 dark:bg-zinc-900/50"
      >
        <header className="flex flex-col gap-3">
          <h2
            id="about-title"
            className="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl dark:text-zinc-50"
          >
            Philosophy &amp; Core Principles
          </h2>
          <p className="text-base text-zinc-600 dark:text-zinc-400">
            Guiding tenets shaping my learning path and software construction
            decisions.
          </p>
        </header>

        <section
          aria-label="Principles list"
          className="grid grid-cols-1 gap-6 md:grid-cols-3"
        >
          <article className="flex flex-col gap-3 rounded-2xl border border-zinc-100 bg-zinc-50/70 p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/30 dark:hover:border-zinc-700">
            <header>
              <h3 className="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                1. Semantic &amp; Accessible
              </h3>
            </header>
            <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
              Web standards exist for a reason. Prioritizing native HTML5
              elements, keyboard accessibility, and screen reader friendliness
              delivers better UX and resilience than heavy abstraction layers.
            </p>
          </article>

          <article className="flex flex-col gap-3 rounded-2xl border border-zinc-100 bg-zinc-50/70 p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/30 dark:hover:border-zinc-700">
            <header>
              <h3 className="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                2. Fundamentals First
              </h3>
            </header>
            <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
              Frameworks come and go, but protocols, data structures, type
              safety, and clean software architecture endure. Understanding the
              underpinnings builds lasting engineering adaptability.
            </p>
          </article>

          <article className="flex flex-col gap-3 rounded-2xl border border-zinc-100 bg-zinc-50/70 p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/30 dark:hover:border-zinc-700">
            <header>
              <h3 className="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                3. Open &amp; Collaborative
              </h3>
            </header>
            <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
              Sharing code, documenting failures, and accepting feedback in
              public accelerates growth. Transparent collaboration transforms
              personal experiments into community knowledge.
            </p>
          </article>
        </section>
      </section>

      {/* 4. Technical Focus & Stack
          Categorized technical skills with interactive <kbd> tokens.
      */}
      <section
        id="stack"
        aria-labelledby="stack-title"
        className="animate-fade-in-up delay-200 flex flex-col gap-8 rounded-3xl border border-zinc-200/80 bg-white p-8 shadow-sm transition-all duration-300 sm:p-10 dark:border-zinc-800 dark:bg-zinc-900/50"
      >
        <header className="flex flex-col gap-3">
          <h2
            id="stack-title"
            className="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl dark:text-zinc-50"
          >
            Technical Stack &amp; Tooling
          </h2>
          <p className="text-base text-zinc-600 dark:text-zinc-400">
            Technologies, runtimes, and engineering workflows currently deployed
            across projects.
          </p>
        </header>

        <section
          aria-label="Tech stack categories"
          className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
          <article className="flex flex-col gap-4 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/30 dark:hover:border-zinc-700">
            <header className="flex flex-col gap-1">
              <h3 className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                Frontend &amp; UI Architecture
              </h3>
              <p className="text-xs text-zinc-500 dark:text-zinc-400">
                High-performance user interfaces and responsive web design.
              </p>
            </header>
            <menu className="flex flex-wrap gap-2">
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Next.js 16 (App Router)
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  React 19
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  TypeScript
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Tailwind CSS v4
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Semantic HTML5 / WAI-ARIA
                </kbd>
              </li>
            </menu>
          </article>

          <article className="flex flex-col gap-4 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/30 dark:hover:border-zinc-700">
            <header className="flex flex-col gap-1">
              <h3 className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                Backend &amp; Data Layer
              </h3>
              <p className="text-xs text-zinc-500 dark:text-zinc-400">
                APIs, data models, and persistent storage solutions.
              </p>
            </header>
            <menu className="flex flex-wrap gap-2">
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Node.js / Bun
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  REST &amp; Route Handlers
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  PostgreSQL &amp; Prisma / Drizzle
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Redis Caching
                </kbd>
              </li>
            </menu>
          </article>

          <article className="flex flex-col gap-4 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/30 dark:hover:border-zinc-700">
            <header className="flex flex-col gap-1">
              <h3 className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                DevOps, Testing &amp; Environment
              </h3>
              <p className="text-xs text-zinc-500 dark:text-zinc-400">
                Developer productivity, version control, and delivery pipelines.
              </p>
            </header>
            <menu className="flex flex-wrap gap-2">
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Git &amp; GitHub Actions
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Linux (CLI / Bash)
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  Turbopack
                </kbd>
              </li>
              <li>
                <kbd className="rounded-md border border-zinc-200 bg-white px-2 py-1 font-mono text-xs font-medium text-zinc-800 shadow-xs transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/40 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-blue-500/50 dark:hover:bg-zinc-700">
                  ESLint &amp; Prettier
                </kbd>
              </li>
            </menu>
          </article>
        </section>
      </section>

      {/* 5. Active Projects
          Showcases the main open-source hub project with architecture and MVC status details.
      */}
      <section
        id="projects"
        aria-labelledby="projects-title"
        className="animate-fade-in-up delay-300 flex flex-col gap-8 rounded-3xl border border-zinc-200/80 bg-white p-8 shadow-sm transition-all duration-300 sm:p-10 dark:border-zinc-800 dark:bg-zinc-900/50"
      >
        <header className="flex flex-col gap-3">
          <h2
            id="projects-title"
            className="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl dark:text-zinc-50"
          >
            Active Project
          </h2>
          <p className="text-base text-zinc-600 dark:text-zinc-400">
            Core repository and architecture currently under active development.
          </p>
        </header>

        <section aria-label="Projects list" className="flex flex-col gap-6">
          {/* Project Card: Exon Community Hub */}
          <article className="group flex flex-col gap-5 rounded-2xl border border-zinc-200/70 bg-zinc-50/60 p-7 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-400 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-800/40 dark:hover:border-zinc-700">
            <header className="flex flex-col gap-3 sm:flex-row sm:items-baseline sm:justify-between">
              <h3 className="text-xl font-bold text-zinc-900 transition-colors duration-200 group-hover:text-blue-600 dark:text-zinc-100 dark:group-hover:text-blue-400">
                Exon | Community Hub
              </h3>
              <menu className="flex flex-wrap items-center gap-1.5">
                <li>
                  <kbd className="rounded-md border border-zinc-300 bg-white px-2 py-0.5 font-mono text-xs font-medium text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    Next.js 16
                  </kbd>
                </li>
                <li>
                  <kbd className="rounded-md border border-zinc-300 bg-white px-2 py-0.5 font-mono text-xs font-medium text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    TypeScript
                  </kbd>
                </li>
                <li>
                  <kbd className="rounded-md border border-zinc-300 bg-white px-2 py-0.5 font-mono text-xs font-medium text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    Tailwind CSS
                  </kbd>
                </li>
              </menu>
            </header>

            <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
              A clean, structured web platform designed as a central hub for
              student developers, technical notes, and open-source
              collaboration. Built with a modular foundation to support upcoming
              features and community contributions.
            </p>

            <dl className="grid grid-cols-1 gap-3 border-t border-zinc-200/60 pt-4 sm:grid-cols-3 dark:border-zinc-800/60">
              <article className="flex flex-col gap-0.5">
                <dt className="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                  Architecture
                </dt>
                <dd className="text-xs font-medium text-zinc-800 dark:text-zinc-200">
                  MVC Pattern &amp; Modular Structure
                </dd>
              </article>
              <article className="flex flex-col gap-0.5">
                <dt className="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                  Repository Type
                </dt>
                <dd className="text-xs font-medium text-zinc-800 dark:text-zinc-200">
                  Public Open-Source
                </dd>
              </article>
              <article className="flex flex-col gap-0.5">
                <dt className="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                  Current Phase
                </dt>
                <dd className="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                  Base MVC Ready
                </dd>
              </article>
            </dl>

            <footer>
              <a
                href="https://github.com/Exoncode-stream/Exon"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 text-sm font-semibold text-zinc-900 underline underline-offset-4 transition-all duration-200 hover:text-blue-600 hover:translate-x-1 dark:text-zinc-100 dark:hover:text-blue-400"
              >
                View GitHub Repository →
              </a>
            </footer>
          </article>
        </section>
      </section>

      {/* 6. Featured Deep-Dive Article
          In-depth technical article on Semantic HTML5 vs Classic Generic HTML.
          Published on August 21, 2026.
      */}
      <section
        id="article"
        aria-labelledby="article-heading"
        className="animate-fade-in-up delay-300 flex flex-col gap-8 rounded-3xl border border-zinc-200/80 bg-white p-8 shadow-sm transition-all duration-300 sm:p-12 dark:border-zinc-800 dark:bg-zinc-900/50"
      >
        <article className="flex flex-col gap-8">
          <header className="flex flex-col gap-4 border-b border-zinc-200/80 pb-6 dark:border-zinc-800">
            <nav
              aria-label="Article metadata"
              className="flex flex-wrap items-center gap-3"
            >
              <kbd className="rounded-md border border-blue-200 bg-blue-50/80 px-2.5 py-0.5 font-mono text-xs font-semibold text-blue-700 dark:border-blue-900/50 dark:bg-blue-950/40 dark:text-blue-300">
                Web Standards &amp; Architecture
              </kbd>
              {/* Canonical publication timestamp */}
              <time
                dateTime="2026-08-21"
                className="font-mono text-xs font-medium text-zinc-500 dark:text-zinc-400"
              >
                August 21, 2026 (21/08/2026)
              </time>
              <strong className="font-mono text-xs font-normal text-zinc-400 dark:text-zinc-500">
                • 5 min read
              </strong>
            </nav>

            <h2
              id="article-heading"
              className="text-3xl font-extrabold tracking-tight text-zinc-900 sm:text-4xl dark:text-zinc-50"
            >
              Why Semantic HTML5 Outperforms Traditional Generic Markup
            </h2>

            <p className="text-lg leading-relaxed text-zinc-700 dark:text-zinc-300">
              A comprehensive and straightforward breakdown of why using
              meaningful HTML5 tags instead of generic container tags transforms
              accessibility, search visibility, maintainability, and user
              experience.
            </p>
          </header>

          <section
            aria-label="Introduction"
            className="flex flex-col gap-4 text-base leading-relaxed text-zinc-700 dark:text-zinc-300"
          >
            <h3 className="text-xl font-bold text-zinc-900 dark:text-zinc-100">
              1. Understanding the Core Difference: What Is Semantic HTML?
            </h3>
            <p>
              In traditional, classic web development, developers often rely
              heavily on generic containers—primarily non-semantic elements like
              plain boxes to divide pages. The structure typically ends up
              looking like repetitive layers of generic containers
              differentiated only by CSS classes.
            </p>
            <p>
              While this approach visualizes properly on a screen, the browser
              and machine tools have no understanding of what each block
              actually represents.
            </p>
            <p>
              <strong>Semantic HTML5</strong>, introduced as a global standard,
              provides elements that explicitly state their meaning and purpose
              to the browser, search engines, and assistive devices. Instead of
              generic tags, we use descriptive building blocks such as{" "}
              <kbd className="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                &lt;header&gt;
              </kbd>
              ,{" "}
              <kbd className="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                &lt;nav&gt;
              </kbd>
              ,{" "}
              <kbd className="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                &lt;main&gt;
              </kbd>
              ,{" "}
              <kbd className="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                &lt;article&gt;
              </kbd>
              ,{" "}
              <kbd className="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                &lt;section&gt;
              </kbd>
              , and{" "}
              <kbd className="rounded border border-zinc-300 bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                &lt;footer&gt;
              </kbd>
              .
            </p>
          </section>

          <section aria-label="Key Advantages" className="flex flex-col gap-6">
            <h3 className="text-xl font-bold text-zinc-900 dark:text-zinc-100">
              2. Detailed Breakdown of Key Advantages
            </h3>

            <section className="grid grid-cols-1 gap-6 md:grid-cols-2">
              <article className="flex flex-col gap-3 rounded-2xl border border-zinc-200/70 bg-zinc-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-800/40">
                <header>
                  <h4 className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                    A. Native Accessibility &amp; Screen Readers
                  </h4>
                </header>
                <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                  Visually impaired users rely on screen readers to navigate the
                  web using keyboard shortcuts. Screen readers scan for native
                  landmarks like navigation, main content, and article headings.
                  When everything is built with generic tags, the accessibility
                  tree remains flat and meaningless, forcing users to listen to
                  every single word in sequence. Semantic HTML allows instant
                  navigation to the exact section needed.
                </p>
              </article>

              <article className="flex flex-col gap-3 rounded-2xl border border-zinc-200/70 bg-zinc-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-800/40">
                <header>
                  <h4 className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                    B. Search Engine Optimization (SEO) &amp; AI Crawlers
                  </h4>
                </header>
                <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                  Search engines like Google and modern AI indexing crawlers do
                  not merely read text; they rank content based on structural
                  hierarchy. Semantic tags clearly indicate what constitutes
                  primary editorial content (article), what is supporting
                  context (aside), and what is supplementary navigation (nav),
                  yielding significantly higher relevance scores and accurate
                  indexing.
                </p>
              </article>

              <article className="flex flex-col gap-3 rounded-2xl border border-zinc-200/70 bg-zinc-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-800/40">
                <header>
                  <h4 className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                    C. Code Maintainability &amp; Self-Documentation
                  </h4>
                </header>
                <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                  In a complex codebase, nested generic containers create visual
                  clutter that slows down debugging and onboarding. Semantic
                  markup is self-documenting: looking at an article element
                  immediately tells any developer that the block is a standalone
                  piece of content, without needing to decipher dozens of CSS
                  class names.
                </p>
              </article>

              <article className="flex flex-col gap-3 rounded-2xl border border-zinc-200/70 bg-zinc-50/70 p-6 dark:border-zinc-800 dark:bg-zinc-800/40">
                <header>
                  <h4 className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                    D. Native Behavior &amp; Reduced Bundle Weight
                  </h4>
                </header>
                <p className="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                  Native HTML5 interactive tags provide built-in keyboard
                  navigation (Tab focusing, Enter key activation, Escape
                  dismissal) without requiring complex JavaScript event
                  listeners or external accessibility polyfills. This results in
                  faster load times, smoother rendering, and fewer runtime
                  errors.
                </p>
              </article>
            </section>
          </section>

          {/* Side-by-side comparison summary */}
          <section
            aria-label="Practical Comparison"
            className="flex flex-col gap-4"
          >
            <h3 className="text-xl font-bold text-zinc-900 dark:text-zinc-100">
              3. Quick Practical Comparison
            </h3>

            <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <article className="flex flex-col gap-2 rounded-2xl border border-rose-200/70 bg-rose-50/40 p-5 dark:border-rose-950/60 dark:bg-rose-950/20">
                <dt className="font-mono text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">
                  Traditional Generic Approach
                </dt>
                <dd className="text-sm text-zinc-700 dark:text-zinc-300">
                  Relies on endless layers of anonymous boxes. Lacks built-in
                  keyboard landmarks and requires custom ARIA attributes to be
                  accessible.
                </dd>
              </article>

              <article className="flex flex-col gap-2 rounded-2xl border border-emerald-200/70 bg-emerald-50/40 p-5 dark:border-emerald-950/60 dark:bg-emerald-950/20">
                <dt className="font-mono text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                  Semantic HTML5 Approach
                </dt>
                <dd className="text-sm text-zinc-700 dark:text-zinc-300">
                  Utilizes meaningful structural landmarks. Inherently
                  accessible, easily indexed by machines, lightweight, and
                  cleanly organized.
                </dd>
              </article>
            </dl>
          </section>

          <footer className="border-t border-zinc-200/80 pt-6 text-sm text-zinc-600 dark:border-zinc-800 dark:text-zinc-400">
            <p>
              <strong>Conclusion:</strong> Writing semantic HTML5 is not an
              aesthetic preference—it is a fundamental software engineering
              discipline that ensures the web remains open, accessible, and
              easily discoverable by all users and tools.
            </p>
          </footer>
        </article>
      </section>

      {/* 7. Contact & Community Section
          NOTE: Uses semantic <address> for contact data without generic wrappers.
      */}
      <section
        id="contact"
        aria-labelledby="contact-title"
        className="animate-fade-in-up delay-400 flex flex-col gap-8 rounded-3xl border border-zinc-200/80 bg-white p-8 shadow-sm transition-all duration-300 sm:p-10 dark:border-zinc-800 dark:bg-zinc-900/50"
      >
        <header className="flex flex-col gap-3">
          <h2
            id="contact-title"
            className="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl dark:text-zinc-50"
          >
            Get in Touch &amp; Connect
          </h2>
          <p className="text-base text-zinc-600 dark:text-zinc-400">
            Whether you want to discuss a project, exchange ideas on software
            engineering, or just say hello, I&apos;m always happy to connect.
          </p>
        </header>

        <address className="not-italic">
          <ul className="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <li className="flex flex-col gap-1 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/40 dark:hover:border-zinc-700">
              <strong className="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                Discord
              </strong>
              <strong className="text-base font-bold text-zinc-900 dark:text-zinc-100">
                guiireg
              </strong>
            </li>
            <li className="flex flex-col gap-1 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/40 dark:hover:border-zinc-700">
              <strong className="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                GitHub
              </strong>
              <a
                href="https://github.com/guiiireg"
                target="_blank"
                rel="noopener noreferrer"
                className="text-base font-bold text-blue-600 underline underline-offset-4 transition-colors duration-200 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
              >
                @guiiireg
              </a>
            </li>
            <li className="flex flex-col gap-1 rounded-2xl border border-zinc-100 bg-zinc-50/80 p-5 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-800/40 dark:hover:border-zinc-700">
              <strong className="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                Email
              </strong>
              <a
                href="mailto:exon.code@proton.me"
                className="text-base font-bold text-blue-600 underline underline-offset-4 transition-colors duration-200 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
              >
                exon.code@proton.me
              </a>
            </li>
          </ul>
        </address>
      </section>

      {/* 8. Global Semantic Footer */}
      <footer
        aria-label="Site footer"
        className="flex flex-col items-center justify-between gap-4 border-t border-zinc-200/80 pt-8 text-xs text-zinc-500 transition-colors duration-300 sm:flex-row dark:border-zinc-800 dark:text-zinc-400"
      >
        <p className="flex items-center gap-2.5">
          <Image
            src="/web-logo.svg"
            alt="Exon logo"
            width={18}
            height={18}
            className="h-4.5 w-4.5 rounded-sm object-contain"
          />
          © 2026 Exon. Handcrafted with Next.js, TypeScript &amp; 100% semantic
          HTML5.
        </p>

        <nav aria-label="Footer quick navigation">
          <menu className="flex items-center gap-4">
            <li>
              <Link
                href="#hero-title"
                className="transition-colors duration-200 hover:underline hover:text-zinc-900 dark:hover:text-zinc-100"
              >
                Back to top ↑
              </Link>
            </li>
            <li>
              <a
                href="https://github.com/Exoncode-stream/Exon"
                target="_blank"
                rel="noopener noreferrer"
                className="transition-colors duration-200 hover:underline hover:text-zinc-900 dark:hover:text-zinc-100"
              >
                Source Code
              </a>
            </li>
          </menu>
        </nav>
      </footer>
    </main>
  );
}
