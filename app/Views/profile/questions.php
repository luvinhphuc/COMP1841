<?php
/**
 * Variables passed from ProfileController::questions()
 *
 * @var array $profileHeader
 * @var string $profileActiveTab
 * @var array $myDiscussions
 * @var array $discussionPagination
 */
?>

<section class="ui-page">

    <div class="ui-container flex flex-col gap-6">
        <?php require ROOT_PATH . '/app/Views/profile/partials/header.php'; ?>
        <?php require ROOT_PATH . '/app/Views/profile/partials/navigation.php'; ?>

        <section aria-labelledby="my-questions-heading">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-motion-intro>
                <div>
                    <h2 id="my-questions-heading" class="text-2xl font-semibold text-ui-ink">My Questions</h2>
                    <p class="mt-1 text-sm leading-6 text-ui-text">Review the questions you have posted, newest first.
                    </p>
                </div>
                <a href="<?= BASE_URL ?>/discussions/create" class="ui-button ui-button-primary w-fit">
                    Ask a question
                </a>
            </div>

            <?php if (empty($myDiscussions)): ?>
                <div class="ui-card mt-5 p-8 text-center sm:p-10" data-motion-reveal>
                    <h3 class="text-xl font-semibold text-ui-ink">You have not asked a question yet</h3>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-ui-text">
                        Start a discussion when you need help with coursework or want feedback from Coursework Forum
                        members.
                    </p>
                    <a href="<?= BASE_URL ?>/discussions/create" class="ui-button ui-button-primary mt-5">
                        Ask your first question
                    </a>
                </div>
            <?php else: ?>
                <div class="mt-5 flex flex-col gap-4" data-motion-list>
                    <?php foreach ($myDiscussions as $discussionCard): ?>
                        <?php $discussionCardAnimated = false; ?>
                        <?php require ROOT_PATH . '/app/Views/discussions/partials/post_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <?php if (($discussionPagination['total_items'] ?? 0) > 0): ?>
                    <div class="ui-card mt-5 overflow-hidden" data-motion-reveal>
                        <?php
                        $paginationConfig = [
                            'item_label' => 'questions',
                            'data' => $discussionPagination,
                        ];
                        require ROOT_PATH . '/app/Views/components/pagination.php';
                        unset($paginationConfig);
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</section>
