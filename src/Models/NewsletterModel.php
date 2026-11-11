<?php
require_once __DIR__ . '/../Repositories/NewsletterRepository.php';

class NewsletterModel {
    private NewsletterRepository $repository;

    public function __construct() {
        $this->repository = new NewsletterRepository();
    }

    public function inscrire(string $email): bool {
        return $this->repository->inscrire($email);
    }
}
