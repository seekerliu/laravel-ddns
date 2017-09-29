<?php

namespace Seekerliu\DynamicDns;

use Illuminate\Console\Command;

class DynamicDnsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dns:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize your ip to DNS server';

    private $dns;

    /**
     * Create a new command instance.
     *
     * @param Dns $dns
     */
    public function __construct(Dns $dns)
    {
        parent::__construct();
        $this->dns = $dns;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dns->run();
    }
}
