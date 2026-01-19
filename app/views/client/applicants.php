<?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Applicants for: <span style="color: #e74c3c;"><?= $job['title']; ?></span></h2>
        <a href="<?= BASE_URL ?>client/index" class="btn btn-secondary">← Go Back</a>
    </div>

    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">1. Direct Applicants</h3>
    
    <input type="text"
       id="applicantSearch"
       placeholder="🔍 Search applicant..."
       onkeyup="searchApplicants()"
       style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;">


    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 40px;">
        <table border="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                <tr>
                    <th style="padding: 15px; text-align: left; color: #555;">Name</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Email</th>
                    <th style="padding: 15px; text-align: left; color: #555;">CV</th>
                    <th style="padding: 15px; text-align: left; color: #555;">Status</th>
                    <th style="padding: 15px; text-align: center; color: #555;">Action</th>
                </tr>
            </thead>
            <tbody id="applicantTableBody">
            <?php if(!empty($applicants)): ?>
                <?php foreach($applicants as $app): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 500;"><?= $app['name']; ?></td>
                    <td style="padding: 15px; color: #666;"><?= $app['email']; ?></td>
                    
                    <td style="padding: 15px;">
                        <?php if(!empty($app['cv_file'])): ?>
                            <a href="<?= BASE_URL ?>public/uploads/cvs/<?= $app['cv_file']; ?>" target="_blank" style="text-decoration: none; color: #e74c3c; font-weight: bold;">
                                📥 Download PDF
                            </a>
                        <?php else: ?>
                            <span style="color: #999;">No CV</span>
                        <?php endif; ?>
                    </td>

                    <td style="padding: 15px;">
                        <?php if($app['status'] == 'selected'): ?>
                            <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Selected</span>
                        <?php elseif($app['status'] == 'rejected'): ?>
                            <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Rejected</span>
                        <?php elseif($app['status'] == 'invited'): ?>
                            <span style="background: #e2e8f0; color: #4a5568; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Invited</span>
                        <?php else: ?>
                            <span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Applied</span>
                        <?php endif; ?>
                    </td>

                    <td style="padding: 15px; text-align: center;">
                        <?php if($app['status'] == 'applied'): ?>
                            <a href="<?= BASE_URL ?>client/updateApplication/<?= $app['app_id']; ?>/selected" class="btn" style="padding: 6px 12px; font-size: 0.85rem; background: #27ae60; text-decoration: none;">
                                Hire
                            </a>
                            <a href="<?= BASE_URL ?>client/updateApplication/<?= $app['app_id']; ?>/rejected" class="btn" style="padding: 6px 12px; font-size: 0.85rem; background: #c0392b; text-decoration: none; margin-left: 5px;">
                                Reject
                            </a>
                        <?php else: ?>
                            <span style="color: #7f8c8d; font-style: italic;">Decision Made</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="padding: 20px; text-align: center; color: #7f8c8d;">No applicants yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php 
        $existing_ids = [];
        if (!empty($applicants)) {
            foreach($applicants as $app) {
                if (isset($app['learner_id'])) {
                    $existing_ids[] = $app['learner_id'];
                }
            }
        }
    ?>

    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">2. 🌟 Instructor Recommendations</h3>
    <div style="background: #fff8e1; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 20px;">
        <p style="margin: 0; color: #b7791f;">Instructors have vetted these students as top performers for your requirements.</p>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <table border="0" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #fdf2e9; border-bottom: 2px solid #fae5d3;">
                <tr>
                    <th style="padding: 15px; text-align: left; color: #d35400;">Recommended Learner</th>
                    <th style="padding: 15px; text-align: left; color: #d35400;">Endorsed By</th>
                    <th style="padding: 15px; text-align: center; color: #d35400;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($recommendations)): ?>
                <?php foreach($recommendations as $rec): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 500;">
                        <?= $rec['learner_name'] ?> <br>
                        <small style="color: #666;"><?= $rec['learner_email'] ?></small>
                    </td>
                    <td style="padding: 15px; color: #555;">
                        Instructor: <strong><?= $rec['instructor_name'] ?></strong>
                    </td>
                    
                    <td style="padding: 15px; text-align: center;">
                        <?php if (in_array($rec['learner_id'], $existing_ids)): ?>
                            <button disabled style="background: #bdc3c7; color: #fff; border:none; padding: 6px 12px; border-radius: 4px; cursor: not-allowed; font-size: 0.85rem;">
                                ✔ Already Invited
                            </button>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>client/inviteLearner/<?= $rec['learner_id'] ?>/<?= $job['id'] ?>" class="btn" style="background: #e67e22; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;">
                                📩 Invite to Apply
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" style="padding: 20px; text-align: center; color: #7f8c8d;">No recommendations yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    function searchApplicants() {
        var query = document.getElementById('applicantSearch').value;
        var jobId = <?= $job['id'] ?>;
        var url = '<?= BASE_URL ?>client/searchApplicantsApi?job_id=' + jobId + '&query=' + query;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = JSON.parse(this.responseText);
                var tbody = document.getElementById('applicantTableBody');
                tbody.innerHTML = ''; 

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="padding:20px; text-align:center;">No applicants found.</td></tr>';
                    return;
                }

                for (var i = 0; i < data.length; i++) {
                    var app = data[i];

                    var statusBadge = '';
                    if(app.status == 'selected') statusBadge = '<span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Selected</span>';
                    else if(app.status == 'rejected') statusBadge = '<span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Rejected</span>';
                    else if(app.status == 'invited') statusBadge = '<span style="background: #e2e8f0; color: #4a5568; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Invited</span>';
                    else statusBadge = '<span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Applied</span>';

                    var actionButtons = '';
                    if(app.status == 'applied') {
                        actionButtons = `<a href="<?= BASE_URL ?>client/updateApplication/${app.app_id}/selected" class="btn" style="padding: 6px 12px; font-size: 0.85rem; background: #27ae60; text-decoration: none;">Hire</a> ` +
                                        `<a href="<?= BASE_URL ?>client/updateApplication/${app.app_id}/rejected" class="btn" style="padding: 6px 12px; font-size: 0.85rem; background: #c0392b; text-decoration: none; margin-left: 5px;">Reject</a>`;
                    } else {
                        actionButtons = '<span style="color: #7f8c8d; font-style: italic;">Decision Made</span>';
                    }

                    var cvLink = app.cv_file ? `<a href="<?= BASE_URL ?>public/uploads/cvs/${app.cv_file}" target="_blank" style="text-decoration: none; color: #e74c3c; font-weight: bold;">📥 Download PDF</a>` : '<span style="color: #999;">No CV</span>';

                    var row = `<tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 15px; font-weight: 500;">${app.name}</td>
                                    <td style="padding: 15px; color: #666;">${app.email}</td>
                                    <td style="padding: 15px;">${cvLink}</td>
                                    <td style="padding: 15px;">${statusBadge}</td>
                                    <td style="padding: 15px; text-align: center;">${actionButtons}</td>
                                   </tr>`;
                    tbody.innerHTML += row;
                }
            }
        };
        xhr.open("GET", url, true);
        xhr.send();
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>