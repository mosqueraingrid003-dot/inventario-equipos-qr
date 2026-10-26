<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** Sin iniciar sesión, la página principal manda al login. */
    public function test_la_pagina_principal_redirige_al_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    /** La pantalla de login se muestra correctamente. */
    public function test_el_login_se_muestra(): void
    {
        $this->get('/login')->assertOk()->assertSee('Inventario TI');
    }
}
