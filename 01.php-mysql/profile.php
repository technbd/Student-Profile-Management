<?php
require_once 'config/session.php';
requireLogin();

$db   = db();
$uid  = (int) $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

// Fetch student profile (may be empty for admin)
$stmt = $db->prepare(
    'SELECT s.*, u.username, u.email AS account_email, u.role
     FROM students s JOIN users u ON u.id = s.user_id
     WHERE s.user_id = ?'
);
$stmt->bind_param('i', $uid);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Admin with no student profile — fetch at least user info
if (!$p) {
    $ustmt = $db->prepare('SELECT id, username, email, role FROM users WHERE id = ?');
    $ustmt->bind_param('i', $uid);
    $ustmt->execute();
    $urow = $ustmt->get_result()->fetch_assoc();
    $ustmt->close();
    $p = [
        'full_name'      => $urow['username'] ?? 'Admin',
        'email'          => $urow['email']    ?? '',
        'account_email'  => $urow['email']    ?? '',
        'username'       => $urow['username'] ?? '',
        'role'           => $urow['role']     ?? 'admin',
        'student_code'   => null,
        'department'     => null,
        'year_of_study'  => null,
        'gender'         => null,
        'gpa'            => null,
        'phone'          => null,
        'date_of_birth'  => null,
        'address'        => null,
        'enrollment_date'=> null,
        'profile_picture'=> 'default.png',
    ];
}

$pic = (!empty($p['profile_picture']) && $p['profile_picture'] !== 'default.png'
        && file_exists(UPLOAD_DIR . $p['profile_picture']))
     ? UPLOAD_URL . $p['profile_picture']
     : 'https://ui-avatars.com/api/?name=' . urlencode($p['full_name'] ?? 'User')
       . '&size=200&background=c9a84c&color=0a0c10&bold=true&format=png';

$isAdmin = ($role === 'admin');
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile — Student Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#0a0c10;--surface:#11141a;--border:#1e2430;
  --gold:#c9a84c;--gold2:#e8c97a;
  --text:#e8e4d9;--muted:#6b7280;
  --red:#e05c5c;--green:#4caf7d;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}

/* NAV */
nav{
  height:62px;background:var(--surface);border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 40px;position:sticky;top:0;z-index:100;
}
.nav-brand{
  display:flex;align-items:center;gap:10px;
  font-family:'Playfair Display',serif;font-size:17px;
}
.nav-dot{width:8px;height:8px;background:var(--gold)}
.nav-r{display:flex;align-items:center;gap:18px}
.nav-user{font-size:13px;color:var(--muted)}
.nav-user strong{color:var(--text)}
.btn-out{
  padding:7px 16px;background:transparent;
  border:1px solid var(--border);color:var(--muted);
  font-family:'DM Sans',sans-serif;font-size:12.5px;
  cursor:pointer;transition:all .2s;
}
.btn-out:hover{border-color:var(--red);color:var(--red)}

/* LAYOUT */
.wrap{max-width:1080px;margin:0 auto;padding:38px 28px}

.ph{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px;padding-bottom:22px;border-bottom:1px solid var(--border)}
.ph h1{font-family:'Playfair Display',serif;font-size:26px}
.ph p{color:var(--muted);font-size:13px;margin-top:4px}
.scode{
  font-family:monospace;font-size:12.5px;color:var(--gold);
  background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.2);
  padding:5px 14px;
}

/* GRID */
.grid{display:grid;grid-template-columns:280px 1fr;gap:22px}

/* PROFILE CARD */
.card{background:var(--surface);border:1px solid var(--border);padding:28px 22px;text-align:center}

.av-wrap{position:relative;display:inline-block;margin-bottom:18px}
.av{
  width:130px;height:130px;object-fit:cover;
  border:2px solid var(--gold);display:block;
}
.av-btn{
  position:absolute;bottom:-8px;right:-8px;
  width:32px;height:32px;background:var(--gold);
  border:2px solid var(--bg);
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:background .2s;
}
.av-btn:hover{background:var(--gold2)}
.av-btn svg{width:14px;fill:var(--bg)}
#fileInput{display:none}

.up-msg{font-size:11.5px;color:var(--muted);margin-top:8px;min-height:16px}

.p-name{font-family:'Playfair Display',serif;font-size:20px;margin-bottom:3px}
.p-dept{font-size:12.5px;color:var(--gold);margin-bottom:18px}

