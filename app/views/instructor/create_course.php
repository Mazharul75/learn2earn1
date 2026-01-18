<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Create New Course</h2>
    
    <?php if(isset($data['error'])): ?>
        <div class="alert alert-danger"><?= $data['error'] ?></div>
    <?php endif; ?>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 600px;">
        <form action="<?= BASE_URL ?>instructor/create" method="POST">
            <label>Course Title:</label>
            <input type="text" name="title" required style="width: 100%; margin-bottom: 10px; padding: 8px;">

            <label>Description:</label>
            <textarea name="description" required style="width: 100%; height: 100px; margin-bottom: 10px; padding: 8px;"></textarea>

            <label>Difficulty Level:</label>
            <select name="difficulty" id="diffSelect" required onchange="togglePrereq()" style="width: 100%; margin-bottom: 10px; padding: 8px;">
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>
            <br>

            <div id="prereqSection" style="display:none; background: #f8f9fa; padding: 15px; border-left: 4px solid #e67e22; margin-bottom: 20px;">
                <label style="color: #d35400;">⚠️ Select Prerequisite Course:</label>
                <small>Students must complete this course before enrolling.</small><br>
                <select name="prerequisite_id" style="width: 100%; padding: 8px;">
                    <option value="">-- Select a Course --</option>
                    <?php if(!empty($data['courses'])): ?>
                        <?php foreach($data['courses'] as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['title'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <label>Maximum Class Size:</label>
                    <input type="number" name="max_capacity" placeholder="50" required min="1" style="width: 100%; padding: 8px;">
                </div>
                <div style="flex: 1;">
                    <label>Reserved Seats:</label>
                    <input type="number" name="reserved_seats" placeholder="5" required min="0" style="width: 100%; padding: 8px;">
                </div>
            </div>
            <br>

            <button type="submit" class="btn">Create Course</button>
            <a href="<?= BASE_URL ?>instructor/index" class="btn" style="background: #95a5a6; text-decoration: none;">Cancel</a>
        </form>
    </div>

    <script>
        function togglePrereq() {
            var diff = document.getElementById('diffSelect').value;
            var section = document.getElementById('prereqSection');
            
            if (diff === 'Intermediate' || diff === 'Advanced') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        }
        window.onload = togglePrereq;
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>