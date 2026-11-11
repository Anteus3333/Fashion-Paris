<?php
require_once __DIR__ . '/../Repositories/MysqlStatsRepository.php';

class MysqlStatsModel {
    private MysqlStatsRepository $repository;

    public function __construct() {
        $this->repository = new MysqlStatsRepository();
    }

    public function resume(): array {
        return $this->repository->resume();
    }

    public function commandesPourSync(): array {
        return $this->repository->commandesPourSync();
    }
}
