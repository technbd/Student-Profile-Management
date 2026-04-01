<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Student Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#0a0c10;--surface:#11141a;--border:#1e2430;
  --gold:#c9a84c;--gold2:#e8c97a;
  --text:#e8e4d9;--muted:#6b7280;
  --red:#e05c5c;--green:#4caf7d;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}

/* LEFT */
.l{
  flex:1;display:flex;flex-direction:column;justify-content:center;
  padding:64px;position:relative;overflow:hidden;
}
.l::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 20% 50%,rgba(201,168,76,.06) 0%,transparent 60%);
}
.grid{
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(201,168,76,.03) 1px,transparent 1px),
    linear-gradient(90deg,rgba(201,168,76,.03) 1px,transparent 1px);
  background-size:52px 52px;
}
.brand{position:relative;z-index:1}
.hex{
  width:52px;height:52px;border:1.5px solid var(--gold);
  display:flex;align-items:center;justify-content:center;
  font-size:22px;color:var(--gold);margin-bottom:28px;
}
.brand h1{font-family:'Playfair Display',serif;font-size:44px;line-height:1.1;margin-bottom:14px}
.brand h1 span{color:var(--gold)}
.brand p{color:var(--muted);font-size:15px;line-height:1.8;max-width:360px}
.line{width:56px;height:2px;background:var(--gold);margin:26px 0}
.stats{display:flex;gap:44px;margin-top:52px}
.stat-n{font-family:'Playfair Display',serif;font-size:28px;color:var(--gold)}
.stat-l{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-top:2px}

/* RIGHT */
.r{
  width:460px;background:var(--surface);
  border-left:1px solid var(--border);
  display:flex;flex-direction:column;justify-content:center;
  padding:60px 48px;
}
.fh{margin-bottom:36px}
.fh .tag{font-size:10.5px;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:8px}
.fh h2{font-family:'Playfair Display',serif;font-size:28px}

.fg{margin-bottom:20px}
label{display:block;font-size:10.5px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:7px}
input{
  width:100%;padding:12px 14px;
  background:var(--bg);border:1px solid var(--border);
  color:var(--text);font-family:'DM Sans',sans-serif;font-size:14.5px;
  outline:none;transition:border-color .2s,box-shadow .2s;
}
input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.07)}
input::placeholder{color:#2c3040}

.pw-wrap{position:relative}
.eye{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:var(--muted);font-size:15px;padding:0;
}

.alert{padding:12px 15px;margin-bottom:20px;font-size:13.5px;display:none}
.alert.err{background:rgba(224,92,92,.1);border-left:3px solid var(--red);color:var(--red);display:block}
.alert.ok{background:rgba(76,175,125,.1);border-left:3px solid var(--green);color:var(--green);display:block}

.btn{
  width:100%;padding:14px;background:var(--gold);
  color:#0a0c10;border:none;font-family:'DM Sans',sans-serif;
  font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
  cursor:pointer;transition:background .2s,transform .1s;
}
.btn:hover{background:var(--gold2)}
.btn:active{transform:scale(.99)}
.btn:disabled{opacity:.6;cursor:not-allowed}

.links{display:flex;justify-content:space-between;margin-top:22px;font-size:13px}
.links a{color:var(--gold);text-decoration:none;font-weight:500}
.links a:hover{text-decoration:underline}

.sep{display:flex;align-items:center;gap:12px;margin:28px 0;color:var(--border)}
.sep::before,.sep::after{content:'';flex:1;height:1px;background:var(--border)}
.sep span{font-size:12px;color:var(--muted)}

.admin-box{
  padding:14px 16px;border:1px dashed var(--border);
  font-size:12.5px;color:var(--muted);
}
.admin-box strong{
  display:block;font-size:10px;letter-spacing:1px;
  text-transform:uppercase;color:var(--text);margin-bottom:6px;
}
.cr{display:flex;justify-content:space-between;padding:3px 0}
.cr span:last-child{color:var(--gold);font-family:monospace}

@keyframes fu{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.r>*{animation:fu .4s ease both;opacity:0}
.r>*:nth-child(1){animation-delay:.05s}
.r>*:nth-child(2){animation-delay:.12s}
.r>*:nth-child(3){animation-delay:.18s}
.r>*:nth-child(4){animation-delay:.22s}
.r>*:nth-child(5){animation-delay:.26s}

@media(max-width:860px){.l{display:none}.r{width:100%;padding:44px 24px}}
</style>
</head>
<body>

<!-- LEFT -->
<div class="l">
  <div class="grid"></div>
  <div class="brand">
    <div class="hex">⬡</div>
    <h1>Student<br><span>Portal</span></h1>
    <div class="line"></div>
    <p>Access your academic profile, manage your information, and track your progress — all in one place.</p>
    <div class="stats">
      <div><div class="stat-n">2,400+</div><div class="stat-l">Students</div></div>
      <div><div class="stat-n">48</div><div class="stat-l">Departments</div></div>
      <div><div class="stat-n">99.9%</div><div class="stat-l">Uptime</div></div>
    </div>
  </div>
</div>

<!-- RIGHT -->
<div class="r">
  <div class="fh">
    <p class="tag">Welcome back</p>
    <h2>Sign In</h2>
  </div>

  <div id="alert" class="alert"></div>

  <form id="form" novalidate onsubmit="doLogin(event)">
    <div class="fg">
      <label>Username or Email</label>
      <input id="username" type="text" placeholder="Enter username or email" autocomplete="username">
    </div>
    <div class="fg">
      <label>Password</label>
      <div class="pw-wrap">
        <input id="password" type="password" placeholder="Enter your password" autocomplete="current-password">
        <button type="button" class="eye" onclick="toggleEye()">👁</button>
      </div>
    </div>
    <button class="btn" id="btn" type="submit">Sign In</button>
  </form>

  <div class="links">
    <a href="/register.php">← Create new account</a>
  </div>

  <div class="sep"><span>admin demo</span></div>

  <div class="admin-box">
    <strong>Demo Admin Login</strong>
    <div class="cr"><span>Username</span><span>admin</span></div>
    <div class="cr"><span>Password</span><span>Admin@1234</span></div>
  </div>
</div>

<script>
function toggleEye() {
  const i = document.getElementById('password');
  i.type = i.type === 'password' ? 'text' : 'password';
}

async function doLogin(e) {
  e.preventDefault();
  const al  = document.getElementById('alert');
  const btn = document.getElementById('btn');
  al.className = 'alert';

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;

  if (!username || !password) {
    al.className = 'alert err';
    al.textContent = 'Please enter your username/email and password.';
    return;
  }

  btn.textContent = 'Signing in…'; btn.disabled = true;

  try {
    const res  = await fetch('/api/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });
    const data = await res.json();

    if (data.success) {
      al.className = 'alert ok';
      al.textContent = 'Login successful! Redirecting…';
      setTimeout(() => { window.location.href = '/profile.php'; }, 700);
    } else {
      al.className = 'alert err';
      al.textContent = data.message || 'Login failed.';
      btn.textContent = 'Sign In'; btn.disabled = false;
    }
  } catch {
    al.className = 'alert err';
    al.textContent = 'Connection error — is the server running?';
    btn.textContent = 'Sign In'; btn.disabled = false;
  }
}
</script>
</body>
</html>

