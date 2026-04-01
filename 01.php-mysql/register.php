<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — Student Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#0a0c10;--surface:#11141a;--border:#1e2430;
  --gold:#c9a84c;--gold2:#e8c97a;
  --text:#e8e4d9;--muted:#6b7280;
  --red:#e05c5c;--green:#4caf7d;--amber:#e09a3a;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}

/* ── LEFT PANEL ── */
.l{
  flex:1;display:flex;flex-direction:column;justify-content:center;
  padding:64px;position:relative;overflow:hidden;
}
.l::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 15% 55%,rgba(201,168,76,.07) 0%,transparent 60%);
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
.brand h1{
  font-family:'Playfair Display',serif;font-size:40px;
  line-height:1.15;margin-bottom:14px;
}
.brand h1 span{color:var(--gold)}
.brand p{color:var(--muted);font-size:14.5px;line-height:1.8;max-width:340px}
.line{width:56px;height:2px;background:var(--gold);margin:26px 0}

.steps{display:flex;flex-direction:column;gap:16px;margin-top:8px}
.step{display:flex;gap:14px;align-items:flex-start}
.sn{
  width:26px;height:26px;flex-shrink:0;
  border:1px solid var(--gold);
  display:flex;align-items:center;justify-content:center;
  font-size:10px;color:var(--gold);font-weight:700;letter-spacing:.5px;margin-top:2px;
}
.st strong{display:block;font-size:13.5px;color:var(--text);margin-bottom:2px}
.st{font-size:12.5px;color:var(--muted);line-height:1.6}

/* ── RIGHT PANEL ── */
.r{
  width:540px;background:var(--surface);
  border-left:1px solid var(--border);
  overflow-y:auto;padding:52px 48px 64px;
}
.fh{margin-bottom:28px}
.fh .tag{font-size:10.5px;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:8px}
.fh h2{font-family:'Playfair Display',serif;font-size:26px;font-weight:700}
.fh p{color:var(--muted);font-size:13px;margin-top:5px}

/* section dividers */
.sec{
  font-size:10px;letter-spacing:2px;text-transform:uppercase;
  color:var(--gold);display:flex;align-items:center;gap:10px;
  margin:26px 0 18px;
}
.sec::after{content:'';flex:1;height:1px;background:var(--border)}

/* form */
.row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.row.one{grid-template-columns:1fr}
.fg{}
label{display:block;font-size:10.5px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
input,select,textarea{
  width:100%;padding:11px 13px;
  background:var(--bg);border:1px solid var(--border);
  color:var(--text);font-family:'DM Sans',sans-serif;font-size:14px;
  outline:none;transition:border-color .2s,box-shadow .2s;
}
input:focus,select:focus,textarea:focus{
  border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.07)
}
input::placeholder{color:#2c3040}
select option{background:var(--surface)}
textarea{resize:vertical;min-height:70px}

/* pw */
.pw-wrap{position:relative}
.eye{
  position:absolute;right:11px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:var(--muted);font-size:15px;padding:0;line-height:1;
}
.bar{display:flex;gap:3px;margin-top:6px}
.bar span{flex:1;height:3px;background:var(--border);transition:background .3s}
.bar.s1 span:nth-child(1){background:var(--red)}
.bar.s2 span:nth-child(-n+2){background:var(--amber)}
.bar.s3 span:nth-child(-n+3){background:var(--gold)}
.bar.s4 span{background:var(--green)}
.hint{font-size:11px;color:var(--muted);margin-top:4px}

/* validation */
input.ok,select.ok{border-color:rgba(76,175,125,.45)}
input.er,select.er{border-color:rgba(224,92,92,.45)}
.ferr{font-size:11px;color:var(--red);margin-top:4px;display:none}
.ferr.on{display:block}

/* alert */
.alert{padding:12px 15px;margin-bottom:18px;font-size:13.5px;display:none}
.alert.err{background:rgba(224,92,92,.1);border-left:3px solid var(--red);color:var(--red);display:block}
.alert.ok{background:rgba(76,175,125,.1);border-left:3px solid var(--green);color:var(--green);display:block}

/* button */
.btn{
  width:100%;padding:13px;background:var(--gold);
  color:#0a0c10;border:none;font-family:'DM Sans',sans-serif;
  font-size:13px;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;cursor:pointer;margin-top:10px;
  transition:background .2s,transform .1s;
}
.btn:hover{background:var(--gold2)}
.btn:active{transform:scale(.99)}
.btn:disabled{opacity:.6;cursor:not-allowed}

.bot{text-align:center;margin-top:22px;font-size:13px;color:var(--muted)}
.bot a{color:var(--gold);text-decoration:none;font-weight:500}
.bot a:hover{text-decoration:underline}

/* success overlay */
.overlay{
  display:none;position:fixed;inset:0;
  background:rgba(10,12,16,.94);z-index:300;
  align-items:center;justify-content:center;flex-direction:column;
  text-align:center;padding:40px;
}
.overlay.on{display:flex}
.ov-icon{
  width:68px;height:68px;border:2px solid var(--green);
  display:flex;align-items:center;justify-content:center;
  font-size:30px;margin-bottom:22px;
  animation:fu .5s ease both;
}
.overlay h2{font-family:'Playfair Display',serif;font-size:27px;margin-bottom:10px;animation:fu .5s .1s ease both}
.overlay p{color:var(--muted);font-size:14px;max-width:320px;animation:fu .5s .2s ease both}
.ov-id{
  font-family:monospace;font-size:19px;color:var(--gold);
  background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.25);
  padding:10px 26px;margin:18px 0;letter-spacing:2px;
  animation:fu .5s .3s ease both;
}
.ov-btn{
  padding:12px 32px;background:var(--gold);color:#0a0c10;
  border:none;font-family:'DM Sans',sans-serif;font-size:13px;
  font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
  cursor:pointer;animation:fu .5s .4s ease both;transition:background .2s;
}
.ov-btn:hover{background:var(--gold2)}

