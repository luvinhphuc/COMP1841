<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FormatHelper;
use App\Helpers\ViewHelper;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Services\AttachmentService;
use App\Services\AuthService;
use Throwable;

/**
 * Handles profile dashboards, account preferences, avatars, and password changes.
 */
class ProfileController extends Controller
{
    private const DEFAULT_DISCUSSION_LIMIT = 5;
    private const DISCUSSION_LIMIT_OPTIONS = [5, 10, 20, 50];

    private ?UserRepository $userRepository = null;

    // Route actions --------------------------------------------------------
    public function index()
    {
        [$authUserId, $authUserEntity, $authUser] = $this->authenticatedProfileUser();

        try {
            $statistics = $this->userRepository->findProfileStatistics($authUserId);
        } catch (Throwable) {
            $statistics = null;
        }

        $statistics = $statistics ?? [
            'questions_asked' => 0,
            'replies_posted' => 0,
            'solved_questions' => 0,
            'total_post_views' => 0,
            'member_since' => (string) $authUserEntity->created_at,
        ];

        $this->view('profile/index', [
            'pageTitle' => 'Profile - ',
            'authUser' => $authUser,
            'profileHeader' => $this->profileHeader($authUser),
            'profileActiveTab' => 'summary',
            'statistics' => $statistics,
        ]);
    }

    public function questions()
    {
        [$authUserId, , $authUser] = $this->authenticatedProfileUser();
        [$myDiscussions, $discussionPagination] = $this->paginatedDiscussions($authUserId);

        $this->view('profile/questions', [
            'pageTitle' => 'My Questions - ',
            'authUser' => $authUser,
            'profileHeader' => $this->profileHeader($authUser),
            'profileActiveTab' => 'questions',
            'myDiscussions' => $myDiscussions,
            'discussionPagination' => $discussionPagination,
        ]);
    }

    public function preferences()
    {
        [, , $authUser] = $this->authenticatedProfileUser();
        $profileState = $this->sessionState('preferences_profile_state');
        $avatarState = $this->sessionState('preferences_avatar_state');
        $passwordState = $this->sessionState('preferences_password_state');

        $this->view('profile/preferences', [
            'pageTitle' => 'Preferences - ',
            'authUser' => $authUser,
            'profileHeader' => $this->profileHeader($authUser),
            'profileActiveTab' => 'preferences',
            'profileErrors' => $profileState['errors'] ?? [],
            'profileOld' => $profileState['old'] ?? [],
            'avatarErrors' => $avatarState['errors'] ?? [],
            'passwordErrors' => $passwordState['errors'] ?? [],
        ]);

        unset(
            $_SESSION['preferences_profile_state'],
            $_SESSION['preferences_avatar_state'],
            $_SESSION['preferences_password_state']
        );
    }

