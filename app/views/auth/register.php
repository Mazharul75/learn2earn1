<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <style>
        /* Feedback Text */
        .feedback { 
            font-size: 0.85em; margin-top: 5px; margin-bottom: 10px; display: block; min-height: 1.2em; 
        }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        
        /* Strength Bar */
        #strength-bar { 
            height: 4px; width: 0%; transition: width 0.3s, background 0.3s; border-radius: 3px; margin-top: 5px; margin-bottom: 5px; 
        }
        .weak { width: 33%; background: #e74c3c; }
        .medium { width: 66%; background: #f39c12; }
        .strong { width: 100%; background: #27ae60; }
        
        /* Admin Badge */
        #admin-badge {
            background: #e8f8f5; padding: 15px; border: 1px solid #2ecc71; border-radius: 5px; margin-bottom: 20px; text-align: center;
        }
    </style>

    <div style="max-width: 500px; margin: 30px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h2 style="text-align: center; color: #2c3e50; margin-bottom: 20px;">Create Account</h2>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger" style="text-align: center; margin-bottom: 20px;"><?= $error ?></div>
        <?php endif; ?>
        
        <form action="<?= BASE_URL ?>auth/register" method="POST" id="regForm">
            
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Full Name</label>
            <input type="text" name="name" id="nameInput" placeholder="Name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <span id="nameFeedback" class="feedback"></span>

            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Email Address</label>
            <input type="email" name="email" id="emailInput" placeholder="Email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <span id="emailFeedback" class="feedback"></span>

            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Password</label>
            <input type="password" name="password" id="passInput" placeholder="Password"
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <div id="strength-bar"></div>
            <span id="passFeedback" class="feedback"></span>

            <div id="role-container" style="margin-bottom: 20px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">I am a:</label>
                <select name="role" id="roleSelect" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="learner">Learner (Student)</option>
                    <option value="instructor">Instructor (Teacher)</option>
                    <option value="client">Client (Hiring)</option>
                </select>
            </div>

            <div id="admin-badge" style="display:none;">
                <strong style="color: #27ae60; font-size: 1.1em;">👑 Admin Invitation Detected</strong>
                <p style="margin: 5px 0 0; font-size: 0.9em; color: #555;">Your account will be created with <strong>Administrator</strong> privileges.</p>
                <input type="hidden" name="role" value="admin" disabled id="adminHiddenInput">
            </div>
            
            <button type="submit" id="submitBtn" class="btn" style="width: 100%; padding: 12px; font-size: 1.1rem; background: #27ae60;">Register</button>
        </form>

        <p style="text-align: center; margin-top: 20px; color: #666;">
            Already have an account? <a href="<?= BASE_URL ?>auth/login" style="color: #3498db; font-weight: bold; text-decoration: none;">Login here</a>
        </p>
    </div>

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
        
        const roleContainer = document.getElementById('role-container');
        const roleSelect = document.getElementById('roleSelect');
        const adminBadge = document.getElementById('admin-badge');
        const adminHiddenInput = document.getElementById('adminHiddenInput');

        let emailTimer;

        // --- EMAIL CHECK + ADMIN DETECTION ---
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
                            emailFeedback.textContent = data.message;
                            emailFeedback.className = 'feedback error';
                        }
                    });
                }
            }, 500);
        });

        // --- PASSWORD STRENGTH METER ---
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
                passFeedback.className = 'feedback error';
            } else if (strength === 2) {
                strengthBar.className = 'medium';
                strengthBar.style.width = '66%';
                passFeedback.textContent = 'Medium';
                passFeedback.className = 'feedback';
                passFeedback.style.color = '#f39c12';
            } else if (strength >= 3) {
                strengthBar.className = 'strong';
                strengthBar.style.width = '100%';
                passFeedback.textContent = 'Strong';
                passFeedback.className = 'feedback success';
            }
        });

        // --- SUBMIT VALIDATION ---
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