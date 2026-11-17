<?php
// ============================================================================
// Data for the dashboard, everything comes from the databases already loaded in memory
// ============================================================================

$username = $login->username();
$user = new User($username);
$name = '';
if ($user->nickname()) {
    $name = $user->nickname();
} elseif ($user->firstName()) {
    $name = $user->firstName();
}

// Escape a name stored encoded or not encoded, the old data was stored without encoding
function dashboardEscape($text) {
    return htmlspecialchars(Sanitize::htmlDecode($text), ENT_QUOTES, 'UTF-8');
}

// The authors only see their own content, the same as Manage > Content
$isAuthor = checkRole(array('author'), false);
$isAdmin = checkRole(array('admin'), false);

$countPublished = 0;
$countDrafts = 0;
$countScheduled = 0;
$recentContent = array();
foreach ($pages->db as $pageKey => $fields) {
    if ($fields['type'] === 'autosave') {
        continue;
    }
    if ($isAuthor && $fields['username'] !== $username) {
        continue;
    }
    if ($fields['type'] === 'published') {
        $countPublished++;
    } elseif ($fields['type'] === 'draft') {
        $countDrafts++;
    } elseif ($fields['type'] === 'scheduled') {
        $countScheduled++;
    }
    $recentContent[$pageKey] = empty($fields['dateModified']) ? $fields['date'] : $fields['dateModified'];
}
// Most recently modified first, the dates use DB_DATE_FORMAT so they sort as text
arsort($recentContent);
$recentContent = array_slice($recentContent, 0, 8, true);

// Seconds since a date of the database, the browser shows it as "2 hours ago"
$now = Date::current(DB_DATE_FORMAT);
function dashboardSecondsAgo($date, $now) {
    $seconds = strtotime($now) - strtotime($date);
    return $seconds > 0 ? $seconds : 0;
}

// Label and color of each type of content
$typeBadges = array(
    'published' => array('label' => $L->g('Published'), 'class' => 'status-published'),
    'sticky'    => array('label' => $L->g('Sticky'),    'class' => 'status-published'),
    'static'    => array('label' => $L->g('Static'),    'class' => 'status-static'),
    'draft'     => array('label' => $L->g('Draft'),     'class' => 'status-draft'),
    'scheduled' => array('label' => $L->g('Scheduled'), 'class' => 'status-scheduled')
);

// Analytics, only when the Visits Stats plugin is installed
$visitsStats = getPlugin('pluginVisitsStats');
$showVisits = ($visitsStats && $visitsStats->installed());
if ($showVisits) {
    $currentDate    = Date::current('Y-m-d');
    $visitsToday    = $visitsStats->visits($currentDate);
    $uniqueVisitors = $visitsStats->uniqueVisitors($currentDate);
    $weekData       = $visitsStats->getLastDaysData(7);
    $weekMax        = max(1, max($weekData['visits']));
}

// Categories and tags, sorted by the amount of content
$showCategories = pluginActivated('pluginCategories');
$showTags = pluginActivated('pluginTags');
$categoryCounts = array();
if ($showCategories) {
    foreach ($categories->db as $key => $fields) {
        $categoryCounts[$key] = count($fields['list']);
    }
    arsort($categoryCounts);
}
$tagCounts = array();
if ($showTags) {
    foreach ($tags->db as $key => $fields) {
        $tagCounts[$key] = count($fields['list']);
    }
    arsort($tagCounts);
}
$maxCategories = 8;
$maxTags = 12;

