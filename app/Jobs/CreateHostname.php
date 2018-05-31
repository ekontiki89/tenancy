<?php

namespace App\Jobs;



use App\Notifications\TenantCreated;
use App\User;
use Hyn\Tenancy\Models\Customer;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Queue\TenantAwareJob;
use Hyn\Tenancy\Contracts\Repositories\CustomerRepository;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Hyn\Tenancy\Environment;
use Illuminate\Support\Facades\Hash;

class CreateHostname implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable,  SerializesModels;



    protected $email;
    protected $name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name, $email)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customer = new Customer;
        $customer->name = $this->name;
        $customer->email = $this->email;
        app(CustomerRepository::class)->create($customer);


        $website = new Website;
        $website->customer()->associate($customer);
        app(WebsiteRepository::class)->create($website);

        // associate the website with a hostname
        $hostname = new Hostname;
        $baseUrl = config('app.url_base');
        $hostname->fqdn = "{$this->name}.{$baseUrl}";
        $hostname->customer()->associate($customer);
        app(HostnameRepository::class)->attach($hostname, $website);

        app(Environment::class)->hostname($hostname);


        $password = str_random();

        $admin = User::create(['name' => $this->name, 'email' => $this->email, 'password' => Hash::make($password)]);
        $admin->guard_name = 'web';
        $admin->assignRole('admin');

        $admin->notify(new TenantCreated($hostname));




    }
}
