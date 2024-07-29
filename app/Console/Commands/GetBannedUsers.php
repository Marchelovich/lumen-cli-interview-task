<?php

namespace App\Console\Commands;

use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GetBannedUsers extends Command
{
    public const HEADERS = ['id', 'email', 'banned_at'];
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'banned-users:get
        {save-to? : Path to save file}
        {--active-users-only : Only get banned users who are not activated}
        {--with-trashed : Get all banned users including the trashed ones}
        {--trashed-only : Get only banned users who have been trashed}
        {--no-admin : Get only banned users who are not admins}
        {--admin-only : Get only banned users who are admins}
        {--sort-by=email : Sort by the email of the user}
        {--with-headers : Include headers in the output}';

    /**
     * The console command description.
     */
    protected $description = 'Get banned users';

    public function __construct(private UserRepository $repository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $users = $this->repository->getBannedUsers(
            $this->option('active-users-only'),
            $this->option('with-trashed'),
            $this->option('trashed-only'),
            $this->option('no-admin'),
            $this->option('admin-only'),
            $this->option('sort-by')
        );

        $output = [];

        foreach ($users as $user) {
            $output[] = [
                'id' => $user->id,
                'email' => $user->email,
                'banned_at' => $user->banned_at,
            ];
        }

        $this->table($this->option('with-headers') ? self::HEADERS : [], $output);

        if ($this->argument('save-to')) {
            $this->writeFile($output);
        }
    }

    /**
     * @param string[] $outout
     *
     * @return void
     * @throws \Exception
     */
    private function writeFile(array $output)
    {
        if (Storage::disk('local')->exists($this->argument('save-to'))) {
            $this->error("File {$this->argument('save-to')} already exists");
        }

        if ($this->option('with-headers')) {
            array_unshift($output, self::HEADERS);
        }

        Storage::disk('local')->put($this->argument('save-to'), implode(PHP_EOL, array_map(function ($row) {
            return implode(';', $row);
        }, $output)));
    }
}
