<?php
/**
 * Variables passed from ProfileController::index()
 *
 * @var array $profileHeader
 * @var string $profileActiveTab
 * @var array $statistics
 */
$memberSinceTimestamp = strtotime((string)($statistics['member_since'] ?? ''));
$memberSince = $memberSinceTimestamp !== false ? date('F j, Y', $memberSinceTimestamp) : 'Unknown';
$summaryStatistics = [
    ['label' => 'Questions asked', 'value' => (int)($statistics['questions_asked'] ?? 0)],
    ['label' => 'Replies posted', 'value' => (int)($statistics['replies_posted'] ?? 0)],
    ['label' => 'Solved questions', 'value' => (int)($statistics['solved_questions'] ?? 0)],
    ['label' => 'Total post views', 'value' => (int)($statistics['total_post_views'] ?? 0)],
];
?>

<section class="ui-page"
    style="background: url('<?= BASE_URL ?>/assets/svg/logo-inset.svg') no-repeat calc(100% + 250px) calc(100% + 150px) / 800px auto;">
    <div class="ui-container flex flex-col gap-6">
        <?php require ROOT_PATH . '/app/Views/profile/partials/header.php'; ?>
        <?php require ROOT_PATH . '/app/Views/profile/partials/navigation.php'; ?>

        <section aria-labelledby="profile-summary-heading">
            <div data-motion-intro>
                <h2 id="profile-summary-heading" class="text-2xl font-semibold text-ui-ink">Summary</h2>
                <p class="mt-1 text-sm leading-6 text-ui-text">An overview of your activity in Coursework Forum.
                </p>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-motion-list>
                <?php foreach ($summaryStatistics as $summaryStatistic): ?>
                <article class="ui-card p-5 sm:p-6" data-motion-item>
                    <p class="text-sm font-semibold text-ui-text">
                        <?= htmlspecialchars($summaryStatistic['label'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="mt-3 text-3xl font-semibold text-ui-ink">
                        <?= number_format($summaryStatistic['value']) ?>
                    </p>
                </article>
                <?php endforeach; ?>

                <article class="ui-card p-5 sm:p-6" data-motion-item>
                    <p class="text-sm font-semibold text-ui-text">Member since</p>
                    <p class="mt-3 text-xl font-semibold text-ui-ink">
                        <time
                            datetime="<?= htmlspecialchars($statistics['member_since'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($memberSince, ENT_QUOTES, 'UTF-8') ?>
                        </time>
                    </p>
                </article>
            </div>
        </section>
    </div>
</section>
