<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/css/style.css">
    <style>
        .feedback { font-size: 0.9em; margin-top: -15px; margin-bottom: 15px; display: block; height: 1.2em; }
        .success { color: var(--secondary-color); font-weight: bold; }
        .error { color: var(--danger-color); font-weight: bold; }
        
        #strength-bar { height: 5px; width: 0%; transition: width 0.3s, background 0.3s; border-radius: 3px; margin-top: -15px; margin-bottom: 15px; }
        .weak { width: 33%; background: #e74c3c; }
        .medium { width: 66%; background: #f39c12; }
        .strong { width: 100%; background: #27ae60; }
        
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-weight: bold; }
        .alert-danger { background: #fce4e4; color: #c0392b; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h2>Create Account</h2>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form action="<?= BASE_URL ?>auth/register" method="POST" id="regForm">
        
        <label>Full Name</label>
        <input type="text" name="name" id="nameInput" placeholder="Name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
        <span id="nameFeedback" class="feedback"></span>

        <label>Email Address</label>
        <input type="email" name="email" id="emailInput" placeholder="Email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        <span id="emailFeedback" class="feedback"></span>

        <label>Password</label>
        <input type="password" name="password" id="passInput" placeholder="Password">
        <div id="strength-bar"></div>
        <span id="passFeedback" class="feedback"></span>

        <label>I am a:</label>
        <select name="role" required>
            <option value="learner">Learner</option>
            <option value="instructor">Instructor</option>
            <option value="client">Client</option>
        </select>
        
        <br>
        <button type="submit" id="submitBtn">Register</button>
    </form>

    <script>
        const nameInput = document.getElementById('nameInput');
        const emailInput = document.getElementById('emailInput');
        const passInput = document.getElementById('passInput');
        
        const nameFeedback = document.getElementById('nameFeedback');
        const emailFeedback = document.getElementById('emailFeedback');
        const passFeedback = document.getElementById('passFeedback');
        const strengthBar = document.getElementById('strength-bar');
        
        const submitBtn = document.getElementById('submitBtn');
        const regForm = document.getElementById('regForm');

        // --- 1. REAL-TIME EMAIL CHECK (Debounced) ---
        let emailTimer; // Timer variable
        
        emailInput.addEventListener('input', function() {
            // A. IMMEDIATE UPDATE: Clear old messages as soon as user types
            emailFeedback.textContent = ''; 
            emailFeedback.className = 'feedback';
            emailInput.style.borderColor = '#ccc';
            submitBtn.disabled = false; // Reset button state while typing
            
            clearTimeout(emailTimer); // Clear previous timer

            // B. Wait 500ms after typing stops, THEN check
            emailTimer = setTimeout(() => {
                let email = this.value.trim();
                
                if(email.length > 0) {
                    fetch('<?= BASE_URL ?>auth/apiCheckEmail', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: email })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'taken') {
                            emailFeedback.textContent = data.message;
                            emailFeedback.className = 'feedback error';
                            emailInput.style.borderColor = 'red';
                            submitBtn.disabled = true; // Block submit if taken
                        } else if (data.status === 'available') {
                            emailFeedback.textContent = data.message;
                            emailFeedback.className = 'feedback success';
                            emailInput.style.borderColor = 'green';
                            submitBtn.disabled = false;
                        } else {
                            // Invalid format
                            emailFeedback.textContent = data.message;
                            emailFeedback.className = 'feedback error';
                        }
                    });
                }
            }, 500); // 500ms delay
        });

        // --- 2. PASSWORD STRENGTH METER ---
        passInput.addEventListener('keyup', function() {
            let val = this.value;
            let strength = 0;
            if (val.length >= 6) strength++;
            if (val.match(/[0-9]/)) strength++;
            if (val.match(/[!@#$%^&*]/)) strength++;

            strengthBar.className = '';
            if (val.length === 0) {
                strengthBar.style.width = '0%';
                passFeedback.textContent = '';
            } else if (strength === 1) {
                strengthBar.className = 'weak';
                strengthBar.style.width = '33%';
                passFeedback.textContent = 'Weak';
                passFeedback.style.color = '#e74c3c';
            } else if (strength === 2) {
                strengthBar.className = 'medium';
                strengthBar.style.width = '66%';
                passFeedback.textContent = 'Medium';
                passFeedback.style.color = '#f39c12';
            } else if (strength >= 3) {
                strengthBar.className = 'strong';
                strengthBar.style.width = '100%';
                passFeedback.textContent = 'Strong';
                passFeedback.style.color = '#27ae60';
            }
        });

        // --- 3. SUBMIT VALIDATION (The Missing Logic) ---
        regForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            nameFeedback.textContent = '';
            // emailFeedback logic is handled by AJAX, but we check empty here
            
            // A. Check Name Empty
            if (nameInput.value.trim() === '') {
                nameFeedback.textContent = "⚠️ Name is required";
                nameFeedback.className = 'feedback error';
                nameInput.style.borderColor = 'red';
                isValid = false;
            }

            // B. Check Email Empty
            if (emailInput.value.trim() === '') {
                emailFeedback.textContent = "⚠️ Email is required";
                emailFeedback.className = 'feedback error';
                emailInput.style.borderColor = 'red';
                isValid = false;
            }

            // C. Check Password Empty
            if (passInput.value.trim() === '') {
                passFeedback.textContent = "⚠️ Password is required";
                passFeedback.className = 'feedback error';
                passInput.style.borderColor = 'red';
                isValid = false;
            }
            // D. Check Password Length
            else if (passInput.value.length < 6) {
                passFeedback.textContent = "⚠️ Password must be at least 6 characters";
                passFeedback.className = 'feedback error';
                passInput.style.borderColor = 'red';
                isValid = false;
            }

            // STOP SUBMISSION IF ANY ERROR
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>