<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CekDebts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cek-debts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $debts  =   \App\Models\Debtor::where('status', 'unpaid')->get();

        $this->info('cek status overdue');

        foreach ($debts as $debt) {
            if (\Carbon\Carbon::parse($debt->due_date)->isPast()) {

                $this->info($debt->name . " " . 'due at : '. \Carbon\Carbon::parse($debt->due_date)->format('D d-m-Y'));

                $debt->update([
                    'status' => 'overdue'
                ]);
            }
        }

        $this->info('done');
    }
}
