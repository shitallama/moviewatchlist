<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (isset($_SESSION['user_id'])) {
	header('Location: ' . $basePath . 'index.php');
	exit;
}

$errors = [];
$identifier = '';
$remember = false;
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['registered']) && $_GET['registered'] === '1') {
	$successMessage = 'Account created, please sign in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$identifier = trim($_POST['identifier'] ?? '');
	$password = $_POST['password'] ?? '';
	$remember = isset($_POST['remember']);

	if ($identifier === '' || $password === '') {
		$errors[] = 'Please enter your username or email and password.';
	} else {
		$stmt = $pdo->prepare(
			'SELECT user_id, username, email, password_hash, is_active FROM Users WHERE username = :identifier OR email = :identifier LIMIT 1'
		);
		$stmt->execute(['identifier' => $identifier]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$user || (int)$user['is_active'] !== 1) {
			$errors[] = 'No active account found for those credentials.';
		} else {
			$storedHash = $user['password_hash'] ?? '';
			$passwordOk = password_verify($password, $storedHash) || hash_equals($storedHash, $password);

			if (!$passwordOk) {
				$errors[] = 'Incorrect password. Please try again.';
			} else {
				$_SESSION['user_id'] = $user['user_id'];
				$_SESSION['user_name'] = $user['username'];

				if ($remember) {
					$_SESSION['remember_me'] = true;
				}

				header('Location: ' . $basePath . 'index.php');
				exit;
			}
		}
	}
}

require_once $basePath . 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<title>Login | CineList</title>
	<link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
	<link rel="stylesheet" href="<?php echo $basePath; ?>assets/login.css">
	<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
	<section class="login-shell">
		<div class="login-panel">
			<div class="login-visual">
				<h1>Welcome back to CineList</h1>
				<p>Pick up your next movie night, track new releases, and keep the watchlist tight.</p>
				<div class="login-badges">
					<div class="badge-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/clapperboard.svg" alt="" aria-hidden="true"> Create your collection</div>
					<div class="badge-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/eye.svg" alt="" aria-hidden="true"> Track your watch status</div>
					<div class="badge-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/star.svg" alt="" aria-hidden="true"> Write Reviews</div>
				</div>
			</div>
			<div class="login-form-wrap">
				<div>
					<h2>Sign in</h2>
					<p class="login-meta">Use your CineList account to continue.</p>
				</div>

				<?php if ($successMessage !== ''): ?>
					<div class="form-success form-message" data-auto-dismiss>
						<?php echo htmlspecialchars($successMessage); ?>
					</div>
				<?php endif; ?>

				<?php if (!empty($errors)): ?>
					<div class="form-alert">
						<?php echo htmlspecialchars(implode(' ', $errors)); ?>
					</div>
				<?php endif; ?>

				<form method="post" class="login-form" novalidate>
					<div class="form-field">
						<label for="identifier">Email or username</label>
						<input
							type="text"
							id="identifier"
							name="identifier"
							autocomplete="username"
							required
							value="<?php echo htmlspecialchars($identifier); ?>"
						>
					</div>
					<div class="form-field">
						<label for="password">Password</label>
						<div class="password-wrap">
							<input
								type="password"
								id="password"
								name="password"
								autocomplete="current-password"
								required
							>
							<button class="toggle-password" type="button" data-target="password">Show</button>
						</div>
					</div>
					<div class="form-row">
						<label class="checkbox-wrap">
							<input type="checkbox" name="remember" <?php echo $remember ? 'checked' : ''; ?>>
							Remember me
						</label>
					</div>
					<button class="primary-btn" type="submit">Login</button>
				</form>

				<div class="login-meta">
					<span>New here?</span>
					<a href="<?php echo $basePath; ?>Login/register.php">Create an account</a>
				</div>
			</div>
		</div>
	</section>

<script src="<?php echo $basePath; ?>assets/js/login.js"></script>
<?php require_once $basePath . 'includes/footer.php'; ?>
</html>
