<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <style>
        .feedback { 
            font-size: 0.85em; 
            margin-top: 5px; 
            margin-bottom: 10px; 
            display: block; 
            min-height: 1.2em; /* Keeps space so layout doesn't jump */
        }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        
        /* Strength Bar Styles */
        #strength-bar { 
            height: 4px; 
            width: 0%; 
            transition: width 0.3s, background 0.3s; 
            border-radius: 3px; 
            margin-top: 5px; 
            margin-bottom: 5px; 
        }
        .weak { width: 33%; background: #e74c3c; }
        .medium { width: 66%; background: #f39c12; }
        .strong { width: 100%; background: #27ae60; }
        
        /* Admin Badge */
        #admin-badge {
            background: #e8f8f5; 
            padding: 15px; 
            border: 1px solid #2ecc71; 
            border-radius: 5px; 
            margin-bottom: 20px;
        }
    </style>

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

        <div id="role-container">
            <label>I am a:</label>
            <select name="role" id="roleSelect" required>
                <option value="learner">Learner</option>
                <option value="instructor">Instructor</option>
                <option value="client">Client</option>
            </select>
        </div>

        <div id="admin-badge" style="display:none;">
            <strong style="color: #27ae60;">👑 Admin Invitation Detected</strong>
            <p style="margin: 5px 0 0; font-size: 0.9em;">Your account will be created with Administrator privileges.</p>
            <input type="hidden" name="role" value="admin" disabled id="adminHiddenInput">
        </div>
        
        <br>
        <button type="submit" id="submitBtn">Register</button>
    </form>

    <script>
        // --- 1. DEFINE ALL VARIABLES (Fixed Missing Lines) ---
        const nameInput = document.getElementById('nameInput');
        const emailInput = document.getElementById('emailInput');
        const passInput = document.getElementById('passInput');
        
        const nameFeedback = document.getElementById('nameFeedback');
        const emailFeedback = document.getElementById('emailFeedback');
        const passFeedback = document.getElementById('passFeedback');
        const strengthBar = document.getElementById('strength-bar');
        
        const submitBtn = document.getElementById('submitBtn');
        const regForm = document.getElementById('regForm');
        
        const roleContainer = document.getElementById('role-container');
        const roleSelect = document.getElementById('roleSelect');
        const adminBadge = document.getElementById('admin-badge');
        const adminHiddenInput = document.getElementById('adminHiddenInput');

        let emailTimer;

        // --- 2. EMAIL CHECK + ADMIN DETECTION ---
        emailInput.addEventListener('input', function() {
            emailFeedback.textContent = ''; 
            emailFeedback.className = 'feedback';
            emailInput.style.borderColor = '#ccc';
            submitBtn.disabled = false;
            
            roleContainer.style.display = 'block';
            adminBadge.style.display = 'none';
            roleSelect.disabled = false;
            adminHiddenInput.disabled = true; 

            clearTimeout(emailTimer);

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
                            submitBtn.disabled = true; 
                        } else if (data.status === 'available') {
                            emailFeedback.textContent = data.message;
                            emailFeedback.className = 'feedback success';
                            emailInput.style.borderColor = 'green';
                            submitBtn.disabled = false;

                            if (data.is_admin_invite === true) {
                                roleContainer.style.display = 'none';
                                roleSelect.disabled = true;
                                adminBadge.style.display = 'block';
                                adminHiddenInput.disabled = false; 
                            }
                        } else {
                            // Invalid Format
                            emailFeedback.textContent = data.message;
                            emailFeedback.className = 'feedback error';
                        }
                    });
                }
            }, 500);
        });

        // --- 3. PASSWORD STRENGTH METER (Fixed) ---
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
                strengthBar.className = 'weak'; // Calls the CSS defined above
                passFeedback.textContent = 'Weak';
                passFeedback.className = 'feedback error';
            } else if (strength === 2) {
                strengthBar.className = 'medium';
                passFeedback.textContent = 'Medium';
                passFeedback.className = 'feedback';
                passFeedback.style.color = '#f39c12';
            } else if (strength >= 3) {
                strengthBar.className = 'strong';
                passFeedback.textContent = 'Strong';
                passFeedback.className = 'feedback success';
            }
        });

        // --- 4. SUBMIT VALIDATION ---
        regForm.addEventListener('submit', function(e) {
            let isValid = true;
            nameFeedback.textContent = '';
            
            if (nameInput.value.trim() === '') {
                nameFeedback.textContent = "⚠️ Name is required";
                nameFeedback.className = 'feedback error';
                nameInput.style.borderColor = 'red';
                isValid = false;
            }

            if (emailInput.value.trim() === '') {
                emailFeedback.textContent = "⚠️ Email is required";
                emailFeedback.className = 'feedback error';
                emailInput.style.borderColor = 'red';
                isValid = false;
            }

            if (passInput.value.trim() === '') {
                passFeedback.textContent = "⚠️ Password is required";
                passFeedback.className = 'feedback error';
                passInput.style.borderColor = 'red';
                isValid = false;
            } else if (passInput.value.length < 6) {
                passFeedback.textContent = "⚠️ Password must be at least 6 characters";
                passFeedback.className = 'feedback error';
                passInput.style.borderColor = 'red';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>