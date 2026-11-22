<?php
$isAnnex2Certificate = !empty($cert['review_status']);
$isLydoApproval = $cert['type'] === 'review';
$isProceedAuthorization = in_array($cert['tx_type'], ['disbursement', 'procurement'], true);
$certificateTitle = $isAnnex2Certificate
    ? 'Certification of Completeness and Review'
    : ($isLydoApproval ? 'Certificate of LYDO Approval' : 'Certificate of Official Recording');
$issuerTitle = $cert['issuer_role'] === 'lydo'
    ? 'Local Youth Development Officer'
    : 'SK Federation President';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($certificateTitle) ?> - <?= htmlspecialchars($cert['project_title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/FundSystem/assets/css/mobile-responsive.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .certificate-title { font-family: 'DM Serif Display', Georgia, serif; }
        .certificate-paper {
            background:
                radial-gradient(ellipse at top left, rgba(219, 234, 254, .55), transparent 34%),
                #fff;
        }
        @page {
            size: A4 portrait;
            margin: <?= $isAnnex2Certificate ? '22mm 14mm 14mm' : '12mm' ?>;
        }
        .annex2-shell { background: #fff; margin: 0 auto; max-width: 210mm; }
        .annex2-paper {
            box-sizing: border-box;
            background: #fff;
            color: #111;
            font-family: Tahoma, Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0 auto;
            min-height: 297mm;
            padding: 22mm 14mm 14mm;
            position: relative;
            width: 210mm;
        }
        .annex2-header {
            align-items: center;
            display: flex;
            height: 1in;
            justify-content: center;
            left: 0;
            position: absolute;
            right: 0;
            text-align: center;
            top: 0.08in;
        }
        .annex2-header img { height: 0.91in; object-fit: contain; position: absolute; width: 0.91in; }
        .annex2-municipal-seal { left: 0.17in; }
        .annex2-header img.annex2-lydo-seal { height: 1.25in; left: auto; right: 0.12in; width: 1.25in; }
        .annex2-header-copy { font-family: "Bahnschrift SemiLight", Bahnschrift, Arial, sans-serif; line-height: 1.1; }
        .annex2-header-copy { margin: 0 1.48in 0 1.28in; }
        .annex2-header-copy p { margin: 0; }
        .annex2-municipality { font-size: 9pt; }
        .annex2-address { font-size: 9pt; }
        .annex2-office { font-family: "Bahnschrift SemiBold Condensed", Bahnschrift, Arial, sans-serif; font-size: 20pt; font-weight: 700; margin-top: 4px !important; }
        .annex2-label { border: 1px solid #111; display: table; font-family: Arial, sans-serif; font-size: 9pt; font-weight: 700; margin: 0 auto 3px; padding: 1px 14px; }
        .annex2-title { font-size: 12pt; font-weight: 700; margin: 0 0 20px; text-align: center; }
        .annex2-body p { margin: 0 0 12px; }
        .annex2-intro { text-align: justify; }
        .annex2-options { list-style: none; margin: 0 0 12px; padding: 0 0 0 0.35in; }
        .annex2-options li { margin: 0 0 3px; }
        .annex2-check { border: 1px solid #111; display: inline-block; height: 12px; margin: 0 5px 0 0; position: relative; vertical-align: -1px; width: 12px; }
        .annex2-check.selected::after { border-bottom: 1.5px solid #111; border-right: 1.5px solid #111; content: ""; height: 7px; left: 3px; position: absolute; top: 0; transform: rotate(45deg); width: 4px; }
        .annex2-records { margin-top: 0; }
        .annex2-field-row { margin-bottom: 2px !important; }
        .annex2-field-label { font-weight: 700; }
        .annex2-field-value { border-bottom: 1px solid #111; display: inline-block; font-weight: 400; min-width: 3.15in; padding: 0 2px; }
        .annex2-field-value.barangay { background: #fef3c7; border-bottom: 2px solid #d97706; border-radius: 2px; color: #78350f; font-weight: 700; padding: 0 5px; }
        .annex2-field-value.project { min-width: 3.35in; }
        .annex2-statement { margin-top: 14px !important; text-align: justify; }
        .annex2-issued { margin-top: 20px !important; }
        .annex2-signed { margin-top: 18px !important; }
        .annex2-signature-space { height: 0.65in; }
        .annex2-signature-title { font-weight: 700; margin: 0 !important; }
        .annex2-footer {
            align-items: flex-end;
            bottom: 1.77in;
            display: flex;
            font-family: "Bahnschrift Light SemiCondensed", Bahnschrift, Arial, sans-serif;
            justify-content: space-between;
            left: 0.15in;
            position: absolute;
            right: 0.1in;
        }
        .annex2-footer-copy { flex: 1; font-size: 6pt; line-height: 1.3; text-align: center; }
        .annex2-footer-copy p { margin: 1px 0; }
        .annex2-motto { font-family: "Bahnschrift SemiLight", Bahnschrift, Arial, sans-serif; font-size: 8pt; font-style: italic; }
        .annex2-footer-contact { align-items: center; display: flex; gap: 3px; justify-content: center; }
        .annex2-footer-contact img { height: 0.12in; object-fit: contain; width: 0.12in; }
        .annex2-footer-brands { align-items: flex-end; display: flex; gap: 5px; margin-left: 7px; }
        .annex2-footer-brands img { object-fit: contain; }
        .annex2-dpo-seal { height: 0.82in; width: 0.6in; }
        .annex2-qr { height: 0.55in; width: 0.55in; }
        .annex2-bagong { height: 0.5in; width: 0.55in; }
        @media print {
            body { background: #fff !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .certificate-shell { max-width: none !important; margin: 0 !important; box-shadow: none !important; }
            .certificate-paper { min-height: 270mm !important; }
            .annex2-paper { margin: 0 !important; min-height: 261mm !important; padding: 0 !important; width: auto !important; }
            .annex2-header { left: 0; right: 0; top: -21mm; }
            .annex2-municipal-seal { left: -9mm; }
            .annex2-header img.annex2-lydo-seal { left: auto; right: -9mm; }
            .annex2-footer { bottom: 14mm; }
            .annex2-shell { border: 0 !important; margin: 0 !important; max-width: none !important; overflow: visible !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
    <link rel="stylesheet" href="/FundSystem/assets/css/municipal-theme.css">
</head>
<body class="certificate-page min-h-screen bg-slate-100 px-4 py-8 sm:py-12">
    <div class="no-print mx-auto mb-5 flex max-w-4xl items-center justify-between gap-3">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-slate-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
            Print Certificate
        </button>
    </div>

    <main class="<?= $isAnnex2Certificate ? 'annex2-shell' : 'certificate-shell mx-auto max-w-4xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl' ?>">
        <?php if ($isAnnex2Certificate): ?>
            <article class="annex2-paper">
                <header class="annex2-header">
                    <img class="annex2-municipal-seal" src="/FundSystem/assets/img/annex2-municipal-seal.png" alt="Municipality of Ramon Magsaysay seal">
                    <img class="annex2-lydo-seal" src="/FundSystem/assets/img/annex2-lydo-seal.png" alt="Local Youth Development Office seal">
                    <div class="annex2-header-copy">
                        <p class="annex2-municipality">MUNICIPALITY OF RAMON MAGSAYSAY</p>
                        <p class="annex2-address">Municipal Hall, Quezon St., Poblacion, Ramon Magsaysay</p>
                        <p class="annex2-address">Zamboanga del Sur, 7024 Philippines</p>
                        <p class="annex2-office">LOCAL YOUTH DEVELOPMENT OFFICE</p>
                    </div>
                </header>

                <div class="annex2-label">Annex 2</div>
                <h1 class="annex2-title">CERTIFICATION OF COMPLETENESS AND REVIEW</h1>

                <?php
                $reviewBarangay = preg_replace('/^Barangay\s+/i', '', $cert['review_barangay_name'] ?? '');
                $reviewOptions = [
                    'complete' => 'Complete and in order',
                    'minor_clarification' => 'Complete but subject to minor clarification',
                    'incomplete' => 'Incomplete and requiring submission of additional documents'
                ];
                ?>
                <div class="annex2-body">
                    <p class="annex2-intro">
                        This is to certify that the financial transaction documents and supporting records submitted by the Sangguniang Kabataan of Barangay
                        <span class="annex2-field-value barangay"><?= htmlspecialchars($reviewBarangay) ?></span>
                        have been reviewed by the Office of the Local Youth Development Officer pursuant to the requirements of the LYOFOST SK Fund Program.
                    </p>
                    <p>Upon review, it is hereby certified that the submitted documents are:</p>
                    <ul class="annex2-options" aria-label="Review outcome">
                        <?php foreach ($reviewOptions as $reviewValue => $reviewLabel): ?>
                            <li>
                                <span class="annex2-check<?= $cert['review_status'] === $reviewValue ? ' selected' : '' ?>" aria-hidden="true"></span>
                                <?= htmlspecialchars($reviewLabel) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="annex2-records">The records reviewed pertain to the following:</p>
                    <p class="annex2-field-row">
                        <span class="annex2-field-label">Reference Document No.:</span>
                        <span class="annex2-field-value"><?= htmlspecialchars($cert['reference_document_no'] ?? '') ?></span>
                    </p>
                    <p class="annex2-field-row">
                        <span class="annex2-field-label">Project/Program/Activity:</span>
                        <span class="annex2-field-value project"><?= htmlspecialchars($cert['project_activity'] ?? '') ?></span>
                    </p>
                    <p class="annex2-field-row">
                        <span class="annex2-field-label">Amount Involved (Php):</span>
                        <span class="annex2-field-value"> <?= number_format((float) ($cert['certificate_amount'] ?? $cert['amount']), 2) ?></span>
                    </p>
                    <p class="annex2-field-row">
                        <span class="annex2-field-label">Date of Transaction:</span>
                        <span class="annex2-field-value"><?= date('F d, Y', strtotime($cert['date_of_transaction'])) ?></span>
                    </p>
                    <p class="annex2-field-row">
                        <span class="annex2-field-label">Date Reviewed:</span>
                        <span class="annex2-field-value"><?= date('F d, Y', strtotime($cert['date_reviewed'])) ?></span>
                    </p>
                    <p class="annex2-statement">This certification is issued to attest that the submitted records have undergone document review and compliance verification for purposes of registration and monitoring under the LYOFOST SK Fund Program.</p>
                    <p class="annex2-issued">
                        Issued this <?= date('jS', strtotime($cert['cert_date'])) ?> day of <?= date('F', strtotime($cert['cert_date'])) ?>, <?= date('Y', strtotime($cert['cert_date'])) ?> at Ramon Magsaysay, Zamboanga del Sur.
                    </p>
                    <p class="annex2-signed">Signed:</p>
                    <div class="annex2-signature-space"></div>
                    <p class="annex2-signature-title">LOCAL YOUTH DEVELOPMENT OFFICER</p>
                </div>

                <footer class="annex2-footer">
                    <div class="annex2-footer-copy">
                        <p class="annex2-motto">Hapsay, Han-ay, Husay, Hamugaway nga Ramon Magsaysay</p>
                        <p class="annex2-motto">For the Filipino Youth, For the Liargaonon Youth</p>
                        <p class="annex2-footer-contact">
                            <img src="/FundSystem/assets/img/annex2-web-icon.png" alt="">
                            www.ramonmagsaysay.gov.ph
                            <img src="/FundSystem/assets/img/annex2-email-icon.png" alt="">
                            skliargao@gmail.com
                            <img src="/FundSystem/assets/img/annex2-facebook-icon.png" alt="">
                            SK LYDO Ramon Magsaysay
                        </p>
                    </div>
                    <div class="annex2-footer-brands">
                        <img class="annex2-dpo-seal" src="/FundSystem/assets/img/annex2-dpo-seal.jpeg" alt="Data Privacy Office registered">
                        <img class="annex2-qr" src="/FundSystem/assets/img/annex2-registry-qr.png" alt="Municipal registry QR code">
                        <img class="annex2-bagong" src="/FundSystem/assets/img/annex2-bagong-pilipinas.png" alt="Bagong Pilipinas">
                    </div>
                </footer>
            </article>
        <?php else: ?>
        <div class="h-2 bg-gradient-to-r from-blue-950 via-blue-700 to-cyan-500"></div>
        <article class="certificate-paper relative flex min-h-[980px] flex-col px-6 py-8 sm:px-14 sm:py-12">
            <div class="pointer-events-none absolute inset-4 rounded-xl border border-blue-100 sm:inset-6"></div>
            <header class="relative text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-blue-200 bg-blue-50 text-blue-800">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.2 1.4 2.6-.1 1.2 2.3 2.3 1.2-.1 2.6L21.6 13l-1.4 2.2.1 2.6-2.3 1.2-1.2 2.3-2.6-.1L12 22.6l-2.2-1.4-2.6.1L6 19l-2.3-1.2.1-2.6L2.4 13l1.4-2.2-.1-2.6L6 7l1.2-2.3 2.6.1L12 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.5 12.5 2.2 2.2 4.8-5"/>
                    </svg>
                </div>
                <p class="text-xs font-extrabold uppercase tracking-[0.24em] text-slate-500">Republic of the Philippines</p>
                <h1 class="mt-1 text-lg font-bold text-slate-800">Municipality of Ramon Magsaysay</h1>
                <p class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">Zamboanga del Sur · SK Fund System</p>

                <div class="mx-auto mt-8 max-w-2xl border-y border-blue-200 py-6">
                    <p class="text-xs font-extrabold uppercase tracking-[0.3em] text-blue-700"><?= $isLydoApproval ? 'Official Approval' : 'Official Record' ?></p>
                    <h2 class="certificate-title mt-3 text-3xl leading-tight text-slate-900 sm:text-4xl"><?= htmlspecialchars($certificateTitle) ?></h2>
                    <p class="mt-2 text-sm font-semibold uppercase tracking-[0.12em] text-slate-500"><?= htmlspecialchars($issuerTitle) ?></p>
                </div>
            </header>

            <section class="relative mt-8 flex-1">
                <?php if ($isLydoApproval): ?>
                    <div class="mb-7 flex items-center justify-center">
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-5 py-2 text-sm font-extrabold uppercase tracking-[0.12em] text-emerald-800">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                            <?= $isProceedAuthorization ? 'Approved to Proceed' : 'Transaction Approved' ?>
                        </span>
                    </div>
                    <p class="mx-auto max-w-3xl text-center text-base leading-8 text-slate-700 sm:text-lg">
                        This certifies that the transaction submitted by
                        <strong class="text-slate-900"><?= htmlspecialchars($cert['sk_name']) ?></strong>
                        of <strong class="text-slate-900"><?= htmlspecialchars($cert['barangay_name'] ?: 'the Municipality of Ramon Magsaysay') ?></strong>
                        for the project or activity identified below has been reviewed and approved by the Local Youth Development Officer.
                    </p>
                    <p class="mx-auto mt-4 max-w-3xl text-center text-sm leading-7 text-slate-600">
                        <?php if ($isProceedAuthorization): ?>
                            The project/activity is cleared to proceed with the approved scope and transaction, subject to the approved ABYIP, availability of funds, and applicable government rules and regulations.
                        <?php else: ?>
                            The transaction is approved for the project/activity stated below, subject to the approved ABYIP and applicable government rules and regulations.
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="mx-auto max-w-3xl text-center text-base leading-8 text-slate-700 sm:text-lg">
                        This certifies that the financial transaction submitted by
                        <strong class="text-slate-900"><?= htmlspecialchars($cert['sk_name']) ?></strong>
                        of <strong class="text-slate-900"><?= htmlspecialchars($cert['barangay_name'] ?: 'the Municipality of Ramon Magsaysay') ?></strong>
                        has been officially recorded by the Sangguniang Kabataan Federation.
                    </p>
                <?php endif; ?>

                <div class="mt-9 overflow-hidden rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between gap-3 bg-slate-900 px-5 py-3 text-white">
                        <h3 class="text-xs font-extrabold uppercase tracking-[0.2em]">Project & Transaction Details</h3>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider"><?= htmlspecialchars($cert['tx_type']) ?></span>
                    </div>
                    <div class="grid gap-px bg-slate-200 sm:grid-cols-2">
                        <div class="space-y-5 bg-white p-5 sm:p-6">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Project / Activity</p>
                                <p class="mt-1 text-base font-bold text-slate-900"><?= htmlspecialchars($cert['project_title']) ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Barangay</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800"><?= htmlspecialchars($cert['barangay_name'] ?: 'N/A') ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Reference Number</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800"><?= htmlspecialchars($cert['reference_no']) ?></p>
                            </div>
                        </div>
                        <div class="space-y-5 bg-white p-5 sm:p-6">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Approved Transaction Amount</p>
                                <p class="mt-1 text-2xl font-extrabold text-blue-800">₱<?= number_format((float)$cert['amount'], 2) ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Submitted</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800"><?= date('F d, Y', strtotime($cert['tx_date'])) ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Approval / Issue Date</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800"><?= date('F d, Y', strtotime($cert['cert_date'] ?: $cert['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-7 rounded-xl border-l-4 border-blue-600 bg-blue-50 px-5 py-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-900">Certificate Statement</p>
                    <p class="mt-1 text-sm leading-6 text-slate-700">
                        <?= $isLydoApproval
                            ? 'This approval applies to the transaction and project/activity stated on this certificate. It is not a substitute for any separate permit or authorization required by law.'
                            : 'This certificate confirms the transaction has been entered into the official SK Fund System record.' ?>
                    </p>
                </div>
            </section>

            <footer class="relative mt-10">
                <div class="flex flex-col-reverse items-start justify-between gap-8 sm:flex-row sm:items-end">
                    <div class="text-xs leading-6 text-slate-500">
                        <p class="font-bold text-slate-700">Certificate No. CERT-<?= str_pad((string)$cert['id'], 6, '0', STR_PAD_LEFT) ?></p>
                        <p>Generated <?= date('F d, Y \a\t h:i A', strtotime($cert['created_at'])) ?></p>
                        <p class="mt-1">Digitally generated by the SK Fund System.</p>
                    </div>
                    <div class="w-full text-center sm:max-w-xs">
                        <div class="mb-2 h-10 border-b border-slate-400"></div>
                        <p class="text-sm font-extrabold uppercase tracking-wide text-slate-900"><?= htmlspecialchars($cert['issuer_name']) ?></p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500"><?= htmlspecialchars($issuerTitle) ?></p>
                        <p class="mt-1 text-xs text-slate-400">Municipality of Ramon Magsaysay</p>
                    </div>
                </div>
            </footer>
        </article>
        <div class="h-2 bg-gradient-to-r from-cyan-500 via-blue-700 to-blue-950"></div>
        <?php endif; ?>
    </main>
</body>
</html>
