<?php

namespace App\Support;

trait DispatchesToast
{
    protected function toast(string $message, string $type = 'success'): void
    {
        $this->dispatch('toast', message: $message, type: $type);
    }

    protected function toastSuccess(string $message): void
    {
        $this->toast($message, 'success');
    }

    protected function toastError(string $message): void
    {
        $this->toast($message, 'error');
    }

    protected function toastWarning(string $message): void
    {
        $this->toast($message, 'warning');
    }
}