@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}

/* page fade-in */
.r>*{animation:fu .4s ease both;opacity:0}
.r>*:nth-child(1){animation-delay:.05s}
.r>*:nth-child(2){animation-delay:.12s}
.r>*:nth-child(3){animation-delay:.18s}
.r>*:nth-child(4){animation-delay:.22s}
.r>*:nth-child(5){animation-delay:.26s}

@media(max-width:860px){.l{display:none}.r{width:100%;padding:36px 22px 52px}}
</style>
</head>
<body>

<!-- LEFT -->
<div class="l">
  <div class="grid"></div>
  <div class="brand">
    <div class="hex">⬡</div>
    <h1>Student<br><span>Registration</span></h1>
    <div class="line"></div>
    <p>Create your student account in minutes. Your Student ID is generated automatically upon registration.</p>
    <div class="steps">
      <div class="step"><div class="sn">01</div><div class="st"><strong>Account Details</strong>Username, email &amp; password</div></div>
      <div class="step"><div class="sn">02</div><div class="st"><strong>Personal Information</strong>Name, phone &amp; date of birth</div></div>
      <div class="step"><div class="sn">03</div><div class="st"><strong>Academic Details</strong>Department &amp; year of study</div></div>
      <div class="step"><div class="sn">04</div><div class="st"><strong>Done</strong>Log in with your new account</div></div>
    </div>
  </div>
</div>

<!-- RIGHT -->
<div class="r">
  <div class="fh">
    <p class="tag">Student Portal</p>
    <h2>Create Account</h2>
    <p>Fields marked <span style="color:var(--gold)">*</span> are required.</p>
  </div>

  <div id="alert" class="alert"></div>

  <form id="regForm" novalidate onsubmit="doRegister(event)">

    <!-- ACCOUNT -->
    <div class="sec">Account</div>
    <div class="row">
      <div class="fg">
        <label>Username <span style="color:var(--gold)">*</span></label>
        <input id="username" type="text" placeholder="e.g. john_doe" autocomplete="username">
        <div class="ferr" id="e-user"></div>
      </div>
      <div class="fg">
        <label>Email <span style="color:var(--gold)">*</span></label>
        <input id="email" type="email" placeholder="you@university.edu" autocomplete="email">
        <div class="ferr" id="e-email"></div>
      </div>
    </div>
    <div class="row">
      <div class="fg">
        <label>Password <span style="color:var(--gold)">*</span></label>
        <div class="pw-wrap">
          <input id="password" type="password" placeholder="Min 8 characters" autocomplete="new-password" oninput="strength(this)">
          <button type="button" class="eye" onclick="toggleEye('password',this)">👁</button>
        </div>
        <div class="bar" id="bar"><span></span><span></span><span></span><span></span></div>
        <div class="hint">Must have uppercase &amp; number.</div>
      </div>
      <div class="fg">
        <label>Confirm Password <span style="color:var(--gold)">*</span></label>
        <div class="pw-wrap">
          <input id="confirm" type="password" placeholder="Repeat password" autocomplete="new-password">
          <button type="button" class="eye" onclick="toggleEye('confirm',this)">👁</button>
        </div>
        <div class="ferr" id="e-confirm"></div>
      </div>
    </div>

    <!-- PERSONAL -->
    <div class="sec">Personal Information</div>
    <div class="row one">
      <div class="fg">
        <label>Full Name <span style="color:var(--gold)">*</span></label>
        <input id="full_name" type="text" placeholder="As on official ID">
        <div class="ferr" id="e-name"></div>
      </div>
    </div>
    <div class="row">
      <div class="fg">
        <label>Phone</label>
        <input id="phone" type="tel" placeholder="+880-1700-000000">
      </div>
      <div class="fg">
        <label>Date of Birth</label>
        <input id="dob" type="date">
      </div>
    </div>
    <div class="row">
      <div class="fg">
        <label>Gender</label>
        <select id="gender">
          <option value="">Select…</option>
          <option>Male</option><option>Female</option><option>Other</option>
        </select>
      </div>
    </div>

    <!-- ACADEMIC -->
    <div class="sec">Academic Information</div>
    <div class="row">
      <div class="fg">
        <label>Department <span style="color:var(--gold)">*</span></label>
        <select id="department">
          <option value="">Select department…</option>
          <option>Computer Science</option>
          <option>Electrical Engineering</option>
          <option>Civil Engineering</option>
          <option>Mechanical Engineering</option>
          <option>Business Administration</option>
          <option>Pharmacy</option>
          <option>Law</option>
          <option>Mathematics</option>
          <option>Physics</option>
          <option>Chemistry</option>
          <option>Economics</option>
          <option>Architecture</option>
          <option>English Literature</option>
          <option>Other</option>
        </select>
        <div class="ferr" id="e-dept"></div>
      </div>
      <div class="fg">
        <label>Year of Study</label>
        <select id="year">
          <option value="">Select year…</option>
          <option value="1">Year 1</option>
          <option value="2">Year 2</option>
          <option value="3">Year 3</option>
          <option value="4">Year 4</option>
          <option value="5">Year 5</option>
          <option value="6">Year 6</option>
        </select>
      </div>
    </div>

    <button class="btn" id="btn" type="submit">Register</button>
  </form>

  <div class="bot">Already have an account? <a href="/login.php">Sign in here</a></div>
