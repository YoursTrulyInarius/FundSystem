<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch SK Fund | Public Transparency Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Anime.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; margin: 0; overflow: hidden; }
        .card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        
        /* Hide scrollbar for the main container but allow scrolling */
        .snap-container::-webkit-scrollbar {
            width: 8px;
        }
        .snap-container::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .snap-container::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        .snap-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }

        /* Inner scrollbars for sections */
        .inner-scroll::-webkit-scrollbar { width: 6px; }
        .inner-scroll::-webkit-scrollbar-track { background: transparent; }
        .inner-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 3px; }
    </style>
</head>
<body class="text-slate-800">

    <!-- Navbar (Fixed) -->
    <nav class="fixed top-0 w-full bg-white/90 backdrop-blur-md border-b border-slate-200 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="nav-brand text-xl font-bold text-slate-900 tracking-tight opacity-0">Watch SK Fund</h1>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="#hero" class="nav-link text-sm font-medium text-slate-600 hover:text-slate-900 transition opacity-0">Home</a>
                    <a href="#projects" class="nav-link text-sm font-medium text-slate-600 hover:text-slate-900 transition opacity-0">Active Projects</a>
                    <a href="#registry" class="nav-link text-sm font-medium text-slate-600 hover:text-slate-900 transition opacity-0">Official Registry</a>
                    <a href="login" class="nav-btn inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-slate-900 hover:bg-slate-800 focus:outline-none transition opacity-0 transform translate-y-2">
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Snap Scrolling Container -->
    <main class="snap-container h-screen w-full overflow-y-scroll snap-y snap-mandatory scroll-smooth">
        
        <!-- Hero Section -->
        <section id="hero" class="snap-start h-screen w-full flex flex-col justify-center items-center relative overflow-hidden pt-16">
            <!-- New Background Image -->
            <div class="absolute inset-0 bg-cover bg-center z-0" style="background-image: url('assets/img/bg.png');"></div>
            <!-- Dark Overlay for Readability -->
            <div class="absolute inset-0 bg-slate-900/85 z-0"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <h2 class="hero-text text-4xl md:text-5xl lg:text-6xl tracking-tight font-extrabold text-white opacity-0 transform translate-y-8">
                    Public Transparency Portal
                </h2>
                <p class="hero-subtext mt-6 max-w-2xl text-lg md:text-xl text-slate-300 mx-auto opacity-0 transform translate-y-8">
                    Monitoring youth development programs and ensuring transparency, accountability, and fiscal responsibility in the management of SK Funds.
                </p>
                <div class="mt-10 flex justify-center gap-4 hero-buttons opacity-0">
                    <a href="#projects" class="px-8 py-3 rounded-full bg-white text-slate-900 font-bold hover:bg-slate-100 transition shadow-lg">View Projects</a>
                    <a href="#registry" class="px-8 py-3 rounded-full bg-slate-800 text-white font-bold hover:bg-slate-700 transition border border-slate-700">See Registry</a>
                </div>
            </div>
        </section>

        <!-- Active Projects -->
        <section id="projects" class="snap-start h-screen w-full bg-[#f8fafc] pt-24 pb-10">
            <div class="flex flex-col h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 shrink-0 section-header opacity-0 transform -translate-x-8">
                    <div>
                        <h3 class="text-3xl font-bold text-slate-900">Active SK Projects</h3>
                        <p class="text-slate-500 mt-1">Ongoing and completed youth programs in the municipality.</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <select id="projectBarangayFilter" class="pl-3 pr-8 py-2 text-sm border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent appearance-none cursor-pointer transition-all">
                            <option value="">All Barangays</option>
                            <option value="Bagong Opon">Bagong Opon</option>
                            <option value="Bambong Daku">Bambong Daku</option>
                            <option value="Bambong Diut">Bambong Diut</option>
                            <option value="Bobongan">Bobongan</option>
                            <option value="Campo IV">Campo IV</option>
                            <option value="Campo V">Campo V</option>
                            <option value="Caniangan">Caniangan</option>
                            <option value="Dipalusan">Dipalusan</option>
                            <option value="Eastern Bobongan">Eastern Bobongan</option>
                            <option value="Esperanza">Esperanza</option>
                            <option value="Gapasan">Gapasan</option>
                            <option value="Katipunan">Katipunan</option>
                            <option value="Kauswagan">Kauswagan</option>
                            <option value="Lower Sambulawan">Lower Sambulawan</option>
                            <option value="Mabini">Mabini</option>
                            <option value="Magsaysay">Magsaysay</option>
                            <option value="Malating">Malating</option>
                            <option value="Paradise">Paradise</option>
                            <option value="Pasingkalan">Pasingkalan</option>
                            <option value="Poblacion">Poblacion</option>
                            <option value="San Fernando">San Fernando</option>
                            <option value="Santo Rosario">Santo Rosario</option>
                            <option value="Sapa Anding">Sapa Anding</option>
                            <option value="Sinaguing">Sinaguing</option>
                            <option value="Switch">Switch</option>
                            <option value="Upper Laperian">Upper Laperian</option>
                            <option value="Wakat">Wakat</option>
                        </select>
                    </div>
                </div>
                
                <div class="inner-scroll flex-1 overflow-y-auto pb-8 pr-2">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if(empty($projects)): ?>
                        <p class="text-slate-500 col-span-full">No active projects found.</p>
                    <?php else: ?>
                        <?php foreach($projects as $index => $proj): ?>
                            <div class="card p-6 hover:shadow-md transition-shadow project-card opacity-0 transform translate-y-4" data-barangay="<?= htmlspecialchars($proj['barangay_name'] ?? '') ?>">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $proj['status'] == 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' ?>">
                                        <?= htmlspecialchars($proj['status']) ?>
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($proj['barangay_name']) ?></span>
                                </div>
                                <h4 class="text-lg font-bold text-slate-900 mb-2 leading-tight"><?= htmlspecialchars($proj['title']) ?></h4>
                                <p class="text-sm text-slate-600 mb-4 line-clamp-2"><?= htmlspecialchars($proj['description']) ?></p>
                                <div class="pt-4 border-t border-slate-100">
                                    <span class="text-sm text-slate-500">Approved Budget:</span>
                                    <span class="text-sm font-bold text-slate-800 ml-1">₱<?= number_format($proj['budget'], 2) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div id="noProjectsRow" style="display: none;" class="mt-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <p class="text-slate-500 font-medium">No projects found for this barangay.</p>
                </div>
            </div>
        </section>

        <!-- Official Registry & Footer -->
        <section id="registry" class="snap-start h-screen w-full bg-[#f8fafc] pt-24 pb-10">
            <div class="flex flex-col h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="mb-6 shrink-0 section-header-2 opacity-0 transform -translate-x-8">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <h3 class="text-3xl font-bold text-slate-900">Watch SK Fund Registry</h3>
                            <p class="text-slate-500 mt-1">All SK financial transactions across all barangays, updated in real-time.</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" id="txSearch" placeholder="Search..." class="pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent w-48 transition-all">
                            </div>
                            <select id="barangayFilter" class="pl-3 pr-8 py-2 text-sm border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent appearance-none cursor-pointer transition-all">
                                <option value="">All Barangays</option>
                                <option value="Bagong Opon">Bagong Opon</option>
                                <option value="Bambong Daku">Bambong Daku</option>
                                <option value="Bambong Diut">Bambong Diut</option>
                                <option value="Bobongan">Bobongan</option>
                                <option value="Campo IV">Campo IV</option>
                                <option value="Campo V">Campo V</option>
                                <option value="Caniangan">Caniangan</option>
                                <option value="Dipalusan">Dipalusan</option>
                                <option value="Eastern Bobongan">Eastern Bobongan</option>
                                <option value="Esperanza">Esperanza</option>
                                <option value="Gapasan">Gapasan</option>
                                <option value="Katipunan">Katipunan</option>
                                <option value="Kauswagan">Kauswagan</option>
                                <option value="Lower Sambulawan">Lower Sambulawan</option>
                                <option value="Mabini">Mabini</option>
                                <option value="Magsaysay">Magsaysay</option>
                                <option value="Malating">Malating</option>
                                <option value="Paradise">Paradise</option>
                                <option value="Pasingkalan">Pasingkalan</option>
                                <option value="Poblacion">Poblacion</option>
                                <option value="San Fernando">San Fernando</option>
                                <option value="Santo Rosario">Santo Rosario</option>
                                <option value="Sapa Anding">Sapa Anding</option>
                                <option value="Sinaguing">Sinaguing</option>
                                <option value="Switch">Switch</option>
                                <option value="Upper Laperian">Upper Laperian</option>
                                <option value="Wakat">Wakat</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="inner-scroll flex-1 overflow-y-auto pr-2">
                <div class="card overflow-hidden registry-table opacity-0 transform translate-y-4">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 sticky top-0">
                            <tr>
                                <th class="px-6 py-4 font-medium">Barangay</th>
                                <th class="px-6 py-4 font-medium">Project / Transaction</th>
                                <th class="px-6 py-4 font-medium">Amount</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium">Date Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(empty($transactions)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        No transactions available yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($transactions as $tx): ?>
                                    <tr class="hover:bg-slate-50 transition-colors registry-row" data-barangay="<?= htmlspecialchars($tx['barangay_name']) ?>">
                                        <td class="px-6 py-4 font-medium text-slate-900"><?= htmlspecialchars($tx['barangay_name']) ?></td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-slate-800"><?= htmlspecialchars($tx['project_title']) ?></div>
                                            <div class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($tx['type']) ?> &nbsp;·&nbsp; Ref: <?= htmlspecialchars($tx['reference_no']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-slate-700">₱<?= number_format($tx['amount'], 2) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold capitalize
                                                <?= $tx['status'] === 'recorded'  ? 'bg-emerald-100 text-emerald-800' 
                                                  : ($tx['status'] === 'reviewed'  ? 'bg-blue-100 text-blue-800'  
                                                  : ($tx['status'] === 'returned'  ? 'bg-red-100 text-red-800'   
                                                  :                                  'bg-amber-100 text-amber-800')) ?>">
                                                <span class="w-1.5 h-1.5 rounded-full
                                                    <?= $tx['status'] === 'recorded' ? 'bg-emerald-500' 
                                                      : ($tx['status'] === 'reviewed' ? 'bg-blue-500' 
                                                      : ($tx['status'] === 'returned' ? 'bg-red-500' 
                                                      : 'bg-amber-500')) ?>"></span>
                                                <?= htmlspecialchars($tx['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500"><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="noResultsRow" style="display:none;">
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        <p class="font-medium text-slate-500">No transactions found for this barangay.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

                <footer class="mt-auto py-6 shrink-0 border-t border-slate-200 text-center opacity-0 registry-footer">
                    <p class="text-sm text-slate-500">&copy; 2026 Municipality of Ramon Magsaysay. Watch SK Fund System.</p>
                </footer>
            </div>
        </section>

    </main>

    <script>
        // --- Registry Filter Logic ---
        function filterRegistry() {
            const searchVal = document.getElementById('txSearch').value.toLowerCase();
            const barangayVal = document.getElementById('barangayFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.registry-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const brgy = row.getAttribute('data-barangay').toLowerCase();
                const text = row.innerText.toLowerCase();
                const matchesBarangay = !barangayVal || brgy.includes(barangayVal);
                const matchesSearch = !searchVal || text.includes(searchVal);
                
                if (matchesBarangay && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no-results message
            const noResults = document.getElementById('noResultsRow');
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        document.getElementById('barangayFilter').addEventListener('change', filterRegistry);
        document.getElementById('txSearch').addEventListener('input', filterRegistry);

        // --- Projects Filter Logic ---
        function filterProjects() {
            const barangayVal = document.getElementById('projectBarangayFilter').value.toLowerCase();
            const cards = document.querySelectorAll('.project-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const brgy = (card.getAttribute('data-barangay') || '').toLowerCase();
                if (!barangayVal || brgy.includes(barangayVal)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const noProjects = document.getElementById('noProjectsRow');
            if (noProjects) {
                noProjects.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        const projFilter = document.getElementById('projectBarangayFilter');
        if (projFilter) {
            projFilter.addEventListener('change', filterProjects);
        }

        // --- Navbar & Animation logic ---
        document.addEventListener('DOMContentLoaded', () => {
            // Navbar Animation
            anime.timeline({
                easing: 'easeOutQuart'
            })
            .add({
                targets: '.nav-brand',
                opacity: [0, 1],
                translateX: [-20, 0],
                duration: 800
            })
            .add({
                targets: '.nav-link',
                opacity: [0, 1],
                translateY: [-10, 0],
                delay: anime.stagger(100),
                duration: 600
            }, '-=600')
            .add({
                targets: '.nav-btn',
                opacity: [0, 1],
                translateY: [10, 0],
                duration: 600
            }, '-=400');

            // Hero Animation
            anime.timeline({
                easing: 'easeOutQuart'
            })
            .add({
                targets: '.hero-text',
                opacity: [0, 1],
                translateY: [40, 0],
                duration: 800,
                delay: 200
            })
            .add({
                targets: '.hero-subtext',
                opacity: [0, 1],
                translateY: [30, 0],
                duration: 800
            }, '-=600')
            .add({
                targets: '.hero-buttons',
                opacity: [0, 1],
                scale: [0.95, 1],
                duration: 800
            }, '-=600');

            // Intersection Observer to trigger animations on scroll snap
            const observerOptions = {
                root: document.querySelector('.snap-container'),
                threshold: 0.3
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        
                        // Projects Section Animations
                        if (entry.target.id === 'projects') {
                            anime({
                                targets: '.section-header',
                                opacity: [0, 1],
                                translateX: [-40, 0],
                                duration: 800,
                                easing: 'easeOutQuart'
                            });
                            
                            anime({
                                targets: '.project-card',
                                opacity: [0, 1],
                                translateY: [30, 0],
                                delay: anime.stagger(100),
                                duration: 800,
                                easing: 'easeOutQuart'
                            });
                        }
                        
                        // Registry Section Animations
                        if (entry.target.id === 'registry') {
                            anime({
                                targets: '.section-header-2',
                                opacity: [0, 1],
                                translateX: [-40, 0],
                                duration: 800,
                                easing: 'easeOutQuart'
                            });
                            
                            anime({
                                targets: '.registry-table',
                                opacity: [0, 1],
                                translateY: [30, 0],
                                duration: 800,
                                easing: 'easeOutQuart',
                                delay: 150
                            });

                            anime({
                                targets: '.registry-footer',
                                opacity: [0, 1],
                                duration: 1000,
                                delay: 600,
                                easing: 'linear'
                            });
                        }
                    }
                });
            }, observerOptions);

            // Observe the sections
            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });
        });
    </script>
</body>
</html>
