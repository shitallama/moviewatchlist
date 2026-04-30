<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="assets/indexstyle.css">
</head>
<body>
    <div class="container">
  <!-- clean navbar with secondary login -->
  <div class="navbar">
    <div class="logo">🎬 WatchSync</div>
    <button class="nav-login" id="loginNavBtn">Log in</button>
  </div>

  <!-- above the fold: headline + value -->
  <div class="hero">
    <h1>Never forget a <span style="background: linear-gradient(145deg,#D43F1F,#E07A3A); background-clip:text; -webkit-background-clip:text; color:transparent;">movie</span> you want to watch</h1>
    <div class="hero-sub">
      Save, organize, and rate movies in one simple place.
    </div>
    <div class="btn-group">
      <button class="btn-primary" id="createWatchlistBtn">✨ Create your watchlist</button>
      <button class="btn-secondary" id="loginSecondaryBtn">Log in</button>
    </div>
  </div>

  <!-- product preview: movie cards with posters -->
  <div class="preview">
    <div class="preview-header">
      <p>✦ YOUR FUTURE WATCHLIST ✦</p>
    </div>
    <div class="movie-grid">
      <div class="movie-card">
        <div class="movie-poster">🎞️</div>
        <div class="movie-info">
          <div class="movie-title">Dune: Part Two</div>
          <div class="movie-year">2024</div>
          <div class="rating">⭐ ★★★★☆</div>
        </div>
      </div>
      <div class="movie-card">
        <div class="movie-poster">🍿</div>
        <div class="movie-info">
          <div class="movie-title">Past Lives</div>
          <div class="movie-year">2023</div>
          <div class="rating">⭐ ★★★★★</div>
        </div>
      </div>
      <div class="movie-card">
        <div class="movie-poster">🌌</div>
        <div class="movie-info">
          <div class="movie-title">The Boy and the Heron</div>
          <div class="movie-year">2023</div>
          <div class="rating">⭐ ★★★★½</div>
        </div>
      </div>
      <div class="movie-card">
        <div class="movie-poster">🔥</div>
        <div class="movie-info">
          <div class="movie-title">Anatomy of a Fall</div>
          <div class="movie-year">2023</div>
          <div class="rating">⭐ ★★★★☆</div>
        </div>
      </div>
      <div class="movie-card">
        <div class="movie-poster">🤖</div>
        <div class="movie-info">
          <div class="movie-title">Robot Dreams</div>
          <div class="movie-year">2023</div>
          <div class="rating">⭐ ★★★★½</div>
        </div>
      </div>
      <div class="movie-card">
        <div class="movie-poster">🌊</div>
        <div class="movie-info">
          <div class="movie-title">Perfect Days</div>
          <div class="movie-year">2023</div>
          <div class="rating">⭐ ★★★★★</div>
        </div>
      </div>
    </div>
  </div>

  <!-- differentiation: why not just notes? -->
  <div class="why-section">
    <div class="why-card">
      <div class="why-icon">🎯</div>
      <h3>Built for movies</h3>
      <p>Not messy notes or random docs — every field is made for film lovers.</p>
    </div>
    <div class="why-card">
      <div class="why-icon">⭐</div>
      <h3>Rate & track</h3>
      <p>Keep a personal log of what you’ve watched, and what’s next.</p>
    </div>
    <div class="why-card">
      <div class="why-icon">📂</div>
      <h3>Organize without overthinking</h3>
      <p>Smart lists & folders, zero clutter.</p>
    </div>
  </div>

  <!-- trust signals: simple and reassuring -->
  <div class="trust-badge">
    <div class="trust-item">🔒 No spam. Just your personal watchlist.</div>
    <div class="trust-item">🛡️ Your data stays private</div>
    <div class="trust-item">📧 One-click signup — no hidden fees</div>
  </div>

  <!-- emotional hook: stop scrolling ➔ start watching -->
  <div class="emotional-hook">
    <p>“That movie your friend told you about? It won’t be lost anymore.”</p>
    <small>✨ Stop scrolling. Start watching. ✨</small>
  </div>

  <footer>
    © WatchSync — Your movie companion
  </footer>
</div>

<!-- simple demo modal (login & watchlist notification) -->
<div id="demoModal" class="modal">
  <div class="modal-content">
    <h3 id="modalTitle">🎬 Welcome</h3>
    <p id="modalMessage">You're one step away from organizing your movie world. Create your watchlist now — it's free and takes 10 seconds.</p>
    <button class="close-modal" id="closeModalBtn">Got it</button>
  </div>
</div>
</body>
</html>