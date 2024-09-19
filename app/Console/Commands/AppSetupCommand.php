<?php

namespace App\Console\Commands;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\SystemPool;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AppSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure the settings for the app.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running migration......');
        Artisan::call('migrate:fresh');
        $this->info('Done running migrations');

        $this->info('Running database seeders......');
        Artisan::call('db:seed');
        $this->info('Done running database seeders');

        $this->info('Running maker......');
        $this->generateMaker();

        $this->info('Running checker......');
        $this->generateChecker();

        $this->info('Running system pool......');
        $this->generateSystemPool();

        $this->info('Running dummy transactions......');
        $this->generateDummyTransactions();

        Artisan::call('optimize:clear');
    }

    protected function generateMaker()
    {
        return DB::transaction(function () {
            $maker = User::create([
                'name' => 'kingsley Maker',
                'email' => 'kingsley.maker@hotmail.com',
                'password' => 'kingsley',
            ]);
            \App\Models\ModelRole::assignRole('maker', $maker->id);
            $maker->wallet()->create(['initial_amount' => 0, 'actual_amount' => 0]);
        });

    }

    protected function generateChecker()
    {
        return DB::transaction(function () {
            $checker = User::create([
                'name' => 'kingsley Checker',
                'email' => 'kingsley.checker@hotmail.com',
                'password' => 'kingsley',
            ]);
            \App\Models\ModelRole::assignRole('checker', $checker->id);
        });
    }

    protected function generateSystemPool()
    {
        SystemPool::firstOrCreate(['balance' => 100000]);
    }

    protected function generateDummyTransactions()
    {
        $userId = User::role('maker')->value('id');

        for ($i = 0; $i < 10; $i++) {
            $arr = TransactionTypeEnum::cases();
            shuffle($arr);
            $transaction[$i] = [
                'amount' => 1000,
                'type' => $arr[0],
                'status' => TransactionStatusEnum::PENDING->value,
                'user_id' => $userId,
                'reference' => strtoupper(uniqid('TX-') . time()),
                'narration' => 'This is a dummy transaction.',
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Transaction::insert($transaction);
    }
}
