<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Repositories\ContactRepository;
use Throwable;

/**
 * Provides the administrative contact inbox and message state transitions.
 */
class ContactController extends AdminController
{
    public function index()
    {
        $this->requireAdmin();
        $filters = [
            'q' => trim((string)($_GET['q'] ?? '')),
            'status' => in_array($_GET['status'] ?? '', ['unread', 'read', 'resolved'], true)
                ? $_GET['status']
                : '',
        ];
        $page = $this->pageNumber();
        $pageLimit = $this->pageLimit();
        $loadError = false;

        try {
            $contactRepository = new ContactRepository();
            $total = $contactRepository->countAll($filters);
            $pagination = $this->pagination('/admin/contacts', $total, $page, $filters, $pageLimit);
            $contactRecords = $contactRepository->findPaginated(
                $filters,
                $pageLimit,
                $pagination['offset']
            );
        } catch (Throwable $exception) {
            error_log('Admin contact messages could not be loaded: ' . $exception->getMessage());
            $contactRecords = [];
            $pagination = $this->pagination('/admin/contacts', 0, 1, $filters, $pageLimit);
            $loadError = true;
        }

        $this->view('admin/contacts/index', [
            'adminSection' => 'contacts',
            'pageTitle' => 'Contact Messages',
            'pageScripts' => ['modal.js'],
            'contacts' => $contactRecords,
            'filters' => $filters,
            'pagination' => $pagination,
            'loadError' => $loadError,
        ]);
    }

    public function show($contactMessageId = 0)
    {
        $this->requireAdmin();
        $contactMessageId = filter_var($contactMessageId, FILTER_VALIDATE_INT);

        if ($contactMessageId === false || $contactMessageId <= 0) {
            $this->notFound();
        }

        try {
            $contactRepository = new ContactRepository();
            $contactRecord = $contactRepository->findById($contactMessageId);

            if ($contactRecord === null) {
                $this->notFound();
            }
        } catch (Throwable $exception) {
            error_log('Admin contact message could not be loaded: ' . $exception->getMessage());
            $this->adminError('/admin/contacts', 'The contact message could not be loaded right now.');
        }

        $this->view('admin/contacts/show', [
            'adminSection' => 'contacts',
            'pageTitle' => 'Contact Message',
            'pageScripts' => ['modal.js'],
            'contact' => $contactRecord->toArray(),
        ]);
    }

    public function markAsRead($contactMessageId = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/contacts');

        $contactMessageId = filter_var($contactMessageId, FILTER_VALIDATE_INT);

        if ($contactMessageId === false || $contactMessageId <= 0) {
            $this->notFound();
        }

        try {
            $contactRepository = new ContactRepository();

            if ($contactRepository->findById($contactMessageId) === null) {
                $this->notFound();
            }

            if (!$contactRepository->updateReadStatus($contactMessageId)) {
                $this->adminError('/admin/contacts', 'The contact message could not be marked as read.');
            }
        } catch (Throwable $exception) {
            error_log('Admin contact message could not be marked as read: ' . $exception->getMessage());
            $this->adminError('/admin/contacts', 'The contact message could not be marked as read.');
        }

        $this->adminSuccess('/admin/contacts', 'Contact message marked as read.');
    }

    public function updateStatus($contactMessageId = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/contacts');

        $contactMessageId = filter_var($contactMessageId, FILTER_VALIDATE_INT);
        $status = strtolower(trim((string)($_POST['status'] ?? '')));

        if ($contactMessageId === false || $contactMessageId <= 0) {
            $this->notFound();
        }

        if (!in_array($status, ['unread', 'read', 'resolved'], true)) {
            $this->adminError('/admin/contacts', 'Please choose a valid contact status.');
        }

        try {
            $contactRepository = new ContactRepository();

            if ($contactRepository->findById($contactMessageId) === null) {
                $this->notFound();
            }

            if (!$contactRepository->updateStatus($contactMessageId, $status)) {
                $this->adminError('/admin/contacts', 'The contact status could not be updated.');
            }
        } catch (Throwable $exception) {
            error_log('Admin contact status could not be updated: ' . $exception->getMessage());
            $this->adminError('/admin/contacts', 'The contact status could not be updated.');
        }

        $this->adminSuccess('/admin/contacts', 'Contact status updated successfully.');
    }

    public function delete($contactMessageId = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/contacts');

        $contactMessageId = filter_var($contactMessageId, FILTER_VALIDATE_INT);

        if ($contactMessageId === false || $contactMessageId <= 0) {
            $this->notFound();
        }

        try {
            $contactRepository = new ContactRepository();

            if ($contactRepository->findById($contactMessageId) === null) {
                $this->notFound();
            }

            if (!$contactRepository->delete($contactMessageId)) {
                $this->adminError('/admin/contacts', 'The contact message could not be deleted.');
            }
        } catch (Throwable $exception) {
            error_log('Admin contact message could not be deleted: ' . $exception->getMessage());
            $this->adminError('/admin/contacts', 'The contact message could not be deleted.');
        }

        $this->adminSuccess('/admin/contacts', 'Contact message deleted successfully.');
    }
}