// Icon of each syslog entry
function dashboardActivityIcon($dictionaryKey) {
    $icons = array(
        'deleted'  => 'trash',
        'access'   => 'lock',
        'content'  => 'file-text-o',
        'scheduler'=> 'clock-o',
        'plugin'   => 'puzzle-piece',
        'theme'    => 'paint-brush',
        'user'     => 'user',
        'category' => 'bookmark',
        'settings' => 'gear',
        'backup'   => 'history',
        'updated'  => 'refresh'
    );
    foreach ($icons as $word => $icon) {
        if (strpos($dictionaryKey, $word) !== false) {
            return $icon;
        }
    }
    return 'circle';
}
?>
<div id="dashboard" class="container">

    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-greeting"><span class="fa fa-hand-spock-o" id="hello-icon"></span><span id="hello-text"><?php echo $L->g('welcome') . ($name ? ', ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : '') ?></span></h1>
        </div>
        <a class="btn btn-primary dashboard-new-content" href="<?php echo HTML_PATH_ADMIN_ROOT . 'new-content' ?>"><span class="fa fa-plus"></span><?php $L->p('New content') ?></a>
    </div>
    <script>
        $(document).ready(function() {
            var hours = new Date().getHours()
            var icon, greeting
            var suffix = <?php echo json_encode($name ? ', ' . $name : '', JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>;
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

    <?php include(PATH_ADMIN_THEMES . $site->adminTheme() . DS . 'html' . DS . 'quick-search.php'); ?>

    <div class="row g-3 mb-4">

        <!-- Continue writing -->
        <div class="<?php echo ($showVisits || $showCategories || $showTags) ? 'col-lg-8' : 'col-12' ?>">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2><?php $L->p('Continue writing') ?></h2>
                    <div class="dashboard-counts">
                        <a class="status-published" href="<?php echo HTML_PATH_ADMIN_ROOT . 'content' ?>"><span class="dashboard-dot"></span><b><?php echo $countPublished ?></b> <?php $L->p('Published') ?></a>
                        <a class="status-draft" href="<?php echo HTML_PATH_ADMIN_ROOT . 'content#draft' ?>"><span class="dashboard-dot"></span><b><?php echo $countDrafts ?></b> <?php $L->p('Drafts') ?></a>
                        <a class="status-scheduled" href="<?php echo HTML_PATH_ADMIN_ROOT . 'content#scheduled' ?>"><span class="dashboard-dot"></span><b><?php echo $countScheduled ?></b> <?php $L->p('Scheduled') ?></a>
                    </div>
                </div>
                <?php if (empty($recentContent)): ?>
                <div class="dashboard-empty">
                    <p><?php $L->p('no-content-yet') ?></p>
                    <a class="btn btn-primary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'new-content' ?>"><span class="fa fa-plus"></span><?php $L->p('New content') ?></a>
                </div>
                <?php else: ?>
                <ul class="dashboard-list">
                    <?php foreach ($recentContent as $pageKey => $modified):
                        $fields = $pages->db[$pageKey];
                        $badge = isset($typeBadges[$fields['type']]) ? $typeBadges[$fields['type']] : array('label' => $fields['type'], 'class' => '');
                        $title = empty($fields['title']) ? $pageKey : $fields['title'];
                        $isPublic = in_array($fields['type'], array('published', 'sticky', 'static'));
                    ?>
                    <li>
                        <span class="status-badge <?php echo $badge['class'] ?>"><?php echo htmlspecialchars($badge['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        <a class="dashboard-list-title" href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-content/' . $pageKey ?>"><?php echo dashboardEscape($title) ?></a>
                        <span class="dashboard-list-date js-time-ago" data-seconds="<?php echo dashboardSecondsAgo($modified, $now) ?>" title="<?php echo Date::format($modified, DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT) ?>"><?php echo Date::format($modified, DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT) ?></span>
                        <span class="dashboard-list-actions">
                            <?php if ($isPublic): ?>
                            <a target="_blank" href="<?php echo DOMAIN_PAGES . $pageKey ?>"><?php $L->p('View') ?></a>
                            <?php endif; ?>
                            <a href="<?php echo HTML_PATH_ADMIN_ROOT . 'edit-content/' . $pageKey ?>"><?php $L->p('Edit') ?></a>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($showVisits || $showCategories || $showTags): ?>
        <div class="col-lg-4 d-flex flex-column gap-3">

            <?php if ($showVisits): ?>
            <!-- Visits, from the Visits Stats plugin -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2><?php $L->p('Visits last 7 days') ?></h2>
                </div>
                <div class="dashboard-visits status-visits">
                    <div>
                        <span class="dashboard-visits-value"><?php echo $weekData['total'] ?></span>
                        <span class="dashboard-visits-sub"><?php echo $L->g('Today') . ': ' . $visitsToday . ' · ' . $L->g('Unique Visitors') . ': ' . $uniqueVisitors ?></span>
                    </div>
                    <div class="visits-bars" aria-hidden="true">
                        <?php foreach ($weekData['visits'] as $i => $visits): ?>
                        <span class="visits-bar<?php echo ($i === count($weekData['visits']) - 1) ? ' today' : '' ?>" style="height: <?php echo max(6, round($visits * 100 / $weekMax)) ?>%" title="<?php echo htmlspecialchars($weekData['labels'][$i], ENT_QUOTES, 'UTF-8') . ': ' . $visits ?>"></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($showCategories): ?>
            <!-- Categories -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2><?php $L->p('Categories') ?></h2>
                    <?php if ($isAdmin): ?>
                    <a href="<?php echo HTML_PATH_ADMIN_ROOT . 'categories' ?>"><?php $L->p('Manage') ?></a>
                    <?php endif; ?>
                </div>
                <?php if (empty($categoryCounts)): ?>
                <div class="dashboard-empty"><p><?php $L->p('No categories') ?></p></div>
                <?php else: ?>
                <ul class="dashboard-list dashboard-list-compact">
                    <?php foreach (array_slice($categoryCounts, 0, $maxCategories, true) as $categoryKey => $pageCount):
                        $category = new Category($categoryKey);
                    ?>
                    <li>
                        <a class="dashboard-list-title" href="<?php echo $isAdmin ? HTML_PATH_ADMIN_ROOT . 'edit-category/' . $categoryKey : $category->permalink() ?>"<?php echo $isAdmin ? '' : ' target="_blank"' ?>><?php echo dashboardEscape($category->name()) ?></a>
                        <span class="dashboard-count"><?php echo $pageCount ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (count($categoryCounts) > $maxCategories): ?>
                <div class="dashboard-card-more">+<?php echo count($categoryCounts) - $maxCategories ?></div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($showTags): ?>
            <!-- Tags -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2><?php $L->p('Tags') ?></h2>
                </div>
                <?php if (empty($tagCounts)): ?>
                <div class="dashboard-empty"><p><?php $L->p('No tags') ?></p></div>
                <?php else: ?>
                <div class="dashboard-tags">
                    <?php foreach (array_slice($tagCounts, 0, $maxTags, true) as $tagKey => $pageCount):
                        $tag = new Tag($tagKey);
                    ?>
                    <a class="dashboard-tag" target="_blank" href="<?php echo $tag->permalink() ?>"><?php echo dashboardEscape($tag->name()) ?><span><?php echo $pageCount ?></span></a>
                    <?php endforeach; ?>
                    <?php if (count($tagCounts) > $maxTags): ?>
                    <span class="dashboard-tag dashboard-tag-more">+<?php echo count($tagCounts) - $maxTags ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    </div>

    <!-- Recent activity -->
    <div class="dashboard-card mb-4">
        <div class="dashboard-card-header">
            <h2><?php $L->p('Recent activity') ?></h2>
        </div>
        <?php
        $logs = array_slice($syslog->db, 0, 5);
        if (empty($logs)):
        ?>
        <div class="dashboard-empty"><p><?php $L->p('No activity yet') ?></p></div>
        <?php else: ?>
        <ul class="dashboard-list dashboard-activity">
            <?php foreach ($logs as $log): ?>
            <li>
                <span class="activity-icon"><span class="fa fa-<?php echo dashboardActivityIcon($log['dictionaryKey']) ?>"></span></span>
                <span class="activity-text">
                    <?php echo $L->g($log['dictionaryKey']) ?>
                    <?php if (!empty($log['notes'])): ?>
                    <b><?php echo dashboardEscape($log['notes']) ?></b>
                    <?php endif; ?>
                </span>
                <span class="dashboard-list-date">
                    <?php echo htmlspecialchars($log['username'], ENT_QUOTES, 'UTF-8') ?> ·
                    <span class="js-time-ago" data-seconds="<?php echo dashboardSecondsAgo($log['date'], $now) ?>" title="<?php echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT) ?>"><?php echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT) ?></span>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <script>
        // Show the dates as "2 hours ago" in the language of the site, the absolute date stays in the tooltip
        $(document).ready(function() {
            if (typeof Intl === 'undefined' || !Intl.RelativeTimeFormat) {
                return;
            }
            var rtf = new Intl.RelativeTimeFormat(<?php echo json_encode($L->currentLanguageShortVersion()) ?>, { numeric: 'auto' });
            var units = [['year', 31536000], ['month', 2592000], ['week', 604800], ['day', 86400], ['hour', 3600], ['minute', 60]];
            $('.js-time-ago').each(function() {
                var seconds = parseInt($(this).data('seconds'), 10) || 0;
                var text = rtf.format(0, 'second');
                for (var i = 0; i < units.length; i++) {
                    if (seconds >= units[i][1]) {
                        text = rtf.format(-Math.floor(seconds / units[i][1]), units[i][0]);
                        break;
                    }
                }
                $(this).text(text);
            });
        });
    </script>

</div>
