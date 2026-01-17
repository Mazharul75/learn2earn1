<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <h2>Create New Course</h2>
    
    <?php if(isset($data['error'])): ?>
        <div class="alert alert-danger"><?= $data['error'] ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>instructor/create" method="POST">
        <label>Course Title:</label>
        <input type="text" name="title" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Difficulty Level:</label>
        <select name="difficulty" id="diffSelect" required onchange="togglePrereq()">
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
        </select>
        <br>

        <div id="prereqSection" style="display:none; background: #f8f9fa; padding: 15px; border-left: 4px solid #e67e22; margin-bottom: 20px;">
            <label style="color: #d35400;">⚠️ Select Prerequisite Course:</label>
            <small>Students must complete this course before enrolling.</small><br>
            <select name="prerequisite_id">
                <option value="">-- Select a Course --</option>
                <?php if(!empty($data['courses'])): ?>
                    <?php foreach($data['courses'] as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['title'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <label>Maximum Class Size:</label>
        <input type="number" name="max_capacity" placeholder="50" required min="1">

        <label>Reserved Seats:</label>
        <input type="number" name="reserved_seats" placeholder="5" required min="0">

        <button type="submit">Create Course</button>
    </form>

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
        
        // Run on load just in case (e.g. if browser cached the selection)
        window.onload = togglePrereq;
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>