.meta{text-align:left;border-top:1px solid var(--border);padding-top:16px}
.mi{
  display:flex;justify-content:space-between;
  padding:7px 0;border-bottom:1px solid rgba(30,36,48,.6);
  font-size:12.5px;
}
.ml{color:var(--muted)}
.mv{font-weight:500}
.gpa{
  background:rgba(76,175,125,.12);color:var(--green);
  padding:1px 9px;font-size:12.5px;font-weight:600;
}

/* EDIT CARD */
.ecard{background:var(--surface);border:1px solid var(--border);padding:30px}

.sec{
  font-size:10px;letter-spacing:2px;text-transform:uppercase;
  color:var(--gold);display:flex;align-items:center;gap:10px;
  margin:24px 0 18px;
}
.sec:first-child{margin-top:0}
.sec::after{content:'';flex:1;height:1px;background:var(--border)}

.row{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px}
.row.one{grid-template-columns:1fr}
.fg{}
label{display:block;font-size:10.5px;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
input,select,textarea{
  width:100%;padding:10px 13px;
  background:var(--bg);border:1px solid var(--border);
  color:var(--text);font-family:'DM Sans',sans-serif;font-size:13.5px;
  outline:none;transition:border-color .2s;
}
input:focus,select:focus,textarea:focus{
  border-color:var(--gold);box-shadow:0 0 0 2px rgba(201,168,76,.07)
}
select option{background:var(--surface)}
textarea{resize:vertical;min-height:76px}

.actions{
  display:flex;gap:12px;margin-top:26px;
  padding-top:22px;border-top:1px solid var(--border);
}
.btn-save{
  padding:11px 26px;background:var(--gold);color:#0a0c10;
  border:none;font-family:'DM Sans',sans-serif;
  font-size:12.5px;font-weight:700;letter-spacing:1.2px;
  text-transform:uppercase;cursor:pointer;transition:background .2s;
}
.btn-save:hover{background:var(--gold2)}
.btn-reset{
  padding:11px 26px;background:transparent;color:var(--muted);
  border:1px solid var(--border);font-family:'DM Sans',sans-serif;
  font-size:12.5px;cursor:pointer;transition:all .2s;
}
.btn-reset:hover{color:var(--text);border-color:var(--muted)}

/* TOAST */
.toast{
  position:fixed;bottom:28px;right:28px;
  padding:12px 20px;font-size:13.5px;display:none;z-index:200;
}
.toast.ok{background:rgba(76,175,125,.15);border:1px solid var(--green);color:var(--green);display:flex}
.toast.er{background:rgba(224,92,92,.15);border:1px solid var(--red);color:var(--red);display:flex}

.admin-banner{
  background:rgba(201,168,76,.07);border:1px solid rgba(201,168,76,.2);
  padding:12px 20px;margin-bottom:28px;font-size:13.5px;color:var(--gold);
  display:flex;align-items:center;gap:10px;
}
.admin-banner strong{color:var(--text)}
.role-tag{
  font-size:10px;letter-spacing:1.5px;text-transform:uppercase;
  background:rgba(201,168,76,.15);color:var(--gold);
  padding:3px 10px;border:1px solid rgba(201,168,76,.25);
  margin-left:8px;
}


@media(max-width:780px){
  .grid{grid-template-columns:1fr}
  .row{grid-template-columns:1fr}
  nav{padding:0 18px}
  .wrap{padding:22px 14px}
  .ph{flex-direction:column;align-items:flex-start;gap:10px}
}
</style>
</head>
<body>

<nav>
  <div class="nav-brand"><div class="nav-dot"></div>Student Portal</div>
  <div class="nav-r">
    <span class="nav-user">
      Hello, <strong><?= $h($_SESSION['full_name'] ?? $_SESSION['username']) ?></strong>
      <span class="role-tag"><?= $h($role) ?></span>
    </span>
    <button class="btn-out" onclick="location.href='/api/logout.php'">Sign Out</button>
  </div>
</nav>

<div class="wrap">

  <?php if ($isAdmin): ?>
  <div class="admin-banner">
    ⚙ <span><strong>Admin Account</strong> — You are logged in as an administrator. Student profile fields are not editable for admin accounts.</span>
  </div>
  <?php endif; ?>

  <div class="ph">
    <div>
      <h1>My Profile</h1>
      <p>View and update your personal and academic information</p>
    </div>
    <?php if (!empty($p['student_code'])): ?>
    <div class="scode"><?= $h($p['student_code']) ?></div>
    <?php endif; ?>
  </div>

  <div class="grid">

    <!-- LEFT: Profile card -->
    <div class="card">
      <div class="av-wrap">
        <img src="<?= $h($pic) ?>" alt="Profile" class="av" id="avImg" data-original="<?= $h($pic) ?>">
        <label class="av-btn" for="fileInput" title="Change photo">
          <svg viewBox="0 0 24 24"><path d="M12 15.2A3.2 3.2 0 1 0 12 8.8a3.2 3.2 0 0 0 0 6.4zm0-4.8a1.6 1.6 0 1 1 0 3.2A1.6 1.6 0 0 1 12 10.4zM20 4h-3.17L15 2H9L7.17 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 14H4V6h4.05l1.83-2h4.24l1.83 2H20v12z"/></svg>
        </label>
        <input type="file" id="fileInput" accept="image/*" onchange="uploadPic(this)">
      </div>
      <div id="upMsg" class="up-msg"></div>

      <div class="p-name"><?= $h($p['full_name'] ?? '—') ?></div>
      <div class="p-dept"><?= $h($p['department'] ?? 'No Department') ?></div>

      <div class="meta">
        <div class="mi"><span class="ml">Student ID</span><span class="mv"><?= $h($p['student_code'] ?? '—') ?></span></div>
        <div class="mi"><span class="ml">Year</span><span class="mv"><?= $p['year_of_study'] ? 'Year '.(int)$p['year_of_study'] : '—' ?></span></div>
        <div class="mi"><span class="ml">Gender</span><span class="mv"><?= $h($p['gender'] ?? '—') ?></span></div>
        <div class="mi"><span class="ml">GPA</span>
          <span class="mv">
            <?php if (!empty($p['gpa'])): ?>
              <span class="gpa"><?= number_format((float)$p['gpa'], 2) ?></span>
            <?php else: ?>—<?php endif; ?>
          </span>
        </div>
        <div class="mi"><span class="ml">Enrolled</span><span class="mv"><?= !empty($p['enrollment_date']) ? date('M Y', strtotime($p['enrollment_date'])) : '—' ?></span></div>
        <div class="mi"><span class="ml">Role</span><span class="mv" style="text-transform:capitalize"><?= $h($_SESSION['role']) ?></span></div>
      </div>
    </div>

    <!-- RIGHT: Edit form -->
    <div class="ecard">

      <div class="sec">Personal Information</div>

      <form id="profileForm" onsubmit="saveProfile(event)">
        <div class="row">
          <div class="fg">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?= $h($p['full_name'] ?? '') ?>" required>
          </div>
          <div class="fg">
            <label>Email <span style="color:var(--muted);font-size:10px;text-transform:none;letter-spacing:0">(read-only)</span></label>
            <input type="email" value="<?= $h($p['email'] ?? '') ?>" readonly
                   style="opacity:.5;cursor:not-allowed" title="Email cannot be changed here">
          </div>
        </div>
        <div class="row">
          <div class="fg">
            <label>Phone</label>
            <input type="tel" name="phone" value="<?= $h($p['phone'] ?? '') ?>" placeholder="+880-1700-000000">
          </div>
          <div class="fg">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" value="<?= $h($p['date_of_birth'] ?? '') ?>">
          </div>
        </div>
        <div class="row">
          <div class="fg">
            <label>Gender</label>
            <select name="gender">
              <option value="">Select…</option>
              <?php foreach (['Male','Female','Other'] as $g): ?>
              <option value="<?= $g ?>" <?= ($p['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="fg">
            <label>Address</label>
            <input type="text" name="address" value="<?= $h($p['address'] ?? '') ?>" placeholder="City, Country">
          </div>
        </div>

        <div class="sec">Academic Information</div>

        <div class="row">
          <div class="fg">
            <label>Department</label>
            <select name="department">
              <option value="">Select…</option>
              <?php foreach ([
                'Computer Science','Electrical Engineering','Civil Engineering',
                'Mechanical Engineering','Business Administration','Pharmacy',
                'Law','Mathematics','Physics','Chemistry','Economics','Architecture',
                'English Literature','Other'
              ] as $d): ?>
              <option value="<?= $d ?>" <?= ($p['department'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="fg">
            <label>Year of Study</label>
            <select name="year_of_study">
              <option value="">Select…</option>
              <?php for ($y = 1; $y <= 6; $y++): ?>
              <option value="<?= $y ?>" <?= ($p['year_of_study'] ?? 0) == $y ? 'selected' : '' ?>>Year <?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="fg">
            <label>GPA</label>
            <input type="number" name="gpa" value="<?= $h($p['gpa'] ?? '') ?>"
                   min="0" max="4" step="0.01" placeholder="0.00 – 4.00">
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn-save" id="saveBtn"
            <?= $isAdmin ? 'disabled title="Admin accounts cannot edit student fields" style="opacity:.45;cursor:not-allowed"' : '' ?>>
            Save Changes
          </button>
          <button type="reset" class="btn-reset" <?= $isAdmin ? 'disabled' : '' ?>>Reset</button>
        </div>
      </form>
    </div>

  </div><!-- .grid -->
</div><!-- .wrap -->

<div class="toast" id="toast"></div>

<script>
function toast(msg, type) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.className = 'toast ' + type;
  clearTimeout(window._t);
  window._t = setTimeout(() => t.className = 'toast', 4000);
}

async function saveProfile(e) {
  e.preventDefault();
  const btn = document.getElementById('saveBtn');
  btn.textContent = 'Saving…'; btn.disabled = true;

  const fd   = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());

  // Remove the read-only email field if it somehow snuck in
  delete data.email;

  try {
    const res = await fetch('/api/profile.php?action=update', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(data)
    });

    // Guard: server might return non-JSON on fatal PHP error
    const text = await res.text();
    let r;
    try { r = JSON.parse(text); }
    catch { toast('Server error: ' + text.substring(0, 120), 'er'); btn.textContent = 'Save Changes'; btn.disabled = false; return; }

    toast(r.message || (r.success ? 'Saved.' : 'Failed.'), r.success ? 'ok' : 'er');

    if (r.success) {
      if (data.full_name) {
        document.querySelector('.p-name').textContent = data.full_name;
        document.querySelector('.nav-user strong').textContent = data.full_name;
      }
      if (data.department) document.querySelector('.p-dept').textContent = data.department;
    }
  } catch (err) {
    toast('Network error: ' + err.message, 'er');
  }

  btn.textContent = 'Save Changes'; btn.disabled = false;
}

async function uploadPic(input) {
  const file = input.files[0];
  if (!file) return;

  const msg   = document.getElementById('upMsg');
  const avImg = document.getElementById('avImg');
  msg.textContent = 'Uploading…';

  // Show instant local preview using blob URL — no server URL needed
  const localPreview = URL.createObjectURL(file);
  avImg.src = localPreview;

  const fd = new FormData();
  fd.append('picture', file);

  try {
    const res  = await fetch('/api/profile.php?action=upload', { method: 'POST', body: fd });

    const text = await res.text();
    let r;
    try { r = JSON.parse(text); }
    catch {
      // Revert preview on parse error
      avImg.src = localPreview; // keep local preview at least
      toast('Server error: ' + text.substring(0, 120), 'er');
      msg.textContent = '';
      input.value = '';
      return;
    }

    if (r.success) {
      // Replace blob URL with the real server URL (cache-busted)
      const serverUrl = r.picture_url + '?t=' + Date.now();
      const testImg   = new Image();
      testImg.onload  = () => { avImg.src = serverUrl; URL.revokeObjectURL(localPreview); };
      testImg.onerror = () => {
        // Server URL broken — keep local blob preview and warn
        console.warn('Server picture URL failed to load:', serverUrl);
        toast('Photo saved but preview URL failed — check UPLOAD_URL in config/db.php', 'er');
      };
      testImg.src = serverUrl;
      toast('Profile picture updated!', 'ok');
    } else {
      // Revert preview
      avImg.src = avImg.dataset.original || avImg.src;
      toast(r.message || 'Upload failed.', 'er');
    }
  } catch (err) {
    avImg.src = avImg.dataset.original || avImg.src;
    toast('Network error: ' + err.message, 'er');
  }

  msg.textContent = '';
  input.value = '';
}
</script>
</body>
</html>

