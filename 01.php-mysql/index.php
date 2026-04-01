<?php
require_once 'config/session.php';
// If already logged in, go straight to profile
if (isLoggedIn()) {
    redirect('/profile.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Portal — University Academic System</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
  --bg:      #0a0c10;
  --surface: #11141a;
  --surface2:#161b24;
  --border:  #1e2430;
  --gold:    #c9a84c;
  --gold2:   #e8c97a;
  --text:    #e8e4d9;
  --muted:   #6b7280;
  --dim:     #3a4050;
  --green:   #4caf7d;
}

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

html { scroll-behavior: smooth; }

body {
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  overflow-x: hidden;
}

/* ── NOISE TEXTURE OVERLAY ─────────────────────────────────── */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
  pointer-events: none;
}

/* ── GRID BACKGROUND ───────────────────────────────────────── */
.grid-bg {
  position: fixed; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(201,168,76,.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,168,76,.025) 1px, transparent 1px);
  background-size: 60px 60px;
  pointer-events: none;
}

/* ── GLOW ORBS ─────────────────────────────────────────────── */
.orb {
  position: fixed; border-radius: 50%;
  filter: blur(100px); pointer-events: none; z-index: 0;
}
.orb1 { width:500px; height:500px; background:rgba(201,168,76,.055); top:-100px; left:-100px; }
.orb2 { width:400px; height:400px; background:rgba(76,175,125,.04);  bottom:-80px; right:-80px; }
.orb3 { width:300px; height:300px; background:rgba(201,168,76,.03);  top:50%; left:50%; transform:translate(-50%,-50%); }

/* ── NAV ───────────────────────────────────────────────────── */
nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 100;
  height: 66px;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 52px;
  background: rgba(10,12,16,.85);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(201,168,76,.1);
}

.nav-logo {
  display: flex; align-items: center; gap: 12px;
  font-family: 'Playfair Display', serif;
  font-size: 18px; color: var(--text);
  text-decoration: none;
}

.nav-hex {
  width: 34px; height: 34px;
  border: 1.5px solid var(--gold);
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; color: var(--gold);
  flex-shrink: 0;
}

.nav-links { display: flex; align-items: center; gap: 8px; }

.nav-link {
  padding: 8px 18px;
  font-size: 13px; font-weight: 500;
  color: var(--muted); text-decoration: none;
  border: 1px solid transparent;
  transition: color .2s, border-color .2s;
}
.nav-link:hover { color: var(--text); }

.nav-cta {
  padding: 9px 22px;
  background: var(--gold); color: #0a0c10;
  font-size: 13px; font-weight: 700;
  letter-spacing: 1px; text-transform: uppercase;
  text-decoration: none;
  border: none; cursor: pointer;
  transition: background .2s;
  display: inline-block;
}
.nav-cta:hover { background: var(--gold2); }

/* ── HERO ──────────────────────────────────────────────────── */
.hero {
  position: relative; z-index: 1;
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  padding: 120px 52px 80px;
  text-align: center;
}

.hero-inner { max-width: 800px; }

.hero-badge {
  display: inline-flex; align-items: center; gap: 8px;
  border: 1px solid rgba(201,168,76,.3);
  background: rgba(201,168,76,.06);
  padding: 6px 16px;
  font-size: 11px; letter-spacing: 2px;
  text-transform: uppercase; color: var(--gold);
  margin-bottom: 32px;
  animation: fadeUp .6s ease both;
}

.hero-badge span { width:6px; height:6px; background:var(--gold); border-radius:50%; display:inline-block; }

.hero h1 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(42px, 7vw, 76px);
  font-weight: 700; line-height: 1.08;
  margin-bottom: 24px;
  animation: fadeUp .6s .1s ease both; opacity: 0;
}

.hero h1 em { font-style: italic; color: var(--gold); font-weight: 600; }

.hero p {
  font-size: 18px; line-height: 1.8;
  color: var(--muted); max-width: 560px;
  margin: 0 auto 44px;
  animation: fadeUp .6s .2s ease both; opacity: 0;
}

.hero-btns {
  display: flex; align-items: center; justify-content: center; gap: 14px;
  flex-wrap: wrap;
  animation: fadeUp .6s .3s ease both; opacity: 0;
}

.btn-primary {
  padding: 14px 36px;
  background: var(--gold); color: #0a0c10;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px; font-weight: 700;
  letter-spacing: 1.5px; text-transform: uppercase;
  text-decoration: none; border: none; cursor: pointer;
  transition: background .2s, transform .1s;
  display: inline-block;
}
.btn-primary:hover  { background: var(--gold2); }
.btn-primary:active { transform: scale(.99); }

