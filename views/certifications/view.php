<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?= htmlspecialchars($cert['type'] === 'review' ? 'Review Completeness' : 'Official Recording') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'EB Garamond', serif; }
        .sans { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .certificate-wrapper { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-200 min-h-screen py-10 px-4">

    <!-- Print/Back Controls -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between">
        <a href="dashboard" class="sans inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Go Back
        </a>
        <button onclick="window.print()" class="sans inline-flex items-center gap-2 px-5 py-2 bg-slate-900 text-white hover:bg-slate-800 rounded-lg text-sm font-semibold shadow transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Certificate
        </button>
    </div>

    <!-- Certificate Document -->
    <div class="certificate-wrapper max-w-4xl mx-auto bg-white shadow-2xl rounded-lg overflow-hidden">

        <!-- Top Color Bar -->
        <div class="h-3 bg-gradient-to-r from-blue-900 via-blue-700 to-blue-500"></div>

        <div class="px-16 py-12">

            <!-- Header -->
            <div class="text-center mb-10">
                <div class="flex items-center justify-center gap-6 mb-6">
                    <!-- Seal placeholder -->
                    <div class="w-20 h-20 rounded-full border-4 border-blue-900 flex items-center justify-center bg-blue-50">
                        <svg class="w-10 h-10 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div class="text-left">
                        <p class="sans text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Republic of the Philippines</p>
                        <p class="text-lg font-semibold text-slate-700">Municipality of Ramon Magsaysay, Zamboanga del Sur</p>
                        <p class="sans text-sm text-slate-500">Watch SK Fund Monitoring System</p>
                    </div>
                </div>

                <div class="border-t-2 border-b-2 border-blue-900 py-4 mt-6">
                    <?php if($cert['type'] === 'review'): ?>
                        <h1 class="text-3xl font-bold text-blue-900 tracking-wide">CERTIFICATION OF REVIEW COMPLETENESS</h1>
                        <p class="sans text-sm text-slate-500 mt-1 uppercase tracking-widest">Issued by the Local Youth Development Officer (LYDO)</p>
                    <?php else: ?>
                        <h1 class="text-3xl font-bold text-blue-900 tracking-wide">CERTIFICATION OF OFFICIAL RECORDING</h1>
                        <p class="sans text-sm text-slate-500 mt-1 uppercase tracking-widest">Issued by the Sangguniang Kabataan Federation President</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Certificate Body -->
            <div class="text-lg leading-relaxed text-slate-800 space-y-5">
                <p>
                    This is to certify that the financial transaction submitted by the
                    <strong><?= htmlspecialchars($cert['sk_name']) ?></strong> of
                    <strong><?= htmlspecialchars($cert['barangay_name']) ?></strong>
                    under the Sangguniang Kabataan Fund has been duly
                    <strong><?= $cert['type'] === 'review' ? 'reviewed and found complete' : 'officially recorded in the Watch SK Fund System' ?></strong>.
                </p>

                <!-- Transaction Details Box -->
                <div class="my-8 border-2 border-blue-900 rounded-lg overflow-hidden">
                    <div class="bg-blue-900 text-white px-6 py-3">
                        <p class="sans text-sm font-bold uppercase tracking-widest">Transaction Details</p>
                    </div>
                    <div class="grid grid-cols-2 divide-x divide-slate-200">
                        <div class="px-6 py-4 space-y-3">
                            <div>
                                <p class="sans text-xs font-bold text-slate-400 uppercase tracking-wider">Project / Activity</p>
                                <p class="text-base font-semibold text-slate-800"><?= htmlspecialchars($cert['project_title']) ?></p>
                            </div>
                            <div>
                                <p class="sans text-xs font-bold text-slate-400 uppercase tracking-wider">Transaction Type</p>
                                <p class="text-base font-semibold text-slate-800 capitalize"><?= htmlspecialchars($cert['tx_type']) ?></p>
                            </div>
                            <div>
                                <p class="sans text-xs font-bold text-slate-400 uppercase tracking-wider">Reference Number</p>
                                <p class="text-base font-semibold text-slate-800"><?= htmlspecialchars($cert['reference_no']) ?></p>
                            </div>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <div>
                                <p class="sans text-xs font-bold text-slate-400 uppercase tracking-wider">Amount</p>
                                <p class="text-2xl font-bold text-blue-900">₱<?= number_format($cert['amount'], 2) ?></p>
                            </div>
                            <div>
                                <p class="sans text-xs font-bold text-slate-400 uppercase tracking-wider">Date Submitted</p>
                                <p class="text-base font-semibold text-slate-800"><?= date('F d, Y', strtotime($cert['tx_date'])) ?></p>
                            </div>
                            <div>
                                <p class="sans text-xs font-bold text-slate-400 uppercase tracking-wider">Certificate Date</p>
                                <p class="text-base font-semibold text-slate-800"><?= date('F d, Y', strtotime($cert['cert_date'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <p>
                    This certification is issued in compliance with the provisions of the 
                    <em>Watch SK Fund Monitoring Program</em> of the Municipality of Ramon Magsaysay, 
                    Zamboanga del Sur, as institutionalized under the existing local ordinance 
                    in consonance with Republic Act No. 10742.
                </p>
            </div>

            <!-- Signature Block -->
            <div class="mt-16 flex justify-end">
                <div class="text-center min-w-56">
                    <div class="border-b-2 border-slate-800 mb-2 pb-1">
                        <p class="text-xl font-bold text-slate-900 uppercase"><?= htmlspecialchars($cert['issuer_name']) ?></p>
                    </div>
                    <p class="sans text-sm text-slate-600 font-semibold uppercase tracking-wide">
                        <?= $cert['issuer_role'] === 'lydo' ? 'Local Youth Development Officer' : 'SK Federation President' ?>
                    </p>
                    <p class="sans text-xs text-slate-400 mt-0.5">Municipality of Ramon Magsaysay</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 pt-6 border-t border-slate-200 flex justify-between items-end">
                <p class="sans text-xs text-slate-400">Certificate No.: CERT-<?= str_pad($cert['id'], 6, '0', STR_PAD_LEFT) ?></p>
                <p class="sans text-xs text-slate-400">Generated: <?= date('F d, Y \a\t h:i A', strtotime($cert['created_at'])) ?></p>
            </div>

        </div>

        <!-- Bottom Color Bar -->
        <div class="h-3 bg-gradient-to-r from-blue-500 via-blue-700 to-blue-900"></div>
    </div>

</body>
</html>
