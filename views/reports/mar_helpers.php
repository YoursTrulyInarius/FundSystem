<?php
function mar_form_data($report) {
    $data = json_decode($report['form_data'] ?? '', true);
    if (!is_array($data)) {
        $data = [];
    }
    $otherImages = $data['other_images'] ?? [];
    if (!is_array($otherImages)) {
        $otherImages = [];
    }
    $otherImages = array_values(array_filter($otherImages, function($path) {
        return is_string($path) && preg_match('~^uploads/mars/other_image_[a-f0-9]{32}\.(?:jpe?g|png|gif|webp)$~i', $path);
    }));

    return [
        'sessions' => is_array($data['sessions'] ?? null) ? $data['sessions'] : [],
        'activities' => is_array($data['activities'] ?? null) ? $data['activities'] : [],
        'resolutions' => is_array($data['resolutions'] ?? null) ? $data['resolutions'] : [],
        'signatories' => is_array($data['signatories'] ?? null) ? $data['signatories'] : [],
        'other_details' => is_string($data['other_details'] ?? null) ? $data['other_details'] : '',
        'other_images' => $otherImages
    ];
}

function mar_attachment_labels($report) {
    if (empty($report['form_data'])) {
        return [
            'session_minutes' => 'Session Minutes',
            'attendance_records' => 'Attendance Records',
            'post_activity_reports' => 'Post-Activity Reports',
            'financial_reports' => 'Financial Reports'
        ];
    }

    return [
        'session_minutes' => 'SK Minutes with Attendance and photo documentation',
        'attendance_records' => 'SK Resolutions',
        'post_activity_reports' => 'Post Activity Report with Pictures',
        'financial_reports' => 'Copy of Monthly Utilization Report (with DV)'
    ];
}

function mar_saved_lydo_remarks($report) {
    $data = json_decode($report['form_data'] ?? '', true);
    $savedRemarks = is_array($data['lydo_remarks'] ?? null) ? $data['lydo_remarks'] : [];
    $normalizeList = function($items) {
        if (!is_array($items)) {
            return [];
        }
        $normalized = [];
        foreach ($items as $item) {
            $normalized[] = is_string($item) ? $item : '';
        }
        return $normalized;
    };

    $attachments = is_array($savedRemarks['attachments'] ?? null) ? $savedRemarks['attachments'] : [];
    $normalizedAttachments = [];
    foreach (['session_minutes', 'attendance_records', 'post_activity_reports', 'financial_reports', 'other'] as $key) {
        $normalizedAttachments[$key] = is_string($attachments[$key] ?? null) ? $attachments[$key] : '';
    }

    return [
        'activities' => $normalizeList($savedRemarks['activities'] ?? []),
        'resolutions' => $normalizeList($savedRemarks['resolutions'] ?? []),
        'attachments' => $normalizedAttachments
    ];
}

function mar_details_payload($report) {
    $form = mar_form_data($report);
    $attachmentLabels = mar_attachment_labels($report);
    $attachments = array_values($attachmentLabels);
    $attachmentKeys = array_keys($attachmentLabels);
    if ($form['other_details'] !== '' || !empty($form['other_images'])) {
        $attachments[] = $form['other_details'] !== '' ? 'OTHER: ' . $form['other_details'] : 'OTHER';
    } else {
        $attachments[] = 'OTHER';
    }
    $attachmentKeys[] = 'other';
    $role = $_SESSION['role'] ?? '';
    $status = $report['status'] ?? '';
    $canViewLydoRemarks = ($role === 'lydo' && $status === 'pending') ||
        ($status === 'returned' && in_array($role, ['lydo', 'sk_admin'], true));
    $lydoRemarks = $canViewLydoRemarks
        ? mar_saved_lydo_remarks($report)
        : [
            'activities' => [],
            'resolutions' => [],
            'attachments' => array_fill_keys(['session_minutes', 'attendance_records', 'post_activity_reports', 'financial_reports', 'other'], '')
        ];

    return [
        'report_id' => (int)($report['id'] ?? 0),
        'month' => (int)($report['month'] ?? 0),
        'year' => (string)($report['year'] ?? ''),
        'barangay' => (string)($report['barangay_name'] ?? ''),
        'submitter' => (string)($report['submitter'] ?? ''),
        'form' => $form,
        'attachments' => $attachments,
        'attachment_keys' => $attachmentKeys,
        'other_images' => $form['other_images'],
        'reason' => (string)($report['remarks'] ?? ''),
        'lydo_remarks' => $lydoRemarks,
        'can_edit_remarks' => false
    ];
}
?>
