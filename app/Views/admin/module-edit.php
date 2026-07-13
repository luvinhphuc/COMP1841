<section class="min-h-screen bg-[#f6f7fb] px-5 py-8 text-[#172033] sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-4xl flex-col gap-8">
        <div><p class="text-xs font-bold uppercase tracking-[0.14em] text-[#0b57d0]">Administration</p><h1 class="mt-2 text-3xl font-bold tracking-tight">Edit Module</h1></div>
        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <form action="<?= BASE_URL ?>/admin/modules/update/<?= (int) $module['id'] ?>" method="post"
            class="grid gap-5 rounded-xl border border-[#dfe5ef] bg-white p-6 shadow-sm">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <label class="grid gap-2 text-sm font-semibold">Code
                <input name="code" required maxlength="20" value="<?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="h-11 rounded-lg border border-[#c4c7c7] px-3 font-normal focus:border-black focus:outline-none">
            </label>
            <label class="grid gap-2 text-sm font-semibold">Name
                <input name="name" required maxlength="150" value="<?= htmlspecialchars($module['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="h-11 rounded-lg border border-[#c4c7c7] px-3 font-normal focus:border-black focus:outline-none">
            </label>
            <label class="grid gap-2 text-sm font-semibold">Description
                <textarea name="description" rows="5" class="rounded-lg border border-[#c4c7c7] px-3 py-2 font-normal focus:border-black focus:outline-none"><?= htmlspecialchars($module['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </label>
            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-[#0b57d0] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0847ad]">Save changes</button>
                <a href="<?= BASE_URL ?>/admin/modules" class="rounded-lg border border-[#c4c7c7] bg-white px-5 py-3 text-sm font-semibold hover:border-black">Cancel</a>
            </div>
        </form>
    </div>
</section>
