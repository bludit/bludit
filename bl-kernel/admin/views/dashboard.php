<div id="dashboard" class="container">

            <!-- Search with welcome message -->
            <?php
            $username = $login->username();
            $user = new User($username);
            $name = '';
            if ($user->nickname()) {
                $name = $user->nickname();
            } elseif ($user->firstName()) {
                $name = $user->firstName();
            }
            ?>
            <div class="quick-search-trigger mb-4" id="searchTrigger">
                <div class="quick-search-icon">
                    <span class="fa fa-hand-spock-o" id="hello-icon"></span>
                </div>
                <span class="quick-search-text">
                    <span id="hello-text"><?php echo $L->g('welcome') . ($name ? ', ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : '') ?></span>
                    <small class="quick-search-hint"><?php $L->p('click-here-for-quick-search') ?></small>
                </span>
                <span class="quick-search-shortcut">⌘K</span>
            </div>
            <script>
                $(document).ready(function() {
                    var date = new Date()
                    var hours = date.getHours()
                    var icon, greeting
                    var suffix = <?php echo json_encode($name ? ', ' . $name : '', JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>
                    if (hours >= 6 && hours < 12) {
                        icon = 'fa-sun-o'; greeting = <?php echo json_encode($L->g('good-morning'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?> + suffix
                    } else if (hours >= 12 && hours < 18) {
                        icon = 'fa-sun-o'; greeting = <?php echo json_encode($L->g('good-afternoon'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?> + suffix
                    } else if (hours >= 18 && hours < 22) {
                        icon = 'fa-moon-o'; greeting = <?php echo json_encode($L->g('good-evening'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?> + suffix
                    } else {
                        icon = 'fa-moon-o'; greeting = <?php echo json_encode($L->g('good-night'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?> + suffix
                    }
                    $('#hello-icon').attr('class', 'fa ' + icon)
                    $('#hello-text').text(greeting)
                });
            </script>

            <!-- Quick Search Modal -->
            <div class="quick-search-modal" id="searchModal">
                <div class="quick-search-overlay" id="searchOverlay"></div>
                <div class="quick-search-content">
                    <div class="quick-search-header">
                        <span class="fa fa-search"></span>
                        <input type="text" id="jsclippy" class="quick-search-input" placeholder="<?php $L->p('search-placeholder') ?>">
                    </div>
                    <div id="searchResults" class="quick-search-results"></div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    var searchInput = $('#jsclippy');
                    var searchResults = $('#searchResults');
                    var modal = $('#searchModal');
                    var trigger = $('#searchTrigger');
                    var overlay = $('#searchOverlay');
                    var searchTimeout;

                    function openSearch() {
                        modal.addClass('active');
                        $('body').css('overflow', 'hidden');
                        setTimeout(function() {
                            searchInput.focus();
                        }, 150);
                    }

                    function closeSearch() {
                        modal.removeClass('active');
                        $('body').css('overflow', '');
                        searchInput.val('');
                        searchResults.empty();
                    }

                    function performSearch(query) {
                        if (!query) {
                            searchResults.empty();
                            return;
                        }

                        $.ajax({
                            url: HTML_PATH_ADMIN_ROOT + "ajax/clippy",
                            data: { query: query },
                            success: function(data) {
                                searchResults.empty();

                                if (data.results && data.results.length > 0) {
                                    data.results.forEach(function(item) {
                                        var resultHtml = '';
                                        if (item.type == 'menu') {
                                            resultHtml = '<a href="' + item.url + '" class="search-suggestion">';
                                            resultHtml += '<span class="fa fa-' + item.icon + '"></span>' + item.text + '</a>';
                                        } else {
                                            resultHtml = '<div class="search-suggestion">';
                                            resultHtml += '<div class="search-suggestion-item">' + item.text + ' <span class="badge badge-pill badge-light">' + item.type + '</span></div>';
                                            resultHtml += '<div class="search-suggestion-options">';
                                            resultHtml += '<a target="_blank" href="' + DOMAIN_PAGES + item.id + '"><?php $L->p('view') ?></a>';
                                            resultHtml += '<a class="ml-2" href="' + DOMAIN_ADMIN + 'edit-content/' + item.id + '"><?php $L->p('edit') ?></a>';
                                            resultHtml += '</div></div>';
                                        }
                                        searchResults.append(resultHtml);
                                    });
                                } else {
                                    searchResults.html('<div class="search-no-results"><?php $L->p("no-results-found") ?></div>');
                                }
                            }
                        });
                    }

                    searchInput.on('input', function() {
                        clearTimeout(searchTimeout);
                        var query = $(this).val();
                        searchTimeout = setTimeout(function() {
                            performSearch(query);
                        }, 300);
                    });

                    trigger.on('click', openSearch);
                    overlay.on('click', closeSearch);

                    $(document).on('keydown', function(e) {
                        if (e.key === 'Escape' && modal.hasClass('active')) {
                            closeSearch();
                        }
                        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                            e.preventDefault();
                            openSearch();
                        }
                    });
                });
            </script>

            <!-- Dashboard Metric Cards -->
            <div class="container px-0">
                <div class="row">

                    <!-- Content Metrics Card -->
                    <div class="col-lg col-12 mb-4">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="metric-icon">
                                        <span class="fa fa-folder"></span>
                                    </div>
                                    <h5 class="card-title mb-0 ml-3"><?php $L->p('Content') ?></h5>
                                </div>
                                <div class="row text-center mt-3">
                                    <div class="col-6 mb-3">
                                        <div class="metric-value"><?php echo count($pages->getPublishedDB()); ?></div>
                                        <div class="metric-label"><?php $L->p('Published') ?></div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="metric-value"><?php echo count($pages->getDraftDB()); ?></div>
                                        <div class="metric-label"><?php $L->p('Drafts') ?></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-value"><?php echo count($pages->getScheduledDB()); ?></div>
                                        <div class="metric-label"><?php $L->p('Scheduled') ?></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-value"><?php echo count($categories->keys()); ?></div>
                                        <div class="metric-label"><?php $L->p('Categories') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php
                    // Categories Card - Only show if Categories plugin is active
                    if (pluginActivated('pluginCategories')) {
                        $categoryList = $categories->keys();
                    ?>
                    <!-- Categories Card -->
                    <div class="col-lg col-12 mb-4">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="metric-icon">
                                        <span class="fa fa-bookmark"></span>
                                    </div>
                                    <h5 class="card-title mb-0 ml-3"><?php $L->p('Categories') ?></h5>
                                </div>
                                <div class="mt-3 metric-card-list">
                                    <?php if (!empty($categoryList)): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($categoryList as $categoryKey):
                                                $category = new Category($categoryKey);
                                                $pageCount = count($category->pages());
                                            ?>
                                                <a href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-category/' . $categoryKey ?>" class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span><?php echo $category->name() ?></span>
                                                    <span class="badge badge-primary badge-pill"><?php echo $pageCount ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center py-3"><?php $L->p('No categories') ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <?php
                    // Tags Card - Only show if Tags plugin is active
                    if (pluginActivated('pluginTags')) {
                        $tagList = $tags->keys();
                    ?>
                    <!-- Tags Card -->
                    <div class="col-lg col-12 mb-4">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="metric-icon">
                                        <span class="fa fa-tag"></span>
                                    </div>
                                    <h5 class="card-title mb-0 ml-3"><?php $L->p('Tags') ?></h5>
                                </div>
                                <div class="mt-3 metric-card-list">
                                    <?php if (!empty($tagList)): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($tagList as $tagKey):
                                                $tag = new Tag($tagKey);
                                                $pageCount = count($tag->pages());
                                            ?>
                                                <a href="<?php echo HTML_PATH_ADMIN_ROOT . 'content/tag/' . $tagKey ?>" class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span><?php echo $tag->name() ?></span>
                                                    <span class="badge badge-primary badge-pill"><?php echo $pageCount ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center py-3"><?php $L->p('No tags') ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
            </div>

            <?php
            // Analytics Section - Only show if Visits Stats plugin is active
            $visitsStats = getPlugin('pluginVisitsStats');
            if ($visitsStats && $visitsStats->installed()):
                $currentDate    = Date::current('Y-m-d');
                $visitsToday    = $visitsStats->visits($currentDate);
                $uniqueVisitors = $visitsStats->uniqueVisitors($currentDate);
                $weekData       = $visitsStats->getLastDaysData(7);
            ?>
            <!-- Analytics Section -->
            <div class="analytics-section mb-4">
                <ul class="list-group list-group-striped b-0 mb-3">
                    <li class="list-group-item">
                        <h4 class="m-0"><?php $L->p('Analytics') ?></h4>
                    </li>
                </ul>
                <div class="row align-items-center">
                    <div class="col-lg-3 col-12 mb-3 mb-lg-0">
                        <div class="row text-center">
                            <div class="col-4 col-lg-12 mb-0 mb-lg-4">
                                <div class="metric-value"><?php echo $visitsToday; ?></div>
                                <div class="metric-label"><?php $L->p('Visits Today') ?></div>
                            </div>
                            <div class="col-4 col-lg-12 mb-0 mb-lg-4">
                                <div class="metric-value"><?php echo $uniqueVisitors; ?></div>
                                <div class="metric-label"><?php $L->p('Unique Visitors') ?></div>
                            </div>
                            <div class="col-4 col-lg-12">
                                <div class="metric-value"><?php echo $weekData['total']; ?></div>
                                <div class="metric-label"><?php $L->p('7-Day Total') ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-12">
                        <canvas id="analytics-chart"></canvas>
                    </div>
                </div>
            </div>
            <script>
            (function() {
                var ctx = document.getElementById('analytics-chart');
                if (!ctx || typeof Chart === 'undefined') { return; }
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($weekData['labels']); ?>,
                        datasets: [{
                            label: <?php echo json_encode($L->g('unique-visitors'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>,
                            backgroundColor: 'rgba(0,120,212,0.85)',
                            borderColor: 'rgba(0,120,212,1)',
                            borderWidth: 1,
                            data: <?php echo json_encode($weekData['unique']); ?>
                        }, {
                            label: <?php echo json_encode($L->g('visits-today'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>,
                            backgroundColor: 'rgba(255,193,3,0.85)',
                            borderColor: 'rgba(255,193,3,1)',
                            borderWidth: 1,
                            data: <?php echo json_encode($weekData['visits']); ?>
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 4,
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { fontSize: 11, boxWidth: 12, fontColor: '#475569' }
                        },
                        scales: {
                            yAxes: [{
                                ticks: { beginAtZero: true, stepSize: 1, fontColor: '#94A3B8', fontSize: 11 },
                                gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: 'rgba(0,0,0,0.1)' }
                            }],
                            xAxes: [{
                                ticks: { fontColor: '#94A3B8', fontSize: 11 },
                                gridLines: { display: false }
                            }]
                        },
                        tooltips: { mode: 'index', intersect: false }
                    }
                });
            })();
            </script>
            <?php endif; ?>

            <!-- Notifications -->
            <div class="mt-4">
                <ul class="list-group list-group-striped b-0">
                    <li class="list-group-item">
                        <h4 class="m-0"><?php $L->p('Notifications') ?></h4>
                    </li>
                    <?php
                    $logs = array_slice($syslog->db, 0, NOTIFICATIONS_AMOUNT);
                    foreach ($logs as $log) {
                        $phrase = $L->g($log['dictionaryKey']);
                        echo '<li class="list-group-item">';
                        echo $phrase;
                        if (!empty($log['notes'])) {
                            echo ' « <b>' . $log['notes'] . '</b> »';
                        }
                        echo '<br><span class="notification-date"><small>';
                        echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
                        echo ' [ ' . $log['username'] . ' ]';
                        echo '</small></span>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>

</div>
