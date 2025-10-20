<?php

namespace App\Providers;

use App\Http\Services\Repositories\BaseRepository;
use App\Http\Services\Repositories\Contracts\BaseContract;
use App\Http\Services\Repositories\Contracts\MenuContract;
use App\Http\Services\Repositories\Contracts\RoleContract;
use App\Http\Services\Repositories\Contracts\SettingContract;
use App\Http\Services\Repositories\Contracts\UserMenuContract;
use App\Http\Services\Repositories\Contracts\UsersContract;
use App\Http\Services\Repositories\Contracts\TodoContract;
use App\Http\Services\Repositories\Contracts\AttendanceContract;
use App\Http\Services\Repositories\MenuRepository;
use App\Http\Services\Repositories\RoleRepository;
use App\Http\Services\Repositories\SettingRepository;
use App\Http\Services\Repositories\UserMenuRepository;
use App\Http\Services\Repositories\UsersRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(BaseContract::class, BaseRepository::class);

        $this->app->bind(TodoContract::class, \App\Http\Services\Repositories\TodoRepository::class);
        $this->app->bind(AttendanceContract::class, \App\Http\Services\Repositories\AttendanceRepository::class);

        $this->app->bind(MenuContract::class, MenuRepository::class);
        $this->app->bind(RoleContract::class, RoleRepository::class);
        $this->app->bind(SettingContract::class, SettingRepository::class);
        $this->app->bind(UserMenuContract::class, UserMenuRepository::class);
        $this->app->bind(UsersContract::class, UsersRepository::class);
    }
}