.btn-secondary {
  padding: 14px 36px;
  background: transparent; color: var(--text);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px; font-weight: 500;
  letter-spacing: .5px;
  text-decoration: none;
  border: 1px solid var(--border);
  transition: border-color .2s, color .2s;
  display: inline-block;
}
.btn-secondary:hover { border-color: var(--gold); color: var(--gold); }

/* scroll hint */
.scroll-hint {
  position: absolute; bottom: 36px; left: 50%; transform: translateX(-50%);
  display: flex; flex-direction: column; align-items: center; gap: 8px;
  font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--dim);
  animation: fadeUp .6s .6s ease both; opacity: 0;
}
.scroll-hint::after {
  content: '';
  width: 1px; height: 40px;
  background: linear-gradient(var(--gold), transparent);
  animation: pulse 2s ease-in-out infinite;
}

/* ── STATS BAR ─────────────────────────────────────────────── */
.stats-bar {
  position: relative; z-index: 1;
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  background: var(--surface);
  display: grid; grid-template-columns: repeat(4, 1fr);
  overflow: hidden;
}

.stat {
  padding: 36px 40px;
  border-right: 1px solid var(--border);
  text-align: center;
}
.stat:last-child { border-right: none; }

.stat-n {
  font-family: 'Playfair Display', serif;
  font-size: 40px; font-weight: 700;
  color: var(--gold); line-height: 1;
  margin-bottom: 6px;
}
.stat-l {
  font-size: 11.5px; color: var(--muted);
  text-transform: uppercase; letter-spacing: 1.5px;
}

/* ── FEATURES ──────────────────────────────────────────────── */
.section {
  position: relative; z-index: 1;
  padding: 100px 52px;
  max-width: 1120px; margin: 0 auto;
}

.sec-tag {
  font-size: 10.5px; letter-spacing: 2.5px;
  text-transform: uppercase; color: var(--gold);
  margin-bottom: 14px;
}

.sec-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(28px, 4vw, 44px);
  font-weight: 700; line-height: 1.2;
  margin-bottom: 16px;
}

.sec-title span { color: var(--gold); }

.sec-sub {
  color: var(--muted); font-size: 16px; line-height: 1.8;
  max-width: 520px; margin-bottom: 60px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1px;
  background: var(--border);
  border: 1px solid var(--border);
}

.feat {
  background: var(--surface);
  padding: 36px 32px;
  transition: background .25s;
  position: relative; overflow: hidden;
}

.feat::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, var(--gold), transparent);
  opacity: 0; transition: opacity .3s;
}

.feat:hover { background: var(--surface2); }
.feat:hover::before { opacity: 1; }

.feat-icon {
  width: 44px; height: 44px;
  border: 1px solid rgba(201,168,76,.3);
  background: rgba(201,168,76,.06);
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; margin-bottom: 20px;
  transition: border-color .3s, background .3s;
}
.feat:hover .feat-icon { border-color: var(--gold); background: rgba(201,168,76,.12); }

.feat h3 {
  font-family: 'Playfair Display', serif;
  font-size: 18px; margin-bottom: 10px;
}
.feat p { font-size: 13.5px; color: var(--muted); line-height: 1.75; }

/* ── HOW IT WORKS ──────────────────────────────────────────── */
.how-section {
  position: relative; z-index: 1;
  background: var(--surface);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 100px 52px;
}

.how-inner { max-width: 1120px; margin: 0 auto; }

.steps-row {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 0; position: relative;
  margin-top: 60px;
}

/* connecting line between steps */
.steps-row::before {
  content: '';
  position: absolute;
  top: 28px; left: 14%; right: 14%;
  height: 1px;
  background: linear-gradient(90deg,
    transparent, var(--gold), var(--gold), var(--gold), transparent);
  opacity: .25;
}

.step-item { padding: 0 24px; text-align: center; position: relative; }

.step-num {
  width: 56px; height: 56px;
  border: 1.5px solid var(--gold);
  background: var(--bg);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Playfair Display', serif;
  font-size: 20px; color: var(--gold);
  margin: 0 auto 22px;
  position: relative; z-index: 1;
}

.step-item h4 {
  font-family: 'Playfair Display', serif;
  font-size: 17px; margin-bottom: 10px;
}
.step-item p { font-size: 13px; color: var(--muted); line-height: 1.7; }

/* ── CTA SECTION ───────────────────────────────────────────── */
.cta-section {
  position: relative; z-index: 1;
  padding: 120px 52px;
  text-align: center;
  overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse at 50% 50%, rgba(201,168,76,.07) 0%, transparent 65%);
  pointer-events: none;
}

