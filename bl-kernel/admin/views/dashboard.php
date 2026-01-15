<div id="dashboard" class="container">
    <div class="row">
        <div class="col-md-7">

            <!-- Good message -->
            <div>
                <h2 id="hello-message" class="pt-0">
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
                    <span class="fa fa-hand-spock-o"></span><span><?php echo $L->g('welcome') ?></span>
                </h2>
                <script>
                    $(document).ready(function() {
                        setTimeout(function() {
                            var date = new Date()
                            var hours = date.getHours()
                            if (hours >= 6 && hours < 12) {
                                $("#hello-message").html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-morning') . ', ' . $name ?>');
                            } else if (hours >= 12 && hours < 18) {
                                $("#hello-message").html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-afternoon') . ', ' . $name ?>');
                            } else if (hours >= 18 && hours < 22) {
                                $("#hello-message").html('<span class="fa fa-moon-o"></span><?php echo $L->g('good-evening') . ', ' . $name ?>');
                            } else {
                                $("#hello-message").html('<span class="fa fa-moon-o"></span><span><?php echo $L->g('good-night') . ', ' . $name ?></span>');
                            }
                        }, 2400);
                    });
                </script>
            </div>

            <!-- Quick Search Trigger -->
            <div class="quick-search-trigger mb-4" id="searchTrigger">
                <div class="quick-search-icon">
                    <span class="fa fa-search"></span>
                </div>
                <span class="quick-search-text">Quick search pages and menu</span>
                <span class="quick-search-shortcut">⌘K</span>
            </div>

            <!-- Quick Search Modal -->
            <div class="quick-search-modal" id="searchModal">
                <div class="quick-search-overlay" id="searchOverlay"></div>
                <div class="quick-search-content">
                    <div class="quick-search-header">
                        <span class="fa fa-search"></span>
                        <select id="jsclippy" class="form-control" name="state"></select>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    var clippy = $("#jsclippy").select2({
                        placeholder: "Search pages, content, and menu items...",
                        allowClear: false,
                        width: "100%",
                        theme: "bootstrap4",
                        minimumInputLength: 0,
                        dropdownParent: ".quick-search-content",
                        language: {
                            inputTooShort: function() { return ''; }
                        },
                        ajax: {
                            url: HTML_PATH_ADMIN_ROOT + "ajax/clippy",
                            data: function(params) {
                                return { query: params.term };
                            },
                            processResults: function(data) {
                                return data;
                            }
                        },
                        templateResult: function(data) {
                            var html = '';
                            if (data.type == 'menu') {
                                html += '<a href="' + data.url + '"><div class="search-suggestion">';
                                html += '<span class="fa fa-' + data.icon + '"></span>' + data.text + '</div></a>';
                            } else {
                                if (typeof data.id === 'undefined') return '';
                                html += '<div class="search-suggestion">';
                                html += '<div class="search-suggestion-item">' + data.text + ' <span class="badge badge-pill badge-light">' + data.type + '</span></div>';
                                html += '<div class="search-suggestion-options">';
                                html += '<a target="_blank" href="' + DOMAIN_PAGES + data.id + '"><?php $L->p('view') ?></a>';
                                html += '<a class="ml-2" href="' + DOMAIN_ADMIN + 'edit-content/' + data.id + '"><?php $L->p('edit') ?></a>';
                                html += '</div></div>';
                            }
                            return html;
                        },
                        escapeMarkup: function(markup) { return markup; }
                    });

                    var modal = $('#searchModal');
                    var trigger = $('#searchTrigger');
                    var overlay = $('#searchOverlay');

                    function openSearch() {
                        modal.addClass('active');
                        $('body').css('overflow', 'hidden');
                        setTimeout(function() {
                            clippy.select2('open');
                            $('.select2-search__field').focus();
                        }, 150);
                    }

                    function closeSearch() {
                        modal.removeClass('active');
                        $('body').css('overflow', '');
                        clippy.select2('close');
                    }

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
            <div class="container pb-5 px-0">
                <div class="row">

                    <!-- Content Metrics Card -->
                    <div class="col-lg-6 col-12 mb-4">
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
                    // Analytics Card - Only show if Simple Stats or Visits Stats plugin is active
                    $simpleStats = getPlugin('pluginSimpleStats');
                    $visitsStats = getPlugin('pluginVisitsStats');

                    if (($simpleStats && $simpleStats->installed()) || ($visitsStats && $visitsStats->installed())) {
                        $currentDate = Date::current('Y-m-d');
                        $visitsToday = 0;
                        $uniqueVisitors = 0;

                        if ($simpleStats && $simpleStats->installed()) {
                            $visitsToday = $simpleStats->visits($currentDate);
                            $uniqueVisitors = $simpleStats->uniqueVisitors($currentDate);
                        } elseif ($visitsStats && $visitsStats->installed()) {
                            $visitsToday = $visitsStats->visits($currentDate);
                            $uniqueVisitors = $visitsStats->uniqueVisitors($currentDate);
                        }
                    ?>
                    <!-- Analytics Metrics Card -->
                    <div class="col-lg-6 col-12 mb-4">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="metric-icon">
                                        <span class="fa fa-bar-chart"></span>
                                    </div>
                                    <h5 class="card-title mb-0 ml-3"><?php $L->p('Analytics') ?></h5>
                                </div>
                                <div class="row text-center mt-3">
                                    <div class="col-6 mb-3">
                                        <div class="metric-value"><?php echo $visitsToday; ?></div>
                                        <div class="metric-label"><?php $L->p('Visits Today') ?></div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="metric-value"><?php echo $uniqueVisitors; ?></div>
                                        <div class="metric-label"><?php $L->p('Unique Visitors') ?></div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted"><?php echo Date::format($currentDate, 'Y-m-d', 'F j, Y'); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
            </div>
        </div>
        <div class="col-md-5">

            <!-- Notifications -->
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
</div>
