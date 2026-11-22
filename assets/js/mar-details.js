let activeMarDetailsPayload = null;

function marText(value) {
    return value === null || value === undefined || value === '' ? '' : String(value);
}

function marElement(tag, text, className) {
    const element = document.createElement(tag);
    if (text !== undefined) element.textContent = text;
    if (className) element.className = className;
    return element;
}

function marGridTable(widths, headerRows, bodyRows) {
    const scroll = marElement('div', undefined, 'mar-table-scroll');
    const table = marElement('table', undefined, 'mar-grid');
    if (widths.length) {
        const colgroup = document.createElement('colgroup');
        widths.forEach((width) => {
            const col = document.createElement('col');
            col.style.width = width;
            colgroup.appendChild(col);
        });
        table.appendChild(colgroup);
    }

    const thead = document.createElement('thead');
    headerRows.forEach((row) => {
        const tr = document.createElement('tr');
        row.forEach((cell) => {
            const th = marElement('th', cell.text || '');
            if (cell.colspan) th.colSpan = cell.colspan;
            if (cell.rowspan) th.rowSpan = cell.rowspan;
            if (cell.className) th.className = cell.className;
            tr.appendChild(th);
        });
        thead.appendChild(tr);
    });
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    bodyRows.forEach((row) => {
        const tr = document.createElement('tr');
        row.forEach((value) => {
            const cell = value && typeof value === 'object' ? value : null;
            const td = marElement('td', cell && cell.element ? undefined : marText(cell ? cell.text : value));
            if (cell && cell.className) td.className = cell.className;
            if (cell && cell.ariaLabel) td.setAttribute('aria-label', cell.ariaLabel);
            if (cell && cell.element) td.appendChild(cell.element);
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    scroll.appendChild(table);
    return scroll;
}

function marSection(title, table) {
    const section = marElement('section', undefined, 'mar-section');
    section.appendChild(marElement('h3', title, 'mar-section-title'));
    section.appendChild(table);
    return section;
}

function marRemarksCell(payload, section, key, label, editable) {
    const remarks = payload.lydo_remarks || {};
    const value = section === 'attachments'
        ? (remarks.attachments || {})[key]
        : (remarks[section] || [])[key];
    if (!editable) {
        return { text: marText(value), className: 'mar-lydo-cell', ariaLabel: label };
    }

    const editor = document.createElement('textarea');
    editor.value = marText(value);
    editor.rows = 2;
    editor.maxLength = 1000;
    editor.className = 'w-full min-w-24 resize-y rounded border border-slate-300 bg-white px-2 py-1 text-xs';
    editor.setAttribute('aria-label', label);
    editor.dataset.lydoRemarkSection = section;
    editor.dataset.lydoRemarkKey = String(key);
    return { element: editor, className: 'mar-lydo-cell', ariaLabel: label };
}

function collectLydoRemarks(content) {
    const remarks = { activities: [], resolutions: [], attachments: {} };
    content.querySelectorAll('[data-lydo-remark-section]').forEach((field) => {
        const section = field.dataset.lydoRemarkSection;
        const key = field.dataset.lydoRemarkKey;
        if (section === 'attachments') {
            remarks.attachments[key] = field.value;
        } else {
            remarks[section][Number(key)] = field.value;
        }
    });
    return remarks;
}

async function saveLydoRemarks() {
    const payload = activeMarDetailsPayload;
    const content = document.getElementById('marDetailsContent');
    const saveButton = document.getElementById('saveLydoRemarks');
    const status = document.getElementById('lydoRemarksStatus');
    if (!payload || !payload.can_edit_remarks || !content || !saveButton || !status) return;

    const remarks = collectLydoRemarks(content);
    const formData = new FormData();
    formData.append('report_id', String(payload.report_id));
    formData.append('remarks', JSON.stringify(remarks));
    saveButton.disabled = true;
    status.textContent = 'Saving remarks...';
    status.className = 'mr-auto text-xs text-slate-600';
    try {
        const response = await fetch('api/lydo/save_mar_remarks', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Unable to save LYDO remarks.');
        }
        payload.lydo_remarks = remarks;
        status.textContent = 'Remarks saved. The SK Council sees them only if this MAR is rejected.';
    } catch (error) {
        status.textContent = error.message || 'Unable to save LYDO remarks.';
        status.className = 'mr-auto text-xs text-red-700';
        saveButton.disabled = false;
    }
}

window.openMarDetails = function(payload) {
    const content = document.getElementById('marDetailsContent');
    if (!content) return;
    activeMarDetailsPayload = payload;
    content.replaceChildren();
    const saveButton = document.getElementById('saveLydoRemarks');
    const status = document.getElementById('lydoRemarksStatus');
    if (saveButton) {
        saveButton.hidden = !payload.can_edit_remarks;
        saveButton.classList.toggle('hidden', !payload.can_edit_remarks);
        saveButton.disabled = true;
    }
    if (status) {
        status.textContent = payload.can_edit_remarks
            ? 'The SK Council sees these remarks only if this MAR is rejected.'
            : '';
        status.className = 'mr-auto text-xs text-slate-600';
    }

    const paper = marElement('article', undefined, 'mar-paper mar-view-paper');
    const header = marElement('div', undefined, 'mar-header');
    const lydoSeal = document.createElement('img');
    lydoSeal.src = '/FundSystem/assets/img/lydo-annex4-seal.png';
    lydoSeal.alt = 'Local Youth Development Office seal';
    const skSeal = document.createElement('img');
    skSeal.src = '/FundSystem/assets/img/sk-annex4-seal.png';
    skSeal.alt = 'Sangguniang Kabataan Ramon Magsaysay seal';
    const headerCopy = marElement('div', undefined, 'mar-header-copy');
    headerCopy.appendChild(marElement('p', 'Annex 4'));
    headerCopy.appendChild(marElement('p', 'REGION IX'));
    headerCopy.appendChild(marElement('p', 'PROVINCE OF ZAMBOANGA DEL SUR'));
    headerCopy.appendChild(marElement('p', 'MUNICIPALITY OF RAMON MAGSAYSAY'));
    header.append(lydoSeal, headerCopy, skSeal);
    paper.appendChild(header);

    paper.appendChild(marElement('h2', 'MONTHLY ACCOMPLISHMENT REPORT', 'mar-title'));
    const period = marElement('div', undefined, 'mar-period');
    period.appendChild(marElement('p', `MONTH OF ${payload.month ? new Date(2000, payload.month - 1, 1).toLocaleString('en-US', { month: 'long' }) : ''},`));
    period.appendChild(marElement('p', `CALENDAR YEAR ${marText(payload.year)}`));
    period.appendChild(marElement('p', `BARANGAY ${marText(payload.barangay)}`));
    paper.appendChild(period);

    const form = payload.form || {};
    const sessions = form.sessions || {};
    const sessionRows = ['regular', 'special'].map((type) => {
        const row = sessions[type] || {};
        return [
            type === 'regular' ? 'REGULAR' : 'SPECIAL /EMERGENCY',
            row.number,
            row.date,
            row.present_male,
            row.present_female,
            row.absent_male,
            row.absent_female
        ];
    });
    paper.appendChild(marSection('PART I. SANGGUNIANG KABATAAN COUNCIL MONTHLY SESSION CONDUCTED', marGridTable(
        ['21%', '10%', '17%', '13%', '13%', '13%', '13%'],
        [
            [{ text: 'TYPE', rowspan: 3 }, { text: 'NUMBER', rowspan: 3 }, { text: 'DATE CONDUCTED', rowspan: 3 }, { text: 'NO. OF MEMBERS', colspan: 4 }],
            [{ text: 'PRESENT', colspan: 2 }, { text: 'ABSENT', colspan: 2 }],
            [{ text: 'MALE' }, { text: 'FEMALE' }, { text: 'MALE' }, { text: 'FEMALE' }]
        ],
        sessionRows
    )));

    const activities = Array.isArray(form.activities) ? form.activities : [];
    paper.appendChild(marSection('PART II. PROGRAMS/PROJECTS/ACTIVITIES UNDERTAKEN', marGridTable(
        ['25%', '20%', '13%', '12%', '12%', '18%'],
        [
            [
                { text: 'NAME/DESCRIPTION PPAS UNDERTAKEN/CONDUCTED', rowspan: 2 },
                { text: 'CENTER OF YOUTH PARTICIPATION', rowspan: 2 },
                { text: 'AMOUNT UTILIZED', rowspan: 2 },
                { text: 'DATE', colspan: 2 },
                { text: 'REMARKS', rowspan: 2 }
            ],
            [{ text: 'STARTED' }, { text: 'COMPLETED' }]
        ],
        (activities.length ? activities : [{}]).map((row, index) => [
            row.description,
            row.center,
            row.amount,
            row.started,
            row.completed,
            marRemarksCell(payload, 'activities', index, `LYDO remarks for activity ${index + 1}`, activities.length > 0 && payload.can_edit_remarks)
        ])
    )));

    const resolutions = Array.isArray(form.resolutions) ? form.resolutions : [];
    paper.appendChild(marSection('PART III. SK RESOLUTIONS PASSED', marGridTable(
        ['76%', '24%'],
        [[
            { text: 'TITLE NO. & DESCRIPTION OF RESOLUTIONS PASSED DURING SK MONTHLY SESSION' },
            { text: 'REMARKS' }
        ]],
        (resolutions.length ? resolutions : ['']).map((description, index) => [
            description,
            marRemarksCell(payload, 'resolutions', index, `LYDO remarks for resolution ${index + 1}`, resolutions.length > 0 && payload.can_edit_remarks)
        ])
    )));

    const signatories = form.signatories || {};
    const signatures = marElement('section', undefined, 'mar-signatures');
    signatures.appendChild(marElement('p', 'Prepared by:'));
    const prepared = marElement('div', undefined, 'mar-signature-pair');
    [['SK Secretary', signatories.secretary], ['SK Treasurer', signatories.treasurer]].forEach(([role, name]) => {
        const item = marElement('div', undefined, 'mar-signature-item');
        item.appendChild(marElement('span', marText(name), 'mar-signature-name'));
        item.appendChild(marElement('span', role));
        prepared.appendChild(item);
    });
    signatures.appendChild(prepared);

    const kagawads = marElement('div', undefined, 'mar-kagawad-grid');
    (signatories.kagawads || ['', '', '', '', '', '']).forEach((name) => {
        const item = marElement('div', undefined, 'mar-signature-item');
        item.appendChild(marElement('span', marText(name), 'mar-signature-name'));
        item.appendChild(marElement('span', 'SK Kagawad'));
        kagawads.appendChild(item);
    });
    signatures.appendChild(kagawads);

    const chairperson = marElement('div', undefined, 'mar-chairperson');
    chairperson.appendChild(marElement('p', 'Noted By:'));
    chairperson.appendChild(marElement('span', marText(signatories.chairperson), 'mar-signature-name'));
    chairperson.appendChild(marElement('p', 'SK Chairperson'));
    signatures.appendChild(chairperson);

    const attachments = payload.attachments || [
        'SK Minutes with Attendance and photo documentation',
        'SK Resolutions',
        'Post Activity Report with Pictures',
        'Copy of Monthly Utilization Report (with DV)'
    ];
    const attachmentSection = marElement('section', undefined, 'mar-attachments');
    const attachmentTable = marGridTable(
        ['72%', '28%'],
        [
            [{ text: 'With Complete Attachments:', colspan: 2, className: 'mar-attachments-title' }],
            [{ text: 'DOCUMENTS' }, { text: 'REMARKS' }]
        ],
        attachments.map((name, index) => [
            name,
            marRemarksCell(
                payload,
                'attachments',
                (payload.attachment_keys || [])[index],
                `LYDO remarks for ${name}`,
                payload.can_edit_remarks
            )
        ])
    );
    const otherImagePaths = Array.isArray(payload.other_images)
        ? payload.other_images.filter((path) => typeof path === 'string' &&
            /^uploads\/mars\/other_image_[a-f0-9]{32}\.(?:jpe?g|png|gif|webp)$/i.test(path))
        : [];
    const otherRow = [...attachmentTable.querySelectorAll('tbody tr')].find((row) =>
        row.firstElementChild.textContent.startsWith('OTHER')
    );
    const otherCell = otherRow && otherRow.firstElementChild;
    if (otherCell && otherImagePaths.length) {
        const imageList = marElement('div', undefined, 'mar-other-image-list');
        otherImagePaths.forEach((path, index) => {
            const link = marElement('a');
            link.href = path;
            link.target = '_blank';
            link.rel = 'noopener';
            const image = document.createElement('img');
            image.src = path;
            image.alt = `OTHER image ${index + 1}`;
            link.appendChild(image);
            imageList.appendChild(link);
        });
        otherCell.appendChild(imageList);
    }
    attachmentSection.appendChild(attachmentTable);
    const signingLayout = marElement('div', undefined, 'mar-signing-layout');
    signingLayout.append(signatures, attachmentSection);
    paper.appendChild(signingLayout);

    const lydo = marElement('div', undefined, 'mar-lydo-signature');
    lydo.appendChild(marElement('p', 'Received and Reviewed By:'));
    lydo.appendChild(marElement('span', undefined, 'mar-signature-line'));
    lydo.appendChild(marElement('strong', 'JISSA THEA T. MAGBANUA MPA, CHRA'));
    lydo.appendChild(marElement('p', 'Local Youth Development Officer'));
    paper.appendChild(lydo);
    content.appendChild(paper);

    if (payload.reason) {
        const reason = marElement('div', undefined, 'mar-review-reason');
        reason.appendChild(marElement('strong', 'Rejection reason'));
        reason.appendChild(marElement('p', payload.reason, 'mt-1'));
        content.appendChild(reason);
    }

    const modal = document.getElementById('marDetailsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.closeMarDetails = function() {
    activeMarDetailsPayload = null;
    const modal = document.getElementById('marDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const content = document.getElementById('marDetailsContent');
    const saveButton = document.getElementById('saveLydoRemarks');
    const status = document.getElementById('lydoRemarksStatus');
    if (saveButton) saveButton.addEventListener('click', saveLydoRemarks);
    if (content) {
        content.addEventListener('input', (event) => {
            if (!event.target.matches('[data-lydo-remark-section]') || !saveButton || !status) return;
            saveButton.disabled = false;
            status.textContent = 'Unsaved changes.';
            status.className = 'mr-auto text-xs text-amber-700';
        });
    }

    document.querySelectorAll('[data-mar-details]').forEach((button) => {
        button.addEventListener('click', () => {
            try {
                window.openMarDetails(JSON.parse(button.dataset.marDetails));
            } catch (error) {
                console.error('Unable to display MAR details.', error);
            }
        });
    });
});
