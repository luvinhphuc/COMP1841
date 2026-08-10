<?php
/**
 * Variables passed from HomeController::privacy()
 *
 * @var string $privacyLastUpdated
 * @var string $privacyLastUpdatedIso
 */

$privacySections = [
    ['id' => 'about', 'label' => 'About this notice'],
    ['id' => 'information', 'label' => 'Information we handle'],
    ['id' => 'public-content', 'label' => 'What becomes public'],
    ['id' => 'purposes', 'label' => 'Why we use information'],
    ['id' => 'sharing', 'label' => 'Sharing and providers'],
    ['id' => 'cookies', 'label' => 'Cookies and sessions'],
    ['id' => 'retention', 'label' => 'Retention and deletion'],
    ['id' => 'security', 'label' => 'Security'],
    ['id' => 'rights', 'label' => 'Your rights'],
    ['id' => 'contact', 'label' => 'Contact and complaints'],
];
?>

<section class="ui-page">
    <div class="mx-auto w-full max-w-[1280px] px-5 py-10 sm:px-8 sm:py-12 lg:px-16 lg:py-16">
        <header class="max-w-4xl" data-motion-intro>
            <p class="ui-eyebrow">Legal &amp; privacy</p>
            <h1 class="mt-3 font-serif text-[clamp(2.75rem,7vw,5.5rem)] leading-[1.02] tracking-[-0.035em] text-ui-ink">
                Privacy Policy
            </h1>
            <p class="mt-6 max-w-3xl text-base leading-7 text-ui-text sm:text-lg sm:leading-8">
                This notice explains what personal information the Coursework Forum platform handles, why it is
                used, when it may be shared, and the choices available to you.
            </p>
            <p class="mt-4 text-sm font-semibold text-ui-muted">
                Last updated:
                <time datetime="<?= htmlspecialchars($privacyLastUpdatedIso, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($privacyLastUpdated, ENT_QUOTES, 'UTF-8') ?>
                </time>
            </p>
        </header>

        <div class="mt-8 max-w-4xl rounded-xl border border-ui-border bg-ui-brand-soft p-5 sm:p-6"
            data-motion-reveal>
            <div class="flex items-start gap-4">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white text-brand-royal"
                    aria-hidden="true">
                    <span class="font-serif text-lg font-semibold">i</span>
                </span>
                <div>
                    <h2 class="text-base font-semibold text-ui-ink">Coursework project notice</h2>
                    <p class="mt-1 text-sm leading-6 text-ui-text">
                        This platform is a student coursework project. It is not the University of Greenwich's official
                        website or the University's privacy notice. The administrator operating this installation is the
                        controller and contact point for information handled through this platform. Use the contact form
                        to request the operator's current legal name, postal contact, and service-provider details.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12 grid gap-10 lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-16">
            <aside>
                <nav class="ui-card p-5 lg:sticky lg:top-28" aria-label="Privacy Policy contents">
                    <h2 class="text-sm font-semibold text-ui-ink">On this page</h2>
                    <ol class="mt-4 space-y-1 text-sm leading-5 text-ui-text">
                        <?php foreach ($privacySections as $privacySection): ?>
                        <li>
                            <a href="#<?= htmlspecialchars($privacySection['id'], ENT_QUOTES, 'UTF-8') ?>"
                                class="block rounded-lg px-3 py-2 transition hover:bg-ui-brand-soft hover:text-brand-royal">
                                <?= htmlspecialchars($privacySection['label'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <a href="<?= BASE_URL ?>/contact"
                        class="ui-button ui-button-secondary mt-5 w-full text-center">
                        Contact the administrator
                    </a>
                </nav>
            </aside>

            <article class="min-w-0" aria-label="Privacy Policy">
                <section id="about" class="scroll-mt-28 border-b border-ui-border pb-10">
                    <p class="ui-eyebrow">01</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        About this notice
                    </h2>
                    <div class="mt-5 space-y-4 text-base leading-7 text-ui-text">
                        <p>
                            This notice applies when you browse the platform, create or use an account, publish a
                            discussion or reply, upload a file, or send a contact message. The administrator responsible
                            for the installation decides how its data and service providers are configured.
                        </p>
                        <p>
                            We aim to collect only the information needed to run a safe academic discussion service. We
                            do not use the application for behavioural advertising, and the application contains no
                            analytics or advertising trackers.
                        </p>
                    </div>
                </section>

                <section id="information" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">02</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Information we handle
                    </h2>
                    <dl class="mt-6 divide-y divide-ui-border overflow-hidden rounded-xl border border-ui-border bg-white">
                        <div class="p-5 sm:grid sm:grid-cols-[180px_minmax(0,1fr)] sm:gap-6">
                            <dt class="font-semibold text-ui-ink">Account details</dt>
                            <dd class="mt-2 leading-7 text-ui-text sm:mt-0">
                                First and last name, username, email address, password hash, role, optional avatar, and
                                account creation or update times. Passwords are not stored as readable text.
                            </dd>
                        </div>
                        <div class="p-5 sm:grid sm:grid-cols-[180px_minmax(0,1fr)] sm:gap-6">
                            <dt class="font-semibold text-ui-ink">Forum activity</dt>
                            <dd class="mt-2 leading-7 text-ui-text sm:mt-0">
                                Discussion titles and content, replies, selected module, solved status, view counts,
                                authorship, and posting or editing times.
                            </dd>
                        </div>
                        <div class="p-5 sm:grid sm:grid-cols-[180px_minmax(0,1fr)] sm:gap-6">
                            <dt class="font-semibold text-ui-ink">Uploads</dt>
                            <dd class="mt-2 leading-7 text-ui-text sm:mt-0">
                                Avatars and files attached to discussions or replies, together with the original file
                                name, file type, size, storage path, and upload time.
                            </dd>
                        </div>
                        <div class="p-5 sm:grid sm:grid-cols-[180px_minmax(0,1fr)] sm:gap-6">
                            <dt class="font-semibold text-ui-ink">Contact messages</dt>
                            <dd class="mt-2 leading-7 text-ui-text sm:mt-0">
                                Name, email address, subject, message, submission time, handling status, and account ID
                                when a signed-in user contacts the administrator.
                            </dd>
                        </div>
                        <div class="p-5 sm:grid sm:grid-cols-[180px_minmax(0,1fr)] sm:gap-6">
                            <dt class="font-semibold text-ui-ink">Session information</dt>
                            <dd class="mt-2 leading-7 text-ui-text sm:mt-0">
                                An essential PHP session identifier and temporary session state used for sign-in, form
                                security, notifications, validation, and avoiding duplicate discussion view counts in
                                the same session.
                            </dd>
                        </div>
                    </dl>
                    <p class="mt-5 text-sm leading-6 text-ui-muted">
                        The application does not directly record IP addresses or browser fingerprints. The hosting
                        server and external resource providers may still receive standard request information such as an
                        IP address, browser details, requested URL, and time of access in their technical logs.
                    </p>
                </section>

                <section id="public-content" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">03</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        What becomes public
                    </h2>
                    <div class="mt-5 space-y-4 text-base leading-7 text-ui-text">
                        <p>
                            Discussion pages are public. Your displayed name, username, avatar, discussions, replies,
                            attachments, and related times can be seen by visitors and may be indexed by search engines.
                            Your account email address and password hash are not shown on discussion pages.
                        </p>
                        <p>
                            Files in the public uploads area are not confidential. The platform is not designed to
                            request or intentionally process special-category information. Do not publish private or
                            sensitive information about yourself or another person. Administrators may remove it when it
                            is identified; contact the administrator promptly if it is disclosed accidentally. Contact
                            messages are restricted to authorised administrators and are not displayed in the forum.
                        </p>
                    </div>
                </section>

                <section id="purposes" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">04</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Why we use information
                    </h2>
                    <ul class="mt-5 list-disc space-y-3 pl-6 text-base leading-7 text-ui-text marker:text-brand-royal">
                        <li>Create, authenticate, maintain, and secure user accounts.</li>
                        <li>Publish discussions, replies, author details, and files at the user's request.</li>
                        <li>Organise content by module, show activity, and support solved-answer features.</li>
                        <li>Moderate the service, enforce permissions, prevent misuse, and troubleshoot faults.</li>
                        <li>Store, review, and respond to contact messages, including sending them through the configured
                            email provider.</li>
                        <li>Comply with legal duties and establish, exercise, or defend legal claims where necessary.</li>
                    </ul>
                    <div class="mt-6 overflow-hidden rounded-xl border border-ui-border bg-white text-sm leading-6">
                        <div class="border-b border-ui-border p-5">
                            <p class="font-semibold text-ui-ink">Legitimate interests</p>
                            <p class="mt-2 text-ui-text">
                                The operator relies on legitimate interests to create and administer accounts, publish
                                content that users intentionally submit, respond to contact messages, deliver essential
                                site resources, moderate discussions, prevent misuse, and keep the academic forum secure
                                and useful. These interests are balanced against users' privacy rights.
                            </p>
                        </div>
                        <div class="p-5">
                            <p class="font-semibold text-ui-ink">Legal obligations and claims</p>
                            <p class="mt-2 text-ui-text">
                                Information may be processed where necessary to meet a legal obligation or establish,
                                exercise, or defend a legal claim. Essential account and session processing is not based
                                on marketing consent.
                            </p>
                        </div>
                    </div>
                    <p class="mt-5 text-sm leading-6 text-ui-muted">
                        Name, username, email address, and password are required to create an account; without them the
                        platform cannot provide an account. Name, email, subject, and message are required when using the
                        contact form so the administrator can identify, review, and respond to the enquiry.
                    </p>
                </section>

                <section id="sharing" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">05</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Sharing and service providers
                    </h2>
                    <div class="mt-5 space-y-4 text-base leading-7 text-ui-text">
                        <p>Information may be available to:</p>
                        <ul class="list-disc space-y-3 pl-6 marker:text-brand-royal">
                            <li>authorised administrators who manage accounts, forum content, and contact messages;</li>
                            <li>the hosting, database, and storage providers selected for this installation;</li>
                            <li>the configured SMTP email provider when a contact message is delivered to an administrator;
                            </li>
                            <li>
                                <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer"
                                    class="font-semibold text-brand-royal underline underline-offset-4">Google</a>
                                when your browser requests Google Fonts,
                                <a href="https://www.jsdelivr.com/terms" target="_blank" rel="noopener noreferrer"
                                    class="font-semibold text-brand-royal underline underline-offset-4">jsDelivr</a>
                                when it requests code-highlighting files, and the
                                <a href="https://www.gre.ac.uk/about-us/governance/information-compliance/privacy"
                                    target="_blank" rel="noopener noreferrer"
                                    class="font-semibold text-brand-royal underline underline-offset-4">University of
                                    Greenwich Moodle service</a>
                                when it requests the site icon; and
                            </li>
                            <li>regulators, courts, law enforcement, or professional advisers when disclosure is required
                                or permitted by law.</li>
                        </ul>
                        <p>
                            We do not sell personal information and do not share it for targeted advertising. Some
                            providers may process technical request data outside the UK. The hosting and email providers,
                            processing locations, and any adequacy decision or contractual transfer safeguards depend on
                            how this installation is configured. Contact the administrator for the current details and a
                            copy of any applicable safeguards.
                        </p>
                    </div>
                </section>

                <section id="cookies" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">06</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Cookies and sessions
                    </h2>
                    <div class="mt-6 overflow-hidden rounded-xl border border-ui-border bg-white">
                        <div class="border-b border-ui-border p-5">
                            <p class="font-semibold text-ui-ink">Essential PHP session cookie</p>
                            <p class="mt-2 text-sm leading-6 text-ui-text">
                                The server sets a session identifier so the platform can keep you signed in, protect
                                forms, remember temporary messages, and maintain essential session state. The cookie name
                                and lifetime are controlled by the server configuration; it is normally a browser-session
                                cookie unless the operator configures it differently.
                            </p>
                        </div>
                        <div class="p-5">
                            <p class="font-semibold text-ui-ink">No analytics or advertising cookies</p>
                            <p class="mt-2 text-sm leading-6 text-ui-text">
                                The application does not set analytics or advertising cookies and does not use local or
                                session storage. External resources may receive cookies already associated with their own
                                domains under those providers' policies.
                            </p>
                        </div>
                    </div>
                </section>

                <section id="retention" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">07</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Retention and deletion
                    </h2>
                    <div class="mt-5 space-y-4 text-base leading-7 text-ui-text">
                        <p>
                            Account details are kept while an account is active or until the installation closes. There
                            is currently no self-service account deletion. When an administrator deletes an account, the
                            name, username, and email in the account record are replaced with de-identified values, the
                            password and avatar reference are cleared, the service attempts to remove the avatar file,
                            and the account is marked deleted. This is pseudonymisation, not complete anonymisation: the
                            same account ID continues to link retained discussions and replies, including their
                            attachments, so existing academic conversations still make sense.
                        </p>
                        <p>
                            Discussions and replies remain until they are removed by an authorised user or administrator.
                            Removed content is hidden through soft deletion and may remain in the database. Related upload
                            records are deleted and the service attempts to remove the stored files. Contact messages
                            remain available to administrators until they are deleted or the installation closes. They
                            keep the name, email, and message originally submitted even if a linked account is later
                            de-identified.
                        </p>
                        <p>
                            The application does not currently enforce an automatic time-based purge schedule. Session,
                            server-log, and backup retention depends on the active hosting configuration. The operator
                            should review stored information and delete or anonymise it when it is no longer needed for
                            the purposes described above, subject to security, backup, and legal requirements.
                        </p>
                    </div>
                </section>

                <section id="security" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">08</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Security
                    </h2>
                    <div class="mt-5 space-y-4 text-base leading-7 text-ui-text">
                        <p>
                            The application uses password hashing, session-ID rotation on sign-in and sign-out, form
                            security tokens, role and ownership checks, server-side validation, and file type and size
                            checks. Uploaded files receive random stored names.
                        </p>
                        <p>
                            No online service can guarantee complete security. Use a unique password and avoid publishing
                            confidential information in discussions, replies, avatars, or attachments. If you believe
                            information or an account has been exposed, contact the administrator promptly.
                        </p>
                    </div>
                </section>

                <section id="rights" class="scroll-mt-28 border-b border-ui-border py-10">
                    <p class="ui-eyebrow">09</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Your rights
                    </h2>
                    <div class="mt-5 space-y-4 text-base leading-7 text-ui-text">
                        <p>
                            Depending on the law and the circumstances, you may have rights to access a copy of your
                            personal information, correct inaccurate information, request deletion or restriction,
                            receive certain information in a portable format, and complain about how it is handled.
                        </p>
                        <div class="rounded-xl border border-ui-border bg-ui-brand-soft p-5">
                            <p class="font-semibold text-ui-ink">Your right to object</p>
                            <p class="mt-2 text-sm leading-6">
                                You may object to processing based on legitimate interests. Tell the administrator what
                                processing you object to and why it affects you. The request will be considered against
                                any compelling legitimate grounds or legal-claims requirements.
                            </p>
                        </div>
                        <p>
                            The application does not make decisions about you using automated decision-making or
                            profiling. Some rights depend on the legal basis and may be limited where information must be
                            retained for legal, security, or freedom-of-expression reasons.
                        </p>
                    </div>
                </section>

                <section id="contact" class="scroll-mt-28 pt-10">
                    <p class="ui-eyebrow">10</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ui-ink sm:text-3xl">
                        Contact and complaints
                    </h2>
                    <div class="mt-5 space-y-4 text-base leading-7 text-ui-text">
                        <p>
                            To ask a privacy question, request access, correction, deletion, or objection, or report a
                            concern, use the
                            <a href="<?= BASE_URL ?>/contact"
                                class="font-semibold text-brand-royal underline underline-offset-4">contact form</a>.
                            Include enough detail to identify your account or request, but do not send passwords.
                        </p>
                        <p>
                            Where current UK requirements apply, a data-protection complaint will be acknowledged within
                            30 days and the administrator will investigate, keep you informed, and communicate the
                            outcome without undue delay. You may also complain to the
                            <a href="https://ico.org.uk/make-a-complaint/" target="_blank" rel="noopener noreferrer"
                                class="font-semibold text-brand-royal underline underline-offset-4">Information
                                Commissioner's Office</a>.
                        </p>
                        <p>
                            We may update this notice when the platform, its providers, or legal requirements change. A
                            revised notice will be published on this page with a new last-updated date.
                        </p>
                    </div>
                </section>
            </article>
        </div>
    </div>
</section>
