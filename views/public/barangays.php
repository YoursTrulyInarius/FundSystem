<!-- ─── Barangay Carousel ─── -->
<section id="barangays" class="pt-24 pb-16 brgy-section min-h-[calc(100vh-68px)]">
    <div class="section-wrap" style="overflow: hidden;">

        <!-- Header row -->
        <div class="section-header mb-5 shrink-0 brgy-hdr opacity-0">
            <div class="section-header-row">
                <div>
                    <div class="section-pill">
                        <span class="section-pill-dot"></span>
                        All Barangays
                    </div>
                    <h2 class="section-title">Municipality of <span>Ramon Magsaysay</span></h2>
                    <p class="section-sub">Explore barangay projects, community benefits, and fund utilization. Drag or use the arrows to browse.</p>
                </div>
                <div class="brgy-carousel-controls mt-4 lg:mt-0">
                    <button id="brgyPrev" class="brgy-nav-btn" aria-label="Previous barangay" disabled>
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="brgy-dots" id="brgyDots"></div>
                    <span class="brgy-counter" id="brgyCounter">1 / <?= count($barangayCards) ?></span>
                    <button id="brgyNext" class="brgy-nav-btn" aria-label="Next barangay">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Carousel viewport -->
        <div class="brgy-carousel-viewport flex-1 min-h-0 brgy-viewport-el opacity-0" id="brgyViewport">
            <div class="brgy-carousel-track" id="brgyTrack">
                <?php foreach ($barangayCards as $bc):
                    $bname = $bc['name'];
                    $burl  = urlencode($bname);
                    $imgPath = $bc['image_path'] ?? null;
                ?>
                <div class="brgy-card" data-brgy-name="<?= htmlspecialchars($bname) ?>"
                     onclick="window.location.href='public/barangay?name=<?= $burl ?>'">

                    <?php if ($imgPath && file_exists($imgPath)): ?>
                        <img class="brgy-card-img"
                             src="<?= htmlspecialchars($imgPath) ?>"
                             alt="<?= htmlspecialchars($bc['display_name']) ?>"
                             draggable="false">
                    <?php else: ?>
                        <div class="brgy-card-img-placeholder">
                            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="1.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="brgy-placeholder-label">No photo yet</span>
                        </div>
                    <?php endif; ?>

                    <div class="brgy-card-body">
                        <p class="brgy-card-name"><?= htmlspecialchars($bc['display_name']) ?></p>
                        <p class="brgy-card-tagline"><?= htmlspecialchars($bc['tagline']) ?></p>

                        <div class="brgy-card-stats">
                            <span class="brgy-card-stat">
                                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <strong><?= (int)$bc['project_count'] ?></strong> projects
                            </span>
                            <?php if ($bc['utilization_rate'] > 0): ?>
                            <span class="brgy-card-stat">
                                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <strong><?= $bc['utilization_rate'] ?>%</strong> utilized
                            </span>
                            <?php endif; ?>
                        </div>

                        <a href="public/barangay?name=<?= $burl ?>"
                           class="brgy-card-btn"
                           onclick="event.stopPropagation()">
                            See Details
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>
