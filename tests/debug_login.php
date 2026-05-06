<?php
// =============================================
// DEBUG VISUEL : login.php (lignes 18-33)
// =============================================
require_once __DIR__ . '/../config/db.php';

// --- Préparer un utilisateur de test ---
$email_test = "debug@test-login.local";
$password_correct = "MonMotDePasse123!";
$hash = password_hash($password_correct, PASSWORD_BCRYPT);

mysqli_query($conn, "DELETE FROM users WHERE email = 'debug@test-login.local'");
$stmt_ins = mysqli_prepare($conn, "INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
$n = "Debug User"; $r = "producteur";
mysqli_stmt_bind_param($stmt_ins, "ssss", $n, $email_test, $hash, $r);
mysqli_stmt_execute($stmt_ins);
mysqli_stmt_close($stmt_ins);

// =============================================
// Fonction pour exécuter un scénario complet
// =============================================
function run_scenario($conn, $email, $password) {
    $steps = [];

    // LIGNE 18
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    $steps[] = [
        'ligne' => 18,
        'code' => 'mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?")',
        'desc' => 'MySQL compile la requête. Le <code>?</code> est un placeholder — l\'email n\'est <strong>pas encore injecté</strong>.',
        'ok' => $stmt !== false,
        'detail' => $stmt ? 'Statement créé avec succès' : 'Échec de la préparation'
    ];

    // LIGNE 21
    $bind = mysqli_stmt_bind_param($stmt, "s", $email);
    $steps[] = [
        'ligne' => 21,
        'code' => 'mysqli_stmt_bind_param($stmt, "s", $email)',
        'desc' => "Lie la valeur <code>\"$email\"</code> au placeholder <code>?</code>. Le <code>\"s\"</code> = type String.",
        'ok' => $bind,
        'detail' => $bind ? "Email \"$email\" lié au paramètre" : 'Échec du binding'
    ];

    // LIGNE 24
    $exec = mysqli_stmt_execute($stmt);
    $steps[] = [
        'ligne' => 24,
        'code' => 'mysqli_stmt_execute($stmt)',
        'desc' => "MySQL exécute la requête avec la valeur liée.",
        'ok' => $exec,
        'detail' => $exec ? 'Requête exécutée' : 'Erreur : ' . mysqli_stmt_error($stmt)
    ];

    // LIGNE 27
    $result = mysqli_stmt_get_result($stmt);
    $num = mysqli_num_rows($result);
    $steps[] = [
        'ligne' => 27,
        'code' => '$result = mysqli_stmt_get_result($stmt)',
        'desc' => "Récupère le jeu de résultats retourné par MySQL.",
        'ok' => $num > 0,
        'detail' => "$num ligne(s) trouvée(s) en base"
    ];

    // LIGNE 30
    $user = mysqli_fetch_assoc($result);
    $steps[] = [
        'ligne' => 30,
        'code' => '$user = mysqli_fetch_assoc($result)',
        'desc' => "Extrait la 1ère ligne sous forme de tableau associatif, ou <code>NULL</code>.",
        'ok' => $user !== null,
        'detail' => $user ? 'Utilisateur trouvé' : '$user = NULL — aucun résultat',
        'user_data' => $user
    ];

    // LIGNE 33
    mysqli_stmt_close($stmt);
    $steps[] = [
        'ligne' => 33,
        'code' => 'mysqli_stmt_close($stmt)',
        'desc' => "Ferme le statement et libère la mémoire.",
        'ok' => true,
        'detail' => 'Ressources libérées'
    ];

    // LIGNE 36 — password_verify
    $verify = $user && password_verify($password, $user['mot_de_passe']);
    $steps[] = [
        'ligne' => 36,
        'code' => 'password_verify($password, $user[\'mot_de_passe\'])',
        'desc' => $user ? "Compare le mot de passe saisi avec le hash bcrypt stocké en base." : "Non exécuté car \$user est NULL.",
        'ok' => $verify,
        'detail' => $verify ? '✅ Mot de passe CORRECT → Connexion autorisée' : '❌ Mot de passe incorrect ou utilisateur inexistant → Accès refusé',
        'final' => true
    ];

    return ['steps' => $steps, 'success' => $verify, 'user' => $user];
}

// Exécuter les 4 scénarios
$scenarios = [
    ['titre' => 'Login réussi', 'icon' => '✅', 'email' => $email_test, 'password' => $password_correct,
     'desc' => 'Email correct + mot de passe correct'],
    ['titre' => 'Mauvais mot de passe', 'icon' => '🔑', 'email' => $email_test, 'password' => 'mauvais_mdp',
     'desc' => 'Email correct + mot de passe incorrect'],
    ['titre' => 'Email inexistant', 'icon' => '👻', 'email' => 'inconnu@nexistepas.com', 'password' => 'test',
     'desc' => 'Email qui n\'existe pas en base'],
    ['titre' => 'Injection SQL', 'icon' => '🛡️', 'email' => "' OR 1=1 --", 'password' => 'hack',
     'desc' => 'Tentative d\'injection SQL bloquée par prepare()'],
];

$results = [];
foreach ($scenarios as $s) {
    $results[] = array_merge($s, run_scenario($conn, $s['email'], $s['password']));
}

// Nettoyage
mysqli_query($conn, "DELETE FROM users WHERE email = 'debug@test-login.local'");
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login — Traçabilité Agricole</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: linear-gradient(135deg, #1a1d2e 0%, #0d1117 100%);
            border-radius: 16px;
            border: 1px solid #30363d;
        }
        .header h1 {
            font-size: 28px;
            background: linear-gradient(135deg, #58a6ff, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .header p { color: #8b949e; font-size: 14px; }
        .header .file-ref {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 14px;
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #58a6ff;
        }

        /* Info box */
        .info-box {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info-box h3 { color: #58a6ff; font-size: 14px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .info-row { display: flex; gap: 10px; margin-bottom: 6px; align-items: center; }
        .info-label { color: #8b949e; font-size: 13px; min-width: 140px; }
        .info-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #f0883e;
            background: #1c2028;
            padding: 3px 10px;
            border-radius: 4px;
        }

        /* Scenario tabs */
        .tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
        .tab {
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid #30363d;
            background: #161b22;
            color: #8b949e;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .tab:hover { border-color: #58a6ff; color: #e1e4e8; }
        .tab.active { background: #1f2937; border-color: #58a6ff; color: #58a6ff; }
        .tab.success { border-color: #3fb950; }
        .tab.success.active { color: #3fb950; border-color: #3fb950; }
        .tab.fail { border-color: #f85149; }
        .tab.fail.active { color: #f85149; border-color: #f85149; }

        /* Scenario content */
        .scenario { display: none; animation: fadeIn 0.3s ease; }
        .scenario.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .scenario-header {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #30363d;
        }
        .scenario-header.success { background: linear-gradient(135deg, #0d2818, #161b22); border-color: #238636; }
        .scenario-header.fail { background: linear-gradient(135deg, #2d1215, #161b22); border-color: #da3633; }
        .scenario-header h2 { font-size: 20px; margin-bottom: 6px; }
        .scenario-header p { color: #8b949e; font-size: 14px; }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
        .badge.success { background: #238636; color: #fff; }
        .badge.fail { background: #da3633; color: #fff; }

        /* Steps */
        .step {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: border-color 0.3s;
        }
        .step:hover { border-color: #484f58; }
        .step-header {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            gap: 12px;
        }
        .step-line {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
            min-width: 55px;
            text-align: center;
        }
        .step-line.ok { background: #0d2818; color: #3fb950; }
        .step-line.ko { background: #2d1215; color: #f85149; }
        .step-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #e1e4e8;
            flex: 1;
        }
        .step-status {
            font-size: 18px;
            min-width: 28px;
            text-align: center;
        }
        .step-body {
            padding: 0 18px 14px 18px;
            border-top: 1px solid #21262d;
            margin-top: 0;
            padding-top: 12px;
        }
        .step-desc { color: #8b949e; font-size: 13px; line-height: 1.6; margin-bottom: 8px; }
        .step-result {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
        }
        .step-result.ok { background: #0d2818; color: #3fb950; }
        .step-result.ko { background: #2d1215; color: #f85149; }

        /* User data table */
        .user-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
            font-size: 13px;
        }
        .user-table td {
            padding: 6px 12px;
            border-bottom: 1px solid #21262d;
        }
        .user-table td:first-child {
            font-family: 'JetBrains Mono', monospace;
            color: #d2a8ff;
            width: 160px;
        }
        .user-table td:last-child {
            font-family: 'JetBrains Mono', monospace;
            color: #7ee787;
        }

        /* Final result */
        .final-box {
            margin-top: 16px;
            padding: 16px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            text-align: center;
        }
        .final-box.success { background: linear-gradient(135deg, #0d2818, #1a3a2a); border: 1px solid #238636; color: #3fb950; }
        .final-box.fail { background: linear-gradient(135deg, #2d1215, #3d1a1d); border: 1px solid #da3633; color: #f85149; }

        /* Injection demo */
        .injection-demo {
            background: #1c1017;
            border: 1px solid #6e3630;
            border-radius: 8px;
            padding: 14px;
            margin-top: 10px;
            font-size: 13px;
        }
        .injection-demo .danger { color: #f85149; font-family: 'JetBrains Mono', monospace; font-size: 12px; }
        .injection-demo .safe { color: #3fb950; font-family: 'JetBrains Mono', monospace; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔍 Debug Login — Temps Réel</h1>
        <p>Visualisation étape par étape de la requête préparée</p>
        <div class="file-ref">📄 actions/login.php → lignes 18 à 36</div>
    </div>

    <div class="info-box">
        <h3>🧪 Utilisateur de test</h3>
        <div class="info-row"><span class="info-label">👤 Nom</span><span class="info-value">Debug User</span></div>
        <div class="info-row"><span class="info-label">📧 Email</span><span class="info-value"><?= htmlspecialchars($email_test) ?></span></div>
        <div class="info-row"><span class="info-label">🔑 Mot de passe</span><span class="info-value"><?= htmlspecialchars($password_correct) ?></span></div>
        <div class="info-row"><span class="info-label">🔒 Hash bcrypt</span><span class="info-value" style="font-size:11px;"><?= htmlspecialchars($hash) ?></span></div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <?php foreach ($results as $i => $r): ?>
            <div class="tab <?= $i === 0 ? 'active' : '' ?> <?= $r['success'] ? 'success' : 'fail' ?>"
                 onclick="showScenario(<?= $i ?>)">
                <?= $r['icon'] ?> <?= htmlspecialchars($r['titre']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Scenarios -->
    <?php foreach ($results as $i => $r): ?>
    <div class="scenario <?= $i === 0 ? 'active' : '' ?>" id="scenario-<?= $i ?>">
        <div class="scenario-header <?= $r['success'] ? 'success' : 'fail' ?>">
            <h2><?= $r['icon'] ?> Scénario <?= $i + 1 ?> : <?= htmlspecialchars($r['titre']) ?></h2>
            <p><?= htmlspecialchars($r['desc']) ?></p>
            <p style="margin-top:8px;">
                <strong>Email :</strong> <code style="color:#f0883e;"><?= htmlspecialchars($r['email']) ?></code>
                &nbsp;|&nbsp;
                <strong>Password :</strong> <code style="color:#f0883e;"><?= htmlspecialchars($r['password']) ?></code>
            </p>
            <span class="badge <?= $r['success'] ? 'success' : 'fail' ?>">
                <?= $r['success'] ? '✅ CONNEXION RÉUSSIE' : '❌ CONNEXION REFUSÉE' ?>
            </span>
        </div>

        <?php foreach ($r['steps'] as $s): ?>
        <div class="step">
            <div class="step-header">
                <span class="step-line <?= $s['ok'] ? 'ok' : 'ko' ?>">L.<?= $s['ligne'] ?></span>
                <span class="step-code"><?= $s['code'] ?></span>
                <span class="step-status"><?= $s['ok'] ? '✅' : '❌' ?></span>
            </div>
            <div class="step-body">
                <div class="step-desc"><?= $s['desc'] ?></div>
                <span class="step-result <?= $s['ok'] ? 'ok' : 'ko' ?>"><?= htmlspecialchars($s['detail']) ?></span>

                <?php if (isset($s['user_data']) && $s['user_data']): ?>
                <table class="user-table">
                    <?php foreach ($s['user_data'] as $k => $v): ?>
                    <tr>
                        <td>$user['<?= $k ?>']</td>
                        <td><?= $k === 'mot_de_passe' ? substr(htmlspecialchars($v), 0, 25) . '...' : htmlspecialchars($v) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>

                <?php if (isset($s['final']) && $s['final']): ?>
                <div class="final-box <?= $s['ok'] ? 'success' : 'fail' ?>">
                    <?= $s['ok']
                        ? '🎉 LOGIN RÉUSSI → $_SESSION[\'user\'] = $user → Redirection vers dashboard.php'
                        : '🚫 LOGIN ÉCHOUÉ → Redirection vers login.php?erreur=1' ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if ($i === 3): // Injection SQL scenario ?>
        <div class="injection-demo">
            <p style="margin-bottom:8px; font-weight:600;">💀 Sans requête préparée (DANGEREUX) :</p>
            <p class="danger">SELECT * FROM users WHERE email = '' OR 1=1 --'</p>
            <p style="color:#8b949e; font-size:12px; margin: 6px 0;">→ Retournerait TOUS les utilisateurs !</p>
            <p style="margin-top:10px; font-weight:600;">🛡️ Avec votre code (prepare + bind) :</p>
            <p class="safe">MySQL cherche un email valant littéralement "' OR 1=1 --" → Aucun résultat → Injection bloquée !</p>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
function showScenario(index) {
    document.querySelectorAll('.scenario').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById('scenario-' + index).classList.add('active');
    document.querySelectorAll('.tab')[index].classList.add('active');
}
</script>
</body>
</html>