</div>

<!-- SUCCESS OVERLAY -->
<div class="overlay" id="overlay">
  <div class="ov-icon">✓</div>
  <h2>Registration Successful!</h2>
  <p>Your student account is ready. Your assigned Student ID:</p>
  <div class="ov-id" id="ov-id">—</div>
  <p style="font-size:12px;margin-bottom:20px">Save this ID — you will need it for official documents.</p>
  <button class="ov-btn" onclick="location.href='/login.php'">Go to Login →</button>
</div>

<script>
const $  = id => document.getElementById(id);
const ok = (el, eid) => { el.classList.remove('er'); el.classList.add('ok'); $(eid).classList.remove('on'); };
const er = (el, eid, msg) => { el.classList.remove('ok'); el.classList.add('er'); $(eid).textContent = msg; $(eid).classList.add('on'); };

function strength(el) {
  const v = el.value, bar = $('bar');
  let s = 0;
  if (v.length >= 8) s++;
  if (/[A-Z]/.test(v)) s++;
  if (/[0-9]/.test(v)) s++;
  if (/[^a-zA-Z0-9]/.test(v)) s++;
  bar.className = 'bar' + (s ? ' s' + s : '');
}

function toggleEye(id, btn) {
  const i = $(id); i.type = i.type === 'password' ? 'text' : 'password';
  btn.textContent = i.type === 'password' ? '👁' : '🙈';
}

function validate() {
  let pass = true;

  // username
  const u = $('username').value.trim();
  if (!u) { er($('username'), 'e-user', 'Username is required.'); pass = false; }
  else if (!/^[a-zA-Z0-9_]{3,30}$/.test(u)) { er($('username'), 'e-user', '3–30 chars, letters/numbers/underscore.'); pass = false; }
  else ok($('username'), 'e-user');

  // email
  const em = $('email').value.trim();
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { er($('email'), 'e-email', 'Enter a valid email.'); pass = false; }
  else ok($('email'), 'e-email');

  // password
  const pw = $('password').value;
  if (pw.length < 8 || !/[A-Z]/.test(pw) || !/[0-9]/.test(pw)) {
    $('password').classList.add('er'); pass = false;
  } else { $('password').classList.remove('er'); $('password').classList.add('ok'); }

  // confirm
  const cf = $('confirm').value;
  if (cf !== pw) { er($('confirm'), 'e-confirm', 'Passwords do not match.'); pass = false; }
  else ok($('confirm'), 'e-confirm');

  // full name
  const fn = $('full_name').value.trim();
  if (!fn) { er($('full_name'), 'e-name', 'Full name is required.'); pass = false; }
  else ok($('full_name'), 'e-name');

  // department
  const dp = $('department').value;
  if (!dp) { er($('department'), 'e-dept', 'Please select a department.'); pass = false; }
  else ok($('department'), 'e-dept');

  return pass;
}

async function doRegister(e) {
  e.preventDefault();
  document.getElementById('alert').className = 'alert';

  if (!validate()) return;

  const btn = $('btn');
  btn.textContent = 'Creating account…'; btn.disabled = true;

  try {
    const res  = await fetch('/api/register.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        username:         $('username').value.trim(),
        email:            $('email').value.trim(),
        password:         $('password').value,
        confirm_password: $('confirm').value,
        full_name:        $('full_name').value.trim(),
        phone:            $('phone').value.trim(),
        date_of_birth:    $('dob').value,
        gender:           $('gender').value,
        department:       $('department').value,
        year_of_study:    $('year').value,
      })
    });
    const data = await res.json();

    if (data.success) {
      $('ov-id').textContent = data.student_code;
      $('overlay').classList.add('on');
    } else {
      const al = document.getElementById('alert');
      al.className = 'alert err';
      al.textContent = data.message;
    }
  } catch (err) {
    const al = document.getElementById('alert');
    al.className = 'alert err';
    al.textContent = 'Connection error — is the server running?';
  }

  btn.textContent = 'Register'; btn.disabled = false;
}
</script>
</body>
</html>

