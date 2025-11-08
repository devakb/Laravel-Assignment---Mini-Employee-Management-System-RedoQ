<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Employee $employee)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Hi '
                . $this->employee->name . ' welcome to our company. You email id is '
                . $this->employee->email . ' and your department is '
                . $this->employee->department->name . ' and your salary is '
                . $this->employee->salary. '. Thank you for joining us!');
    }
}