    public function updateProfile()
    {
        $this->requirePost(BASE_URL . '/profile/preferences');

        [$authUserId] = $this->authenticatedProfileUser();

        $profileData = [
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'username' => trim((string) ($_POST['username'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
        ];

        try {
            $profileErrors = (new AuthService($this->userRepository))->validateAccount($profileData, $authUserId);

            if (!empty($profileErrors)) {
                $this->redirectWithState('preferences_profile_state', [
                    'errors' => $profileErrors,
                    'old' => $profileData,
                ]);
            }

            if (!$this->userRepository->updateProfile($authUserId, $profileData)) {
                $this->redirectWithState('preferences_profile_state', [
                    'errors' => ['general' => 'Unable to update your profile. Please try again.'],
                    'old' => $profileData,
                ]);
            }

            $this->refreshAuthSession($authUserId);
            $this->redirectWithToast(BASE_URL . '/profile/preferences', [
                'type' => 'success',
                'title' => 'Profile updated',
                'message' => 'Your profile information has been saved.',
            ]);
        } catch (Throwable) {
            $this->redirectWithState('preferences_profile_state', [
                'errors' => ['general' => 'Unable to update your profile right now. Please try again.'],
                'old' => $profileData,
            ]);
        }
    }

    public function updateAvatar()
    {
        $this->requirePost(BASE_URL . '/profile/preferences');

        [$authUserId, $authUserEntity] = $this->authenticatedProfileUser();
        $attachmentService = new AttachmentService();

        try {
            [$avatarData, $avatarErrors] = $this->validateAvatarUpload($attachmentService);

            if (!empty($avatarErrors)) {
                $this->redirectWithState('preferences_avatar_state', [
                    'errors' => $avatarErrors,
                ]);
            }

            $avatarErrors = $this->persistAvatarUpdate(
                $authUserId,
                $authUserEntity,
                $avatarData,
                $attachmentService
            );

            if (!empty($avatarErrors)) {
                $this->redirectWithState('preferences_avatar_state', [
                    'errors' => $avatarErrors,
                ]);
            }

            $this->refreshAuthSession($authUserId);
            $this->redirectWithToast(BASE_URL . '/profile/preferences', [
                'type' => 'success',
                'title' => 'Avatar updated',
                'message' => 'Your new avatar has been saved.',
            ]);
        } catch (Throwable) {
            $this->redirectWithState('preferences_avatar_state', [
                'errors' => ['general' => 'Unable to update your avatar right now. Please try again.'],
            ]);
        }
    }

    public function updatePassword()
    {
        $this->requirePost(BASE_URL . '/profile/preferences');

        [$authUserId, $authUserEntity] = $this->authenticatedProfileUser();

        $passwordData = [
            'current_password' => (string) ($_POST['current_password'] ?? ''),
            'new_password' => (string) ($_POST['new_password'] ?? ''),
            'confirm_password' => (string) ($_POST['confirm_password'] ?? ''),
        ];
        $passwordErrors = $this->validatePassword($passwordData, $authUserEntity);

        if (!empty($passwordErrors)) {
            $this->redirectWithState('preferences_password_state', [
                'errors' => $passwordErrors,
            ]);
        }

        try {
            $passwordHash = password_hash($passwordData['new_password'], PASSWORD_DEFAULT);

            if (!$this->userRepository->updatePassword($authUserId, $passwordHash)) {
                $this->redirectWithState('preferences_password_state', [
                    'errors' => ['general' => 'Unable to change your password. Please try again.'],
                ]);
            }

            $this->redirectWithToast(BASE_URL . '/profile/preferences', [
                'type' => 'success',
                'title' => 'Password changed',
                'message' => 'Your password has been updated successfully.',
            ]);
        } catch (Throwable) {
            $this->redirectWithState('preferences_password_state', [
                'errors' => ['general' => 'Unable to change your password right now. Please try again.'],
            ]);
        }
    }

    public function routeNotFound()
    {
        $this->notFound();
    }

    // Authentication and view data
    private function authenticatedProfileUser()
    {
        $authUserId = $this->currentUserId();

        if ($authUserId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $this->userRepository = $this->userRepository ?? new UserRepository();

        try {
            $authUserEntity = $this->userRepository->findById($authUserId);
        } catch (Throwable) {
            $authUserEntity = null;
        }

        if ($authUserEntity === null) {
            unset($_SESSION['auth_user']);
            $this->redirectTo(BASE_URL . '/login');
        }

        $authUser = $authUserEntity->toArray();
        $_SESSION['auth_user'] = $authUser;

        return [$authUserId, $authUserEntity, $authUser];
    }

    private function profileHeader(array $authUser)
    {
        $memberSinceTimestamp = strtotime((string) ($authUser['created_at'] ?? ''));

        return [
            'username' => trim((string) ($authUser['username'] ?? '')),
            'full_name' => FormatHelper::textOr($authUser['full_name'] ?? '', 'Student'),
            'role' => strtolower((string) ($authUser['role'] ?? 'student')),
            'avatar_url' => FormatHelper::authorAvatarUrl($authUser),
            'avatar_initial' => FormatHelper::authorInitial($authUser),
            'member_since_raw' => (string) ($authUser['created_at'] ?? ''),
            'member_since' => $memberSinceTimestamp !== false
                ? date('F j, Y', $memberSinceTimestamp)
                : 'Unknown',
        ];
    }

    // Avatar workflow
    private function validateAvatarUpload(AttachmentService $attachmentService)
    {
        $avatar = $attachmentService->validatedAvatar($_FILES['avatar'] ?? null);
        $avatarError = trim((string) ($avatar['error'] ?? ''));

        return [
            $avatar,
            $avatarError !== '' ? ['avatar' => $avatarError] : [],
        ];
    }

    private function persistAvatarUpdate(
        int $authUserId,
        User $authUserEntity,
        array $avatarData,
        AttachmentService $attachmentService
    ) {
        $storedAvatar = $attachmentService->storeAvatar($avatarData);

        if ($storedAvatar === null) {
            return ['avatar' => 'The avatar could not be saved. Please choose another image.'];
        }

        try {
            if (!$this->userRepository->updateAvatar($authUserId, $storedAvatar)) {
                $attachmentService->removeStoredAttachment(['path' => $storedAvatar]);

                return ['general' => 'Unable to update your avatar. Please try again.'];
            }

            $this->removePreviousAvatar($authUserEntity, $storedAvatar, $attachmentService);

            return [];
        } catch (Throwable $exception) {
            $attachmentService->removeStoredAttachment(['path' => $storedAvatar]);

            throw $exception;
        }
    }

    private function removePreviousAvatar(
        User $authUserEntity,
        string $storedAvatar,
        AttachmentService $attachmentService
    ) {
        $oldAvatar = ltrim(str_replace('\\', '/', trim((string) ($authUserEntity->avatar ?? ''))), '/');

        if ($oldAvatar !== $storedAvatar
            && preg_match('#^uploads/avatars/[A-Za-z0-9._-]+$#', $oldAvatar)) {
            $attachmentService->removeStoredAttachment(['path' => $oldAvatar]);
        }
    }

    private function refreshAuthSession(int $authUserId)
    {
        $authUserEntity = $this->userRepository->findById($authUserId);

        if ($authUserEntity === null) {
            unset($_SESSION['auth_user']);
            $this->redirectTo(BASE_URL . '/login');
        }

        $_SESSION['auth_user'] = $authUserEntity->toArray();
    }

    // Validation
    private function validatePassword(array $passwordData, User $authUserEntity)
    {
        $errors = [];

        if ($passwordData['current_password'] === '') {
            $errors['current_password'] = 'Current password is required.';
        } elseif (!password_verify($passwordData['current_password'], (string) ($authUserEntity->password ?? ''))) {
            $errors['current_password'] = 'Current password is incorrect.';
        }

        $errors = array_merge($errors, (new AuthService($this->userRepository))->validatePassword(
            $passwordData['new_password'],
            $passwordData['confirm_password'],
            'new_password',
            'confirm_password',
            'New password'
        ));

        return $errors;
    }

    // Pagination and session state
    private function paginatedDiscussions(int $authUserId)
    {
        $requestedPage = max(1, (int) ($_GET['page'] ?? 1));
        $pageLimit = $this->discussionPageLimit();
        $totalDiscussions = 0;

        try {
            $postRepository = new PostRepository();
            $totalDiscussions = $postRepository->countByUserId($authUserId);
            $totalPages = max(1, (int) ceil($totalDiscussions / $pageLimit));
            $currentPage = min($requestedPage, $totalPages);
            $offset = ($currentPage - 1) * $pageLimit;
            $postRecords = $postRepository->findByUserId($authUserId, $pageLimit, $offset);
        } catch (Throwable) {
            $postRecords = [];
            $currentPage = 1;
            $totalPages = 1;
        }

        $firstPage = max(1, min($currentPage - 2, $totalPages - 4));
        $lastPage = min($totalPages, $firstPage + 4);
        $pages = [];

        for ($page = $firstPage; $page <= $lastPage; $page++) {
            $pages[] = [
                'number' => $page,
                'url' => $this->discussionPageUrl($page, $pageLimit),
                'current' => $page === $currentPage,
            ];
        }

        return [
            array_map(
                fn (array $postRecord) => ViewHelper::formatDiscussionCard($postRecord),
                $postRecords
            ),
            [
                'current' => $currentPage,
                'total' => $totalPages,
                'total_items' => $totalDiscussions,
                'per_page' => $pageLimit,
                'per_page_options' => self::DISCUSSION_LIMIT_OPTIONS,
                'pages' => $pages,
                'has_previous' => $currentPage > 1,
                'has_next' => $currentPage < $totalPages,
                'previous_url' => $this->discussionPageUrl(max(1, $currentPage - 1), $pageLimit),
                'next_url' => $this->discussionPageUrl(min($totalPages, $currentPage + 1), $pageLimit),
                'path' => BASE_URL . '/profile/questions',
                'query' => [],
            ],
        ];
    }

    private function discussionPageLimit()
    {
        $pageLimit = filter_var(
            $_GET['per_page'] ?? self::DEFAULT_DISCUSSION_LIMIT,
            FILTER_VALIDATE_INT
        );

        return in_array($pageLimit, self::DISCUSSION_LIMIT_OPTIONS, true)
            ? $pageLimit
            : self::DEFAULT_DISCUSSION_LIMIT;
    }

    private function discussionPageUrl(int $page, int $pageLimit)
    {
        $query = ['per_page' => $pageLimit];

        if ($page > 1) {
            $query['page'] = $page;
        }

        return BASE_URL . '/profile/questions?' . http_build_query($query);
    }

    private function sessionState(string $key)
    {
        $state = $_SESSION[$key] ?? [];

        return is_array($state) ? $state : [];
    }

    private function redirectWithState(string $key, array $state)
    {
        $_SESSION[$key] = $state;
        $this->redirectTo(BASE_URL . '/profile/preferences');
    }
}
