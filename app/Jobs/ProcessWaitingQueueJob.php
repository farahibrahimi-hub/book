<?php

namespace App\Jobs;

use App\Actions\ReserveBookAction;
use App\Models\Book;
use App\Models\WaitingQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessWaitingQueueJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $bookId,
    ) {}

    public function handle(ReserveBookAction $reserveAction): void
    {
        $book = Book::find($this->bookId);

        if (! $book || ! $book->hasAvailableCopies()) {
            return;
        }

        $nextInLine = WaitingQueue::nextInLine($this->bookId);

        if (! $nextInLine) {
            return;
        }

        $user = $nextInLine->user;

        try {
            // Remove from queue before attempting reservation
            $nextInLine->delete();

            $reserveAction->execute($user, $book);

            Log::info("Assigned book [{$book->title}] from queue to user [{$user->name}]");
        } catch (\InvalidArgumentException $e) {
            Log::warning("Failed to assign queued book [{$book->title}] to user [{$user->name}]: {$e->getMessage()}");
        }
    }
}
