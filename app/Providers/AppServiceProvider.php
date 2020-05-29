<?php

namespace App\Providers;

use App\Services\CustomValidator;
use Illuminate\Support\ServiceProvider;
use Validator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        //aqui é onde registro, vinculo o repositorio com a interface
        $this->app->bind('App\Interfaces\PessoaRepositoryInterface', 'App\Repositories\PessoaRepository');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::resolver(function ($translator, $data, $rules, $messages = array(), $customAttributes = array()) {
            return new CustomValidator($translator, $data, $rules, $messages, $customAttributes);
        });
    }
}
