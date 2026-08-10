<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FormatHelper;
use App\Repositories\ContactRepository;
use App\Services\MailService;
use Throwable;

/**
 * Stores contact requests before attempting a non-critical email notification.
 */
class ContactController extends Controller
{
    private ?ContactRepository $contactRepository;
    private ?MailService $mailService;

    public function __construct(
        ?ContactRepository $contactRepository = null,
        ?MailService $mailService = null
    ) {
        $this->contactRepository = $contactRepository;
        $this->mailService = $mailService;
    }

    // Route actions --------------------------------------------------------
    public function index()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            $this->redirectTo(BASE_URL . '/contact');
        }

        $state = $_SESSION['contact_form_state'] ?? [];
        $state = is_array($state) ? $state : [];
        $errors = is_array($state['errors'] ?? null) ? $state['errors'] : [];
        $old = is_array($state['old'] ?? null) ? $state['old'] : [];
        $authUser = $this->authenticatedContactUser();

        if ($authUser !== null) {
            $old = array_merge($old, $this->contactIdentity($authUser));
        }

        $this->view('contact/index', [
            'pageTitle' => 'Contact - ',
            'errors' => $errors,
            'old' => $old,
            'identityIsReadOnly' => $authUser !== null,
            'pageScripts' => ['contact.js'],
        ]);

        unset($_SESSION['contact_form_state']);
    }

    public function store()
    {
        $this->requirePost(BASE_URL . '/contact');

        $authUser = $this->authenticatedContactUser();
        $old = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'subject' => trim((string) ($_POST['subject'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
        ];
        $data = $old;
        $data['user_id'] = null;

        if ($authUser !== null) {
            $data = array_merge($data, $this->contactIdentity($authUser));
            $data['user_id'] = (int) ($authUser['id'] ?? 0);
        }

        $errors = $this->validateContact($data);

        if (!empty($errors)) {
            $this->redirectWithErrors($errors, $data);
        }

        try {
            $this->contactRepository = $this->contactRepository ?? new ContactRepository();
            $contactId = $this->contactRepository->create($data);

            if ($contactId <= 0) {
                $this->redirectWithErrors([
                    'general' => 'Unable to submit your message. Please check the details and try again.',
                ], $data);
            }
        } catch (Throwable $exception) {
            error_log('Contact message could not be saved: ' . $exception->getMessage());
            $this->redirectWithErrors([
                'general' => 'Unable to submit your message right now. Please try again.',
            ], $data);
        }

        try {
            $this->mailService = $this->mailService ?? new MailService();
            $this->mailService->sendToAdmin(
                $this->notificationSubject($contactId, $data['subject']),
                $this->notificationHtml($contactId, $data),
                $this->notificationText($contactId, $data),
                [
                    'email' => $data['email'],
                    'name' => $data['name'],
                ]
            );
        } catch (Throwable $exception) {
            error_log(sprintf(
                'Contact notification failed for contact #%d: %s',
                $contactId,
                $exception->getMessage()
            ));
        }

        $this->redirectWithToast(BASE_URL . '/contact', [
            'type' => 'success',
            'title' => 'Message received',
            'message' => 'Your message has been saved. An administrator will review it.',
        ]);
    }

    // Identity, validation, and notification formatting ------------------
    private function authenticatedContactUser()
    {
        $authUser = $this->currentUser();
        $authUserId = $this->currentUserId();

        if ($authUser === null || $authUserId === null) {
            return null;
        }

        $authUser['id'] = $authUserId;

        return $authUser;
    }

    private function contactIdentity(array $authUser)
    {
        $fullName = trim((string) ($authUser['full_name'] ?? ''));

        if (FormatHelper::textLength($fullName) > 75) {
            $fullName = FormatHelper::shortText($fullName, 75);
        }

        return [
            'name' => $fullName,
            'email' => trim((string) ($authUser['email'] ?? '')),
        ];
    }

    private function validateContact(array $data)
    {
        $errors = [];
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $subject = trim((string) ($data['subject'] ?? ''));
        $message = trim((string) ($data['message'] ?? ''));

        if ($name === '') {
            $errors['name'] = 'Please enter your name.';
        } elseif (FormatHelper::textLength($name) > 75) {
            $errors['name'] = 'Name must be 75 characters or fewer.';
        }

        if ($email === '') {
            $errors['email'] = 'Please enter your email address.';
        } elseif (FormatHelper::textLength($email) > 150) {
            $errors['email'] = 'Email must be 150 characters or fewer.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($subject === '') {
            $errors['subject'] = 'Please enter a subject.';
        } elseif (FormatHelper::textLength($subject) > 255) {
            $errors['subject'] = 'Subject must be 255 characters or fewer.';
        }

        if ($message === '') {
            $errors['message'] = 'Please enter a message.';
        } elseif (FormatHelper::textLength($message) > 5000) {
            $errors['message'] = 'Message must be 5000 characters or fewer.';
        }

        return $errors;
    }

    private function redirectWithErrors(array $errors, array $old)
    {
        unset($old['user_id']);

        $_SESSION['contact_form_state'] = [
            'errors' => $errors,
            'old' => $old,
        ];

        $this->redirectTo(BASE_URL . '/contact');
    }

    private function notificationSubject(int $contactId, string $subject)
    {
        $subject = preg_replace('/[\r\n]+/', ' ', trim($subject)) ?: 'Contact message';

        return sprintf('[Contact #%d] %s', $contactId, $subject);
    }

    private function notificationHtml(int $contactId, array $data)
    {
        $escape = static fn ($value) => htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        return '<h1>New contact message</h1>'
            . '<p><strong>Contact ID:</strong> #' . $contactId . '</p>'
            . '<p><strong>Name:</strong> ' . $escape($data['name']) . '</p>'
            . '<p><strong>Email:</strong> ' . $escape($data['email']) . '</p>'
            . '<p><strong>Subject:</strong> ' . $escape($data['subject']) . '</p>'
            . '<p><strong>Message:</strong></p>'
            . '<p>' . nl2br($escape($data['message'])) . '</p>';
    }

    private function notificationText(int $contactId, array $data)
    {
        return implode("\n", [
            'New contact message',
            'Contact ID: #' . $contactId,
            'Name: ' . $data['name'],
            'Email: ' . $data['email'],
            'Subject: ' . $data['subject'],
            '',
            'Message:',
            $data['message'],
        ]);
    }
}
