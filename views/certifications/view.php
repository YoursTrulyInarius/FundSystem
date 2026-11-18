<?php
$isLydoApproval = $cert['type'] === 'review';
$isProceedAuthorization = in_array($cert['tx_type'], ['disbursement', 'procurement'], true);
$certificateTitle = $isLydoApproval ? 'Certificate of LYDO Approval' : 'Certificate of Official Recording';
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
        @page { size: A4 portrait; margin: 12mm; }
        @media print {
            body { background: #fff !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .certificate-shell { max-width: none !important; margin: 0 !important; box-shadow: none !important; }
            .certificate-paper { min-height: 270mm !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 px-4 py-8 sm:py-12">
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

    <main class="certificate-shell mx-auto max-w-4xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
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
    </main>
</body>
</html>
