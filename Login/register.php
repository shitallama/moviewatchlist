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
$username = '';
$email = '';
$agreed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirmPassword = $_POST['confirm_password'] ?? '';
	$agreed = isset($_POST['agree']);

	if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
		$errors[] = 'Please complete all required fields.';
	}

	if ($username !== '' && strlen($username) < 3) {
		$errors[] = 'Username must be at least 3 characters.';
	}

	if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Please enter a valid email address.';
	}

	if ($password !== '' && strlen($password) < 8) {
		$errors[] = 'Password must be at least 8 characters.';
	}

	if ($password !== '' && $confirmPassword !== '' && $password !== $confirmPassword) {
		$errors[] = 'Passwords do not match.';
	}

	if (!$agreed) {
		$errors[] = 'Please agree to the terms to create your account.';
	}

	if (empty($errors)) {
		$stmt = $pdo->prepare(
			'SELECT user_id FROM Users WHERE username = :username OR email = :email LIMIT 1'
		);
		$stmt->execute([
			'username' => $username,
			'email' => $email,
		]);
		$existing = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($existing) {
			$errors[] = 'An account with that username or email already exists.';
		} else {
			$passwordHash = password_hash($password, PASSWORD_DEFAULT);

			try {
				$insert = $pdo->prepare(
					'INSERT INTO Users (username, email, password_hash, is_active, is_admin) VALUES (:username, :email, :password_hash, 1, 0)'
				);
				$insert->execute([
					'username' => $username,
					'email' => $email,
					'password_hash' => $passwordHash,
				]);

				header('Location: ' . $basePath . 'Login/login.php?registered=1');
				exit;
			} catch (PDOException $e) {
				$errors[] = 'Could not create your account. Please try again.';
			}
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<title>Register | CineList</title>
	<link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
	<link rel="stylesheet" href="<?php echo $basePath; ?>assets/register.css">
	<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>
	<section class="register-shell">
		<div class="register-panel">
			<div class="register-visual">
				<h1>Join CineList today</h1>
				<p>Track what you watch, build smarter lists, and get inspired for your next movie night.</p>
				<div class="register-steps">
					<div class="step-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/user-plus.svg" alt="" aria-hidden="true"> Create your profile</div>
					<div class="step-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/layers.svg" alt="" aria-hidden="true"> Organize your genres</div>
					<div class="step-item"><img class="icon" src="<?php echo $basePath; ?>assets/icons/heart.svg" alt="" aria-hidden="true"> Save favorites fast</div>
				</div>
			</div>
			<div class="register-form-wrap">
				<div>
					<h2>Create account</h2>
					<p class="login-meta">It takes less than a minute.</p>
				</div>

				<?php if (!empty($errors)): ?>
					<div class="form-alert">
						<?php echo htmlspecialchars(implode(' ', $errors)); ?>
					</div>
				<?php endif; ?>

				<form method="post" class="register-form" novalidate>
					<div class="register-grid">
						<div class="form-field">
							<label for="username">Username</label>
							<input
								type="text" id="username" name="username" autocomplete="username" placeholder="Choose a username" required value="<?php echo htmlspecialchars($username); ?>">
						</div>
						<div class="form-field">
							<label for="email">Email</label>
							<input type="email" id="email" name="email" autocomplete="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($email); ?>">
						</div>
					</div>
					<div class="form-field">
						<label for="password">Password</label>
						<div class="password-wrap">
							<input type="password" id="password" name="password" autocomplete="new-password" required placeholder="Enter a password (min 8 characters)">
							<button class="toggle-password" type="button" data-target="password" aria-label="Show password">
								<img class="icon" src="<?php echo $basePath; ?>assets/icons/eye-closed.svg" alt="" aria-hidden="true" data-icon-open="<?php echo $basePath; ?>assets/icons/eye-open.svg" data-icon-closed="<?php echo $basePath; ?>assets/icons/eye-closed.svg">
							</button>
						</div>
					</div>
					<div class="form-field">
						<label for="confirm_password">Confirm password</label>
						<div class="password-wrap">
							<input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required placeholder="Confirm your password">
							<button class="toggle-password" type="button" data-target="confirm_password" aria-label="Show password">
								<img class="icon" src="<?php echo $basePath; ?>assets/icons/eye-closed.svg" alt="" aria-hidden="true" data-icon-open="<?php echo $basePath; ?>assets/icons/eye-open.svg" data-icon-closed="<?php echo $basePath; ?>assets/icons/eye-closed.svg">
							</button>
						</div>
					</div>
					<label class="terms-row">
						<input type="checkbox" name="agree" <?php echo $agreed ? 'checked' : ''; ?>>
						I agree to the terms and privacy policy.
					</label>
					<button class="primary-btn" type="submit">Create account</button>
				</form>

				<div class="login-meta">
					<span>Already have an account?</span>
					<a href="<?php echo $basePath; ?>Login/login.php">Sign in</a>
				</div>
			</div>
		</div>
</section>

<?php require_once $basePath . 'includes/footer.php'; ?>
</html>