.cta-section .sec-tag  { text-align: center; }
.cta-section .sec-title { text-align: center; max-width: 600px; margin: 0 auto 16px; }
.cta-section .sec-sub   { text-align: center; margin: 0 auto 44px; }

.cta-btns {
  display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;
}

/* ── FOOTER ────────────────────────────────────────────────── */
footer {
  position: relative; z-index: 1;
  background: var(--surface);
  border-top: 1px solid var(--border);
  padding: 40px 52px;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 16px;
}

.footer-brand {
  display: flex; align-items: center; gap: 10px;
  font-family: 'Playfair Display', serif;
  font-size: 16px;
}

.footer-copy { font-size: 12.5px; color: var(--muted); }

.footer-links { display: flex; gap: 22px; }
.footer-links a {
  font-size: 12.5px; color: var(--muted);
  text-decoration: none; transition: color .2s;
}
.footer-links a:hover { color: var(--gold); }

/* ── ANIMATIONS ────────────────────────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: none; }
}

@keyframes pulse {
  0%, 100% { opacity: .3; }
  50%       { opacity: 1; }
}

/* scroll-reveal */
.reveal {
  opacity: 0; transform: translateY(24px);
  transition: opacity .6s ease, transform .6s ease;
}
.reveal.visible { opacity: 1; transform: none; }
.reveal.d1 { transition-delay: .1s; }
.reveal.d2 { transition-delay: .2s; }
.reveal.d3 { transition-delay: .3s; }
.reveal.d4 { transition-delay: .4s; }

/* ── RESPONSIVE ────────────────────────────────────────────── */
@media (max-width: 900px) {
  nav { padding: 0 22px; }
  .hero { padding: 110px 24px 70px; }
  .stats-bar { grid-template-columns: repeat(2, 1fr); }
  .stat { border-bottom: 1px solid var(--border); }
  .features-grid { grid-template-columns: 1fr; }
  .steps-row { grid-template-columns: 1fr 1fr; gap: 40px; }
  .steps-row::before { display: none; }
  .section, .how-section, .cta-section { padding: 64px 24px; }
  footer { padding: 32px 22px; flex-direction: column; align-items: flex-start; }
}

@media (max-width: 540px) {
  .stats-bar { grid-template-columns: 1fr 1fr; }
  .steps-row { grid-template-columns: 1fr; }
  .nav-links .nav-link { display: none; }
}
</style>
</head>
<body>

<div class="grid-bg"></div>
<div class="orb orb1"></div>
<div class="orb orb2"></div>
<div class="orb orb3"></div>

<!-- ── NAV ──────────────────────────────────────────────────── -->
<nav>
  <a href="/index.php" class="nav-logo">
    <div class="nav-hex">⬡</div>
    Student Portal
  </a>
  <div class="nav-links">
    <a href="#features" class="nav-link">Features</a>
    <a href="#how"      class="nav-link">How It Works</a>
    <a href="/login.php"    class="nav-link">Sign In</a>
    <a href="/register.php" class="nav-cta">Register</a>
  </div>
</nav>

<!-- ── HERO ─────────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-badge">
      <span></span>
      Academic Management System
    </div>

    <h1>
      Your Academic<br>
      Journey, <em>Simplified</em>
    </h1>

    <p>
      One platform to manage your student profile, track academic progress,
      and keep your records up to date — built for the modern university.
    </p>

    <div class="hero-btns">
      <a href="/register.php" class="btn-primary">Create Account</a>
      <a href="/login.php"    class="btn-secondary">Sign In →</a>
    </div>
  </div>

  <div class="scroll-hint">Explore</div>
</section>

<!-- ── STATS BAR ─────────────────────────────────────────────── -->
<div class="stats-bar">
  <div class="stat reveal">
    <div class="stat-n" data-target="2400">0</div>
    <div class="stat-l">Enrolled Students</div>
  </div>
  <div class="stat reveal d1">
    <div class="stat-n" data-target="48">0</div>
    <div class="stat-l">Departments</div>
  </div>
  <div class="stat reveal d2">
    <div class="stat-n" data-target="99">0</div>
    <div class="stat-l">% Uptime</div>
  </div>
  <div class="stat reveal d3">
    <div class="stat-n" data-target="12">0</div>
    <div class="stat-l">Years Running</div>
  </div>
</div>

