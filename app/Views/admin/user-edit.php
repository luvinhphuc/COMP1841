<section class="min-h-screen bg-[#f6f7fb] px-5 py-8 text-[#172033] sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-4xl flex-col gap-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#0b57d0]">Administration</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">Edit User</h1>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <form action="<?= BASE_URL ?>/admin/users/update/<?= (int) $user['id'] ?>" method="post"
            class="grid gap-5 rounded-xl border border-[#dfe5ef] bg-white p-6 shadow-sm sm:grid-cols-2">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <label class="grid gap-2 text-sm font-semibold">
                First name
                <input name="first_name" required maxlength="50" value="<?= htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="h-11 rounded-lg border border-[#c4c7c7] px-3 font-normal focus:border-black focus:outline-none">
            </label>
            <label class="grid gap-2 text-sm font-semibold">
                Last name
                <input name="last_name" required maxlength="50" value="<?= htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="h-11 rounded-lg border border-[#c4c7c7] px-3 font-normal focus:border-black focus:outline-none">
            </label>
            <label class="grid gap-2 text-sm font-semibold">
                Username
                <input name="username" required maxlength="75" value="<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="h-11 rounded-lg border border-[#c4c7c7] px-3 font-normal focus:border-black focus:outline-none">
            </label>
            <label class="grid gap-2 text-sm font-semibold">
                Email
                <input type="email" name="email" required maxlength="150" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="h-11 rounded-lg border border-[#c4c7c7] px-3 font-normal focus:border-black focus:outline-none">
            </label>
            <label class="grid gap-2 text-sm font-semibold sm:col-span-2">
                Role
                <select name="role" class="h-11 rounded-lg border border-[#c4c7c7] bg-white px-3 font-normal focus:border-black focus:outline-none"
                    <?= (int) $user['id'] === (int) $currentUserId ? 'disabled' : '' ?>>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>" <?= ($user['role'] ?? '') === $role ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ((int) $user['id'] === (int) $currentUserId): ?>
                    <input type="hidden" name="role" value="admin">
                    <span class="font-normal text-[#444748]">You cannot remove your own admin role.</span>
                <?php endif; ?>
            </label>

            <div class="flex gap-3 sm:col-span-2">
                <button type="submit" class="rounded-lg bg-[#0b57d0] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0847ad]">Save changes</button>
                <a href="<?= BASE_URL ?>/admin/users" class="rounded-lg border border-[#c4c7c7] bg-white px-5 py-3 text-sm font-semibold hover:border-black">Cancel</a>
            </div>
        </form>
    </div>
</section>
