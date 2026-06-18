<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\User;
use Illuminate\Support\Facades\Auth;

Artisan::command('test:endpoints', function () {
    $adminUser = User::where('email', 'admin@yis.com')->first();
    $parentUser = User::where('email', 'parent@yis.com')->first();

    if (!$adminUser) {
        $this->error("ERROR: Test admin user admin@yis.com not found.");
        return 1;
    }
    if (!$parentUser) {
        $this->error("ERROR: Test parent user parent@yis.com not found.");
        return 1;
    }

    $tests = [
        [
            'user' => $adminUser,
            'role' => 'SCHOOL ADMIN',
            'urls' => [
                '/school/dashboard',
                '/school/cards/template-creator',
                '/school/cards/generate-card',
                '/school/diary/create',
                '/school/diary/report',
                '/school/events',
                '/school/certificates/template-creator',
                '/school/certificates/manage',
                '/school/certificates/class-wise',
                '/school/certificates/report',
            ]
        ],
        [
            'user' => $parentUser,
            'role' => 'PARENT / STUDENT',
            'urls' => [
                '/parent/dashboard',
                '/parent/diary',
                '/parent/events',
                '/parent/cards',
                '/parent/certificates',
                '/parent/documents',
                '/parent/attendance',
            ]
        ]
    ];

    $allOk = true;
    $kernel = app(Illuminate\Contracts\Http\Kernel::class);

    foreach ($tests as $testSuite) {
        $user = $testSuite['user'];
        $role = $testSuite['role'];
        $urls = $testSuite['urls'];

        $this->info("--------------------------------------------------------");
        $this->info("VERIFYING ENDPOINTS FOR $role");
        $this->info("--------------------------------------------------------");

        Auth::login($user);
        session()->start();

        foreach ($urls as $url) {
            $request = Illuminate\Http\Request::create($url, 'GET');
            $request->setLaravelSession(app('session')->driver());
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            try {
                $response = $kernel->handle($request);
                $status = $response->getStatusCode();
                
                if ($status === 200) {
                    $this->info("GET $url -> Status: $status [OK]");
                    
                    // Check for PRO/PREMIUM strings in specific badge classes or tags
                    $html = $response->getContent();
                    $badgePattern = '/badge-sidebar-(pro|prox|premium)/i';
                    if (preg_match($badgePattern, $html, $matches)) {
                        $this->warn("  [WARNING] Found sidebar badge class in HTML: " . $matches[0]);
                    }
                    if (preg_match('/>\s*(PRO|PREMIUM|PRO\+)\s*</i', $html, $matches)) {
                        $this->warn("  [WARNING] Found literal badge text in HTML: " . $matches[0]);
                    }
                } else {
                    $allOk = false;
                    $this->error("GET $url -> Status: $status [FAILED]");
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        $this->comment("  [Redirect] to: " . $response->getTargetUrl());
                    } else {
                        $this->comment("  [Error] Content snippet: " . substr(strip_tags($response->getContent()), 0, 200));
                    }
                }
            } catch (\Exception $e) {
                $allOk = false;
                $this->error("GET $url -> Exception: " . $e->getMessage());
            }
            $this->info("--------------------------------------------------------");
        }
    }

    if ($allOk) {
        $this->info("SUCCESS: All endpoints returned 200 OK and loaded without error!");
        return 0;
    } else {
        $this->error("FAILURE: One or more endpoints failed to load.");
        return 1;
    }
})->purpose('Test all school admin and parent dashboard endpoints');