<!-- ── FEATURES ──────────────────────────────────────────────── -->
<section class="section" id="features">
  <div class="sec-tag reveal">Platform Features</div>
  <h2 class="sec-title reveal d1">Everything you need<br><span>in one place</span></h2>
  <p class="sec-sub reveal d2">Manage your academic identity with a clean, secure, and intuitive interface designed for students.</p>

  <div class="features-grid">
    <div class="feat reveal">
      <div class="feat-icon">👤</div>
      <h3>Student Profile</h3>
      <p>Maintain your personal details, contact info, and academic record in a single, organised profile.</p>
    </div>
    <div class="feat reveal d1">
      <div class="feat-icon">📸</div>
      <h3>Photo Upload</h3>
      <p>Upload and update your official profile picture anytime. Supports JPG, PNG, GIF and WEBP formats.</p>
    </div>
    <div class="feat reveal d2">
      <div class="feat-icon">🎓</div>
      <h3>Academic Tracking</h3>
      <p>Track your department, year of study, GPA, and enrollment date — all visible at a glance.</p>
    </div>
    <div class="feat reveal">
      <div class="feat-icon">🔐</div>
      <h3>Secure Login</h3>
      <p>Industry-standard bcrypt password hashing and session management keep your account safe.</p>
    </div>
    <div class="feat reveal d1">
      <div class="feat-icon">🪪</div>
      <h3>Student ID</h3>
      <p>Receive an auto-generated unique Student ID (e.g. STU-2026-0001) upon registration.</p>
    </div>
    <div class="feat reveal d2">
      <div class="feat-icon">📱</div>
      <h3>Fully Responsive</h3>
      <p>Access and update your profile from any device — desktop, tablet or mobile.</p>
    </div>
  </div>
</section>

<!-- ── HOW IT WORKS ───────────────────────────────────────────── -->
<section class="how-section" id="how">
  <div class="how-inner">
    <div class="sec-tag reveal">Get Started</div>
    <h2 class="sec-title reveal d1">Up and running<br><span>in four steps</span></h2>

    <div class="steps-row">
      <div class="step-item reveal">
        <div class="step-num">01</div>
        <h4>Register</h4>
        <p>Create your account with your name, email, department and a secure password.</p>
      </div>
      <div class="step-item reveal d1">
        <div class="step-num">02</div>
        <h4>Get Your ID</h4>
        <p>Your unique Student ID is generated automatically on successful registration.</p>
      </div>
      <div class="step-item reveal d2">
        <div class="step-num">03</div>
        <h4>Sign In</h4>
        <p>Log in using your username or email and password to access your dashboard.</p>
      </div>
      <div class="step-item reveal d3">
        <div class="step-num">04</div>
        <h4>Manage Profile</h4>
        <p>Update your details, upload a photo, and keep your academic record current.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────── -->
<section class="cta-section">
  <div class="sec-tag reveal">Join Today</div>
  <h2 class="sec-title reveal d1">Ready to get<br><span>started?</span></h2>
  <p class="sec-sub reveal d2">
    Registration takes under two minutes. Your Student ID is waiting.
  </p>
  <div class="cta-btns reveal d3">
    <a href="/register.php" class="btn-primary">Create Your Account</a>
    <a href="/login.php"    class="btn-secondary">Already registered? Sign in</a>
  </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────────────── -->
<footer>
  <div class="footer-brand">
    <div class="nav-hex" style="width:28px;height:28px;font-size:12px">⬡</div>
    Student Portal
  </div>
  <div class="footer-copy">© <?= date('Y') ?> Student Portal. All rights reserved.</div>
  <div class="footer-links">
    <a href="/register.php">Register</a>
    <a href="/login.php">Login</a>
  </div>
</footer>

<script>
// ── Scroll-reveal ──────────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ── Counter animation ──────────────────────────────────────────
function animateCounter(el) {
  const target = parseInt(el.dataset.target);
  const suffix = el.dataset.target >= 99 ? '%' : '+';
  const duration = 1600;
  const step = 16;
  const increment = target / (duration / step);
  let current = 0;

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      current = target;
      clearInterval(timer);
    }
    el.textContent = Math.floor(current).toLocaleString() + suffix;
  }, step);
}

// Trigger counters when stats bar enters view
const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.querySelectorAll('[data-target]').forEach(animateCounter);
      counterObserver.unobserve(e.target);
    }
  });
}, { threshold: 0.3 });

const statsBar = document.querySelector('.stats-bar');
if (statsBar) counterObserver.observe(statsBar);

// ── Smooth nav shrink on scroll ────────────────────────────────
const nav = document.querySelector('nav');
window.addEventListener('scroll', () => {
  if (window.scrollY > 60) {
    nav.style.height = '54px';
    nav.style.borderBottomColor = 'rgba(201,168,76,.15)';
  } else {
    nav.style.height = '66px';
    nav.style.borderBottomColor = 'rgba(201,168,76,.1)';
  }
}, { passive: true });
</script>
</body>
</html>

