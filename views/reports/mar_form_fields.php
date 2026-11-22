<article class="mar-paper" aria-label="Annex 4 Monthly Accomplishment Report form">
    <div class="mar-header">
        <img src="/FundSystem/assets/img/lydo-annex4-seal.png" alt="Local Youth Development Office seal">
        <div class="mar-header-copy">
            <p><strong>Annex 4</strong></p>
            <p>REGION <span>IX</span></p>
            <p>PROVINCE OF <span>ZAMBOANGA DEL SUR</span></p>
            <p>MUNICIPALITY OF <span>RAMON MAGSAYSAY</span></p>
        </div>
        <img src="/FundSystem/assets/img/sk-annex4-seal.png" alt="Sangguniang Kabataan Ramon Magsaysay seal">
    </div>

    <h2 class="mar-title">MONTHLY ACCOMPLISHMENT REPORT</h2>
    <div class="mar-period">
        <p class="mar-period-row">
            <span>MONTH OF</span>
            <select name="month" required class="mar-fill-line mar-period-month" aria-label="Month">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= (int)date('n') === $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                <?php endfor; ?>
            </select>,
        </p>
        <p class="mar-period-row">
            <span>CALENDAR YEAR</span>
            <input type="number" name="year" min="1901" max="2155" value="<?= date('Y') ?>" required class="mar-fill-line mar-period-year" aria-label="Calendar year">
        </p>
        <p class="mar-period-row">
            <span>BARANGAY</span>
            <input type="text" value="<?= htmlspecialchars($_SESSION['barangay_name'] ?? '') ?>" readonly class="mar-fill-line mar-period-barangay" aria-label="Barangay">
        </p>
    </div>

    <section class="mar-section">
        <h3 class="mar-section-title">PART I. SANGGUNIANG KABATAAN COUNCIL MONTHLY SESSION CONDUCTED</h3>
        <div class="mar-table-scroll">
            <table class="mar-grid">
                <colgroup>
                    <col style="width: 21%">
                    <col style="width: 10%">
                    <col style="width: 17%">
                    <col style="width: 13%">
                    <col style="width: 13%">
                    <col style="width: 13%">
                    <col style="width: 13%">
                </colgroup>
                <thead>
                    <tr>
                        <th rowspan="3">TYPE</th>
                        <th rowspan="3">NUMBER</th>
                        <th rowspan="3">DATE CONDUCTED</th>
                        <th colspan="4">NO. OF MEMBERS</th>
                    </tr>
                    <tr>
                        <th colspan="2">PRESENT</th>
                        <th colspan="2">ABSENT</th>
                    </tr>
                    <tr>
                        <th>MALE</th>
                        <th>FEMALE</th>
                        <th>MALE</th>
                        <th>FEMALE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (['regular' => 'REGULAR', 'special' => 'SPECIAL /EMERGENCY'] as $type => $label): ?>
                        <tr>
                            <td><?= $label ?></td>
                            <td><input type="number" min="0" max="9999" name="sessions[<?= $type ?>][number]" value="0" required class="mar-cell-input" aria-label="<?= $label ?> number"></td>
                            <td><input type="date" name="sessions[<?= $type ?>][date]" class="mar-cell-input" aria-label="<?= $label ?> date"></td>
                            <?php foreach (['present_male', 'present_female', 'absent_male', 'absent_female'] as $field): ?>
                                <td><input type="number" min="0" max="9999" name="sessions[<?= $type ?>][<?= $field ?>]" value="0" required class="mar-cell-input" aria-label="<?= $label ?> <?= str_replace('_', ' ', $field) ?>"></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="mar-section">
        <h3 class="mar-section-title">PART II. PROGRAMS/PROJECTS/ACTIVITIES UNDERTAKEN</h3>
        <div class="mar-table-scroll">
            <table class="mar-grid">
                <colgroup>
                    <col style="width: 25%">
                    <col style="width: 20%">
                    <col style="width: 13%">
                    <col style="width: 12%">
                    <col style="width: 12%">
                    <col style="width: 18%">
                </colgroup>
                <thead>
                    <tr>
                        <th rowspan="2">NAME/DESCRIPTION PPAS UNDERTAKEN/CONDUCTED</th>
                        <th rowspan="2">CENTER OF YOUTH PARTICIPATION</th>
                        <th rowspan="2">AMOUNT UTILIZED</th>
                        <th colspan="2">DATE</th>
                        <th rowspan="2">REMARKS</th>
                    </tr>
                    <tr>
                        <th>STARTED</th>
                        <th>COMPLETED</th>
                    </tr>
                </thead>
                <tbody id="activityRows"></tbody>
            </table>
        </div>
        <button type="button" data-add-activity class="mar-row-control">+ Add activity</button>
    </section>

    <section class="mar-section">
        <h3 class="mar-section-title">PART III. SK RESOLUTIONS PASSED</h3>
        <div class="mar-table-scroll">
            <table class="mar-grid">
                <colgroup>
                    <col style="width: 76%">
                    <col style="width: 24%">
                </colgroup>
                <thead>
                    <tr>
                        <th>TITLE NO. &amp; DESCRIPTION OF RESOLUTIONS PASSED DURING SK MONTHLY SESSION</th>
                        <th>REMARKS</th>
                    </tr>
                </thead>
                <tbody id="resolutionRows"></tbody>
            </table>
        </div>
        <button type="button" data-add-resolution class="mar-row-control">+ Add resolution</button>
    </section>

    <div class="mar-signing-layout">
        <section class="mar-signatures">
            <p>Prepared by:</p>
            <div class="mar-signature-pair">
                <label class="mar-signature-item"><input name="signatories[secretary]" maxlength="150" data-mar-text class="mar-signature-name" aria-label="SK Secretary"><span>SK Secretary</span></label>
                <label class="mar-signature-item"><input name="signatories[treasurer]" maxlength="150" data-mar-text class="mar-signature-name" aria-label="SK Treasurer"><span>SK Treasurer</span></label>
            </div>
            <div class="mar-kagawad-grid">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <label class="mar-signature-item"><input name="signatories[kagawads][<?= $i ?>]" maxlength="150" data-mar-text class="mar-signature-name" aria-label="SK Kagawad <?= $i + 1 ?>"><span>SK Kagawad</span></label>
                <?php endfor; ?>
            </div>
            <div class="mar-chairperson">
                <p>Noted By:</p>
                <input name="signatories[chairperson]" maxlength="150" data-mar-text class="mar-signature-name" aria-label="SK Chairperson">
                <p>SK Chairperson</p>
            </div>
        </section>

        <section class="mar-attachments">
        <div class="mar-table-scroll">
            <table class="mar-grid">
                <colgroup>
                    <col style="width: 72%">
                    <col style="width: 28%">
                </colgroup>
                <thead>
                    <tr><th colspan="2" class="mar-attachments-title">With Complete Attachments:</th></tr>
                    <tr><th>DOCUMENTS</th><th>REMARKS</th></tr>
                </thead>
                <tbody>
                    <?php
                    $attachments = [
                        'session_minutes' => 'SK Minutes with Attendance and photo documentation',
                        'attendance_records' => 'SK Resolutions',
                        'post_activity_reports' => 'Post Activity Report with Pictures',
                        'financial_reports' => 'Copy of Monthly Utilization Report (with DV)'
                    ];
                    foreach ($attachments as $field => $label):
                    ?>
                        <tr>
                            <td>
                                <span><?= $label ?></span>
                                <input type="file" name="<?= $field ?>" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required data-required-document class="mar-upload">
                                <span data-existing-document="<?= $field ?>" class="mar-upload-status" hidden></span>
                            </td>
                            <td class="mar-lydo-cell" aria-label="Reserved for LYDO remarks"></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td>
                            <strong>OTHER</strong>
                            <textarea name="other_details" rows="2" maxlength="1000" data-mar-text class="mar-cell-input" aria-label="Other attachment details"></textarea>
                            <input type="file" name="other_images[]" accept="image/*" multiple data-other-images class="mar-upload" aria-label="Optional OTHER images; add up to five">
                            <span data-other-image-status class="mar-upload-status">Optional — upload 1–5 images, or leave blank.</span>
                        </td>
                        <td class="mar-lydo-cell" aria-label="Reserved for LYDO remarks"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        </section>
    </div>

    <div class="mar-lydo-signature">
        <p>Received and Reviewed By:</p>
        <span class="mar-signature-line"></span>
        <strong>JISSA THEA T. MAGBANUA MPA, CHRA</strong>
        <p>Local Youth Development Officer</p>
    </div>
</article>

<template id="activityRowTemplate">
    <tr>
        <td><textarea name="activities[__INDEX__][description]" rows="2" maxlength="2000" data-mar-text class="mar-cell-input" aria-label="PPA name and description"></textarea></td>
        <td><input name="activities[__INDEX__][center]" maxlength="255" data-mar-text class="mar-cell-input" aria-label="Center of youth participation"></td>
        <td><input type="number" name="activities[__INDEX__][amount]" min="0" step="0.01" class="mar-cell-input" aria-label="Amount utilized"></td>
        <td><input type="date" name="activities[__INDEX__][started]" class="mar-cell-input" aria-label="Activity start date"></td>
        <td><input type="date" name="activities[__INDEX__][completed]" class="mar-cell-input" aria-label="Activity completion date"></td>
        <td class="mar-lydo-cell" aria-label="Reserved for LYDO remarks"></td>
    </tr>
</template>

<template id="resolutionRowTemplate">
    <tr>
        <td><textarea name="resolutions[__INDEX__][description]" rows="2" maxlength="2000" data-mar-text class="mar-cell-input" aria-label="Resolution title and description"></textarea></td>
        <td class="mar-lydo-cell" aria-label="Reserved for LYDO remarks"></td>
    </tr>
</template>
