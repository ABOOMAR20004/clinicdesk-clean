<?php

declare(strict_types=1);

class ErrorController
{
    public function index(): void
    {
        $this->notFound();
    }

    public function forbidden(): void
    {
        $this->{"403"}();
    }

    public function notFound(): void
    {
        $this->{"404"}();
    }

    public function __call(string $name, array $arguments): void
    {
        if ($name === '403') {
            view('errors/403', ['pageTitle' => 'Forbidden']);
            return;
        }

        view('errors/404', ['pageTitle' => 'Not Found']);
    }
}